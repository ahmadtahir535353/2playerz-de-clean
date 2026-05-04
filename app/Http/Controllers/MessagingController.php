<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\UserBlock;
use App\Notifications\PrivateMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagingController extends Controller
{
    /**
     * Display the messages list page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get only the first 10 conversations for initial load
        // Ordered by latest message first
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->whereNotNull('last_message_id') // Only show conversations with actual messages
            ->with(['user1', 'user2', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();

        return view('customer-panel.messages.index', compact('conversations'));
    }
    
    /**
     * Load older conversations for infinite scroll
     */
    public function loadOlderConversations(Request $request)
    {
        $user = Auth::user();
        $oldestConversationDate = $request->get('oldest_conversation_date');
        
        // Get next 10 conversations older than the oldest one currently loaded
        $conversations = Conversation::where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->whereNotNull('last_message_id')
            ->where('last_message_at', '<', $oldestConversationDate)
            ->with(['user1', 'user2', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->limit(10)
            ->get();
        
        // Render the conversations HTML
        $html = view('customer-panel.partials.conversation-items', compact('conversations'))->render();
        
        return response()->json([
            'html' => $html,
            'hasMore' => $conversations->count() === 10, // If we got 10, there might be more
            'oldestDate' => $conversations->last()?->last_message_at ?? null
        ]);
    }

    /**
     * Display a specific conversation
     */
    public function show($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->with(['user1', 'user2'])
            ->firstOrFail();

        $otherUser = $conversation->getOtherUser($user->id);
        $isBlocked = UserBlock::isBlockedBetween($user->id, $otherUser->id);

        // Get only the latest 30 messages for initial load
        $messages = Message::where('conversation_id', $conversationId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->reverse()
            ->values();

        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return view('customer-panel.messages.show', compact('conversation', 'messages', 'otherUser', 'isBlocked'));
    }
    
    /**
     * Load older messages for infinite scroll
     */
    public function loadOlderMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $oldestMessageId = $request->get('oldest_message_id');
        
        // Verify user has access to this conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Get older messages before the oldest message ID
        $messages = Message::where('conversation_id', $conversationId)
            ->where('id', '<', $oldestMessageId)
            ->with(['sender', 'recipient'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

        // Format messages for response
        $formattedMessages = $messages->map(function($message) use ($user) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'recipient_id' => $message->recipient_id,
                'created_at' => $message->created_at->format('d.m.Y, H:i'),
                'is_edited' => $message->is_edited,
                'is_deleted_by_sender' => $message->is_deleted_by_sender,
                'is_deleted_by_recipient' => $message->is_deleted_by_recipient,
                'sender' => [
                    'id' => $message->sender->id,
                    'username' => $message->sender->username,
                    'profile_image' => $message->sender->profile_image,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
            'has_more' => $messages->count() === 20
        ]);
    }

    /**
     * Send a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $recipientId = $request->recipient_id;

        // Don't allow sending messages to yourself
        if ($user->id == $recipientId) {
            return response()->json(['error' => 'Cannot send message to yourself'], 400);
        }

        // Block: no messaging in either direction
        if (UserBlock::isBlockedBetween($user->id, $recipientId)) {
            return response()->json(['error' => __('messages.block.cannot_message_blocked')], 403);
        }

        // Check if recipient allows messages from this user
        $recipient = User::findOrFail($recipientId);
        if (!$recipient->canReceiveMessagesFrom($user)) {
            return response()->json(['error' => 'This user does not allow messages from you'], 403);
        }

        DB::beginTransaction();
        try {
            // Get or create conversation
            $conversation = Conversation::getOrCreateConversation($user->id, $recipientId);

            // Create message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'recipient_id' => $recipientId,
                'message' => $request->message,
            ]);

            // Update conversation
            $conversation->update([
                'last_message_at' => now(),
                'last_message_id' => $message->id,
            ]);

            // Load the message with sender information before broadcasting
            $message->load('sender');

            // Send notification to recipient using Laravel notification system
            try {
                // Setup mail config dynamically (same as newsletter system) BEFORE sending notification
                $mailData = \App\Models\MailSetting::first();
                if ($mailData) {
                    $protocol = \App\Models\MailSetting::TYPE[$mailData->mail_protocol];
                    $host = $mailData->mail_host;

                    if ($mailData->mail_protocol == \App\Models\MailSetting::MAIL_LOG) {
                        $protocol = 'log';
                        $host = 'mailhog';
                    }
                    if ($mailData->mail_protocol == \App\Models\MailSetting::SMTP) {
                        $protocol = 'smtp';
                    }
                    if ($mailData->mail_protocol == \App\Models\MailSetting::SENDGRID) {
                        $protocol = 'sendgrid';
                    }

                    config([
                        'mail.default' => $protocol,
                        "mail.mailers.$protocol.transport" => $protocol,
                        "mail.mailers.$protocol.host" => $host,
                        "mail.mailers.$protocol.port" => $mailData->mail_port,
                        "mail.mailers.$protocol.encryption" => \App\Models\MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                        "mail.mailers.$protocol.username" => $mailData->mail_username,
                        "mail.mailers.$protocol.password" => $mailData->mail_password,
                        'mail.from.address' => $mailData->reply_to,
                        'mail.from.name' => $mailData->mail_title,
                    ]);
                }

                // Check if notification already exists to prevent duplicates
                $exists = DB::table('notifications')
                    ->where('to_user_id', $recipientId)
                    ->where('from_user_id', $user->id)
                    ->where('type', 'App\\Notifications\\PrivateMessageNotification')
                    ->whereJsonContains('data', ['message_id' => $message->id])
                    ->exists();

                if (!$exists) {
                    // Insert into custom notifications table for compatibility
                    DB::table('notifications')->insert([
                        'type' => 'App\\Notifications\\PrivateMessageNotification',
                        'notifiable_type' => 'App\\Models\\User',
                        'notifiable_id' => $recipientId,
                        'to_user_id' => $recipientId,
                        'from_user_id' => $user->id,
                        'post_id' => null,
                        'data' => json_encode([
                            'message' => __('messages.other_lang.private_message_notification', ['sender' => $user->username]) . ': "' . \Str::limit($message->message, 50) . '"',
                            'message_id' => $message->id,
                            'conversation_id' => $conversation->id,
                            'sender_name' => $user->full_name,
                            'sender_username' => $user->username,
                            'sender_profile_image' => $user->profile_image,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    \Log::info('Private message notification sent', [
                        'message_id' => $message->id, 
                        'recipient_id' => $recipientId,
                        'preference' => $recipient->getMessageNotificationPreference(),
                        'email' => $recipient->email
                    ]);

                    // Send email notification in background if user prefers email + notification
                    if ($recipient->getMessageNotificationPreference() === 'email_and_notification') {
                        // Dispatch email sending to background to avoid blocking
                        dispatch(function () use ($user, $message, $recipient, $mailData) {
                            try {
                                // Setup mail config in background job
                                if ($mailData) {
                                    $protocol = \App\Models\MailSetting::TYPE[$mailData->mail_protocol];
                                    $host = $mailData->mail_host;

                                    if ($mailData->mail_protocol == \App\Models\MailSetting::MAIL_LOG) {
                                        $protocol = 'log';
                                        $host = 'mailhog';
                                    }
                                    if ($mailData->mail_protocol == \App\Models\MailSetting::SMTP) {
                                        $protocol = 'smtp';
                                    }
                                    if ($mailData->mail_protocol == \App\Models\MailSetting::SENDGRID) {
                                        $protocol = 'sendgrid';
                                    }

                                    config([
                                        'mail.default' => $protocol,
                                        "mail.mailers.$protocol.transport" => $protocol,
                                        "mail.mailers.$protocol.host" => $host,
                                        "mail.mailers.$protocol.port" => $mailData->mail_port,
                                        "mail.mailers.$protocol.encryption" => \App\Models\MailSetting::ENCRYPTION_TYPE[$mailData->encryption],
                                        "mail.mailers.$protocol.username" => $mailData->mail_username,
                                        "mail.mailers.$protocol.password" => $mailData->mail_password,
                                        'mail.from.address' => $mailData->reply_to,
                                        'mail.from.name' => $mailData->mail_title,
                                    ]);
                                }

                                // Send email
                                \Illuminate\Support\Facades\Mail::send('emails.private_message', [
                                    'sender' => $user,
                                    'message' => $message,
                                    'notifiable' => $recipient,
                                    'subject' => __('messages.other_lang.private_message_subject', ['sender' => $user->username ?? $user->full_name]),
                                    'messageText' => $message->message ?? ''
                                ], function ($mail) use ($recipient, $user) {
                                    $mail->to($recipient->email)
                                        ->subject(__('messages.other_lang.private_message_subject', ['sender' => $user->username ?? $user->full_name]));
                                });

                                \Log::info('Private message email sent in background', [
                                    'message_id' => $message->id,
                                    'recipient_id' => $recipient->id,
                                    'email' => $recipient->email
                                ]);
                            } catch (\Exception $e) {
                                \Log::error('Private message email failed (background)', [
                                    'error' => $e->getMessage(),
                                    'message_id' => $message->id,
                                    'email' => $recipient->email
                                ]);
                            }
                        })->afterResponse();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Private message notification failed', [
                    'error' => $e->getMessage(), 
                    'message_id' => $message->id,
                    'recipient_id' => $recipientId,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Broadcast the message
            try {
                broadcast(new MessageSent($message));
                \Log::info('MessageSent broadcast successful', ['message_id' => $message->id]);
            } catch (\Exception $e) {
                \Log::error('MessageSent broadcast failed', ['error' => $e->getMessage(), 'message_id' => $message->id]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'recipient_id' => $message->recipient_id,
                    'created_at' => $message->created_at->format('d.m.Y, H:i'),
                    'is_edited' => $message->is_edited,
                    'sender' => [
                        'id' => $message->sender->id,
                        'username' => $message->sender->username,
                        'profile_image' => $message->sender->profile_image,
                    ],
                    'recipient' => [
                        'id' => $message->recipient->id,
                        'username' => $message->recipient->username,
                        'profile_image' => $message->recipient->profile_image,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }

    /**
     * Edit a message
     */
    public function update(Request $request, $messageId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        
        $message = Message::where('id', $messageId)
            ->where('sender_id', $user->id)
            ->firstOrFail();

        $message->update([
            'message' => $request->message,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        // Load the message with sender information before broadcasting
        $message->load('sender');

        // Broadcast the updated message
        try {
            broadcast(new \App\Events\MessageUpdated($message));
            \Log::info('MessageUpdated broadcast successful', ['message_id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('MessageUpdated broadcast failed', ['error' => $e->getMessage(), 'message_id' => $message->id]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'recipient_id' => $message->recipient_id,
                'created_at' => $message->created_at->format('d.m.Y, H:i'),
                'is_edited' => $message->is_edited,
                'edited_at' => $message->edited_at ? $message->edited_at->format('d.m.Y, H:i') : null,
                'is_deleted_by_sender' => $message->is_deleted_by_sender,
                'is_deleted_by_recipient' => $message->is_deleted_by_recipient,
                'sender' => [
                    'id' => $message->sender->id,
                    'username' => $message->sender->username,
                ]
            ]
        ]);
    }

    /**
     * Delete a message (soft delete)
     */
    public function destroy($messageId)
    {
        $user = Auth::user();
        
        $message = Message::where('id', $messageId)
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('recipient_id', $user->id);
            })
            ->firstOrFail();

        // Soft delete: Mark message as deleted by sender or recipient
        if ($message->sender_id == $user->id) {
            $message->update(['is_deleted_by_sender' => true]);
        } else {
            $message->update(['is_deleted_by_recipient' => true]);
        }

        // Broadcast the message deletion
        try {
            broadcast(new \App\Events\MessageDeleted($messageId, $message->conversation_id));
            \Log::info('MessageDeleted broadcast successful', ['message_id' => $messageId]);
        } catch (\Exception $e) {
            \Log::error('MessageDeleted broadcast failed', ['error' => $e->getMessage(), 'message_id' => $messageId]);
        }

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'is_deleted_by_sender' => $message->is_deleted_by_sender,
                'is_deleted_by_recipient' => $message->is_deleted_by_recipient,
            ]
        ]);
    }

    /**
     * Delete entire conversation and all its messages permanently
     */
    public function deleteConversation($conversationId)
    {
        $user = Auth::user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->firstOrFail();

        // Permanently delete all messages in this conversation
        Message::where('conversation_id', $conversationId)->delete();
        
        // Permanently delete the conversation
        $conversation->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get conversation with a specific user
     */
    public function getConversationWithUser($userId)
    {
        $user = Auth::user();
        
        if ($user->id == $userId) {
            return redirect()->back()->with('error', __('messages.block.cannot_message_self'));
        }

        if (UserBlock::isBlockedBetween($user->id, $userId)) {
            return redirect()->back()->with('error', __('messages.block.cannot_message_blocked'));
        }

        $conversation = Conversation::getOrCreateConversation($user->id, $userId);
        
        return redirect()->route('messages.show', $conversation->id);
    }

    /**
     * Check for new messages in a conversation
     */
    public function checkNewMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $lastMessageId = $request->get('last_message_id', 0);
        
        // Verify user has access to this conversation
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user1_id', $user->id)
                      ->orWhere('user2_id', $user->id);
            })
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        // Get new messages since the last message ID
        $messages = Message::where('conversation_id', $conversationId)
            ->where('id', '>', $lastMessageId)
            ->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->where('is_deleted_by_sender', false)
                      ->orWhere(function($q) use ($user) {
                          $q->where('recipient_id', $user->id)
                            ->where('is_deleted_by_recipient', false);
                      });
            })
            ->with(['sender'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        $formattedMessages = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'recipient_id' => $message->recipient_id,
                'created_at' => $message->created_at->format('d.m.Y, H:i'),
                'is_edited' => $message->is_edited,
                'sender' => [
                    'id' => $message->sender->id,
                    'username' => $message->sender->username,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages
        ]);
    }
}
