@extends('emails.layout')

@section('mail-body')
<div class="es-wrapper-color">
    <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td class="esd-email-paddings" valign="top">
                    <!-- Header -->
                    <table class="es-header" cellspacing="0" cellpadding="0" align="center">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center">
                                    <table class="es-header-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" width="600" cellspacing="0" cellpadding="0" align="center">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p30t es-p30b es-p20r es-p20l" align="left">
                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="esd-container-frame" width="560" valign="top" align="center">
                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text">
                                                                                    <h1 style="color: #ffffff; font-size: 28px; font-weight: bold; margin: 0;">2Playerz.de</h1>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                    
                    <!-- Main Content -->
                    <table class="es-content" cellspacing="0" cellpadding="0" align="center">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center">
                                    <table class="es-content-body" style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);" width="600" cellspacing="0" cellpadding="0" align="center">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p30t es-p30r es-p30l" align="left">
                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="esd-container-frame" width="540" valign="top" align="center">
                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                        <tbody>
                                                                            <!-- Message Icon -->
                                                                            <!-- <tr>
                                                                                <td align="center" class="esd-block-text es-p20b">
                                                                                    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 80px; height: 80px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                                                        <span style="font-size: 32px; color: #ffffff;">💬</span>
                                                                                    </div>
                                                                                </td>
                                                                            </tr> -->
                                                                            
                                                                            <!-- Subject -->
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text es-p10t es-p10b">
                                                                                    <h2 style="color: #333333; font-size: 24px; font-weight: bold; margin: 0;">{{ $subject }}</h2>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <!-- Greeting -->
                                                                            <tr>
                                                                                <td align="left" class="esd-block-text es-p20t es-p20b">
                                                                                    <p style="color: #666666; font-size: 16px; margin: 0; line-height: 1.6;">{{ __('messages.other_lang.hello', ['name' => $notifiable->username ?? $notifiable->email]) }},</p>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <!-- Message Content -->
                                                                            <tr>
                                                                                <td align="left" class="esd-block-text es-p15t es-p15b">
                                                                                    <div style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                                                                        <p style="color: #333333; font-size: 16px; margin: 0; line-height: 1.6; font-weight: 500;">
                                                                                            @php
                                                                                                // Get sender name
                                                                                                $senderName = $sender->username ?? $sender->full_name ?? '';
                                                                                                if (empty($senderName) && isset($posts) && isset($posts->sender)) {
                                                                                                    $senderName = $posts->sender->username ?? $posts->sender->full_name ?? '';
                                                                                                }
                                                                                                
                                                                                                // Get message text - try multiple sources
                                                                                                // First try directly passed messageText variable (from controller)
                                                                                                $finalMessageText = $messageText ?? '';
                                                                                                
                                                                                                // If empty, try message object
                                                                                                if (empty($finalMessageText) && isset($message) && is_object($message)) {
                                                                                                    $finalMessageText = $message->message ?? '';
                                                                                                }
                                                                                                
                                                                                                // If still empty, try posts
                                                                                                if (empty($finalMessageText) && isset($posts) && isset($posts->message)) {
                                                                                                    $finalMessageText = $posts->message ?? '';
                                                                                                }
                                                                                            @endphp
                                                                                            {{ __('messages.other_lang.private_message_email_body', ['sender' => $senderName, 'message' => $finalMessageText]) }}
                                                                                        </p>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <!-- Reply Button -->
                                                                            <tr>
                                                                                <td align="center" class="esd-block-button es-p30t es-p30b">
                                                                                    <span class="es-button-border" style="border-radius: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                                                        <a href="{{ route('messages.show', ($message->conversation_id ?? $posts->conversation_id ?? '')) }}" class="es-button" target="_blank" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 25px; padding: 15px 30px; font-size: 16px; font-weight: bold; text-decoration: none; color: #ffffff; display: inline-block; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                                                                            🔗 {{ __('messages.other_lang.reply_message') }}
                                                                                        </a>
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                            <!-- Additional Info -->
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text es-p20t es-p20b">
                                                                                    <div style="background: #e3f2fd; border-radius: 8px; padding: 15px; margin: 20px 0;">
                                                                                        <p style="color: #1976d2; font-size: 14px; margin: 0; font-weight: 500;">
                                                                                            💡 {{ __('messages.other_lang.thanks_email_message') }}
                                                                                        </p>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                    
                    <!-- Footer -->
                    <table class="es-footer" cellspacing="0" cellpadding="0" align="center">
                        <tbody>
                            <tr>
                                <td class="esd-stripe" align="center">
                                    <table class="es-footer-body" style="background-color: #f8f9fa;" width="600" cellspacing="0" cellpadding="0" align="center">
                                        <tbody>
                                            <tr>
                                                <td class="esd-structure es-p20t es-p20r es-p20l" align="left">
                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="esd-container-frame" width="560" valign="top" align="center">
                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" class="esd-block-text">
                                                                                    <p style="color: #666666; font-size: 12px; margin: 0; line-height: 1.4;">
                                                                                    {{ config('app.name') }} - Dein Spielemagazin für PlayStation, Xbox & Nintendo.
                                                                                    </p>
                                                                                    <p style="color: #999999; font-size: 11px; margin: 5px 0 0 0;">
                                                                                        {{ __('messages.other_lang.email_sent_notification') }}
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection