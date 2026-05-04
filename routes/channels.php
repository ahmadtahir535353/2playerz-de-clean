<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Conversation channel authorization
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    // Check if user is part of this conversation
    return \App\Models\Conversation::where('id', $conversationId)
        ->where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->exists();
});

// Private conversation channel authorization (for private-conversation.{id})
Broadcast::channel('private-conversation.{conversationId}', function ($user, $conversationId) {
    \Log::info('Channel authorization check', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'user_exists' => $user ? 'yes' : 'no'
    ]);
    
    // Check if user is part of this conversation
    $conversation = \App\Models\Conversation::where('id', $conversationId)
        ->where(function($query) use ($user) {
            $query->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
        })
        ->first();
    
    \Log::info('Conversation check result', [
        'conversation_found' => $conversation ? 'yes' : 'no',
        'conversation_data' => $conversation ? $conversation->toArray() : null
    ]);
    
    return $conversation !== null;
});
