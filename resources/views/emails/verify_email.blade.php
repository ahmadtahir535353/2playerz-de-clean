<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
</head>

<body style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif; margin: 0; background-color: #f4f4f4; color: #333;">
    <div style="max-width: 700px; margin: 0 auto; background: white; border-radius: 2px; overflow: hidden; margin-top: 10px;">
        <div style="background: linear-gradient(180deg, #720072, #000); padding: 10px !important; text-align: center;">
            <a href="{{ route('front.home') }}" style="text-decoration: none;">
                <img src="{{ getAppLogo() }}" alt="{{ getAppName() }}" style="max-height: 35px; display: block; margin: 0 auto;">
            </a>
        </div>

        <div style="padding: 30px !important;">
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 0; color: #333;">
                {{ __('messages.mails.hello') . '!' }}
            </p>
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 15px; color: #333;">
                {{ __('messages.mails.please_click') }}
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}" target="_blank" rel="noopener" style="background-color: #734D96; color: white; font-size: 16px; font-weight: 600; padding: 12px 30px; border-radius: 3px; text-decoration: none; display: inline-block;">
                    {{ __('messages.mails.verify_email') }}
                </a>
            </div>

            <p style="font-size: 16px; line-height: 1.5em; margin-top: 20px; color: #333;">
                {{ __('messages.mails.action_required') }}
            </p>
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 15px; color: #333;">
                {{ __('messages.mails.regard') }}<br>
                {{ config('app.name') }}
            </p>

            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;">
                <tr>
                    <td>
                        <p style="font-size: 14px; line-height: 1.5em; margin-top: 0; color: #333;">
                            {{ __('messages.mails.trouble', [
                                'actionText' => Lang::get('messages.mails.verify_email'),
                            ]) }}
                            <a href="{{ $url }}" style="color: #3869d4; word-break: break-all; text-decoration: none;">{{ $url }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <footer style="background-color: #1a1d21; color: #ffffffab; font-size: 13px; text-align: center; padding: 30px 20px; width: 100% !important;">
            <div style="margin: 25px auto;">
                <img src="{{ getAppLogo() }}" alt="{{ getAppName() }}" style="max-height: 40px; display: block; margin: 0 auto;">
            </div>

            <p style="max-width: 500px; margin: 0 auto; line-height: 1.6; color: #ffffffab;">
                <strong style="color: #ffffffab;">{{ __('messages.other_lang.email_footer.company_description') }}</strong><br>
                {{ __('messages.other_lang.email_footer.business_management') }}<br>
                {{ __('messages.other_lang.email_footer.address_line1') }}<br>
                {{ __('messages.other_lang.email_footer.contact_email') }} <a href="mailto:{{ __('messages.other_lang.email_footer.contact_email_address') }}" style="color: #ffffffab; text-decoration: none;">{{ __('messages.other_lang.email_footer.contact_email_address') }}</a>
            </p>

            <div style="margin-top: 15px;">
                <a href="https://2playerz.de/support" target="_blank" style="color: #ffffffab; text-decoration: none; margin: 0 8px;">{{ __('messages.other_lang.email_footer.impressum') }}</a> ·
                <a href="https://2playerz.de/privacy" style="color: #ffffffab; text-decoration: none; margin: 0 8px;">{{ __('messages.other_lang.email_footer.privacy') }}</a> ·
                <a href="https://2playerz.de/terms-conditions" style="color: #ffffffab; text-decoration: none; margin: 0 8px;">{{ __('messages.other_lang.email_footer.terms_conditions') }}</a> ·
                <a href="https://2playerz.de" target="_blank" style="color: #ffffffab; text-decoration: none; margin: 0 8px;">{{ __('messages.other_lang.email_footer.website') }}</a>
            </div>

            <p style="margin-top: 20px; font-size: 12px; color: #ffffffab;">
                &copy; {{ date('Y') }} {{ getAppName() }}. {{ __('messages.other_lang.email_footer.copyright') }}
            </p>
        </footer>
    </div>
</body>

</html>
