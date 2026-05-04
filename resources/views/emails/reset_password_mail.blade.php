<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
        }

        .header {
            background: linear-gradient(180deg, #720072, #000);
            padding: 10px;
            text-align: center;
        }

        .header img {
            max-height: 35px;
        }

        .content {
            padding: 30px;
        }

        .button {
            text-align: center;
            margin: 30px 0;
        }

        .button a {
            background-color: #734D96;
            color: white;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
        }

        .footer {
            background-color: #1a1d21;
            color: #ffffffab;
            font-size: 13px;
            text-align: center;
            padding: 30px 20px;
        }

        .footer a {
            color: #ffffffab;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .footer-logo img {
            max-height: 40px;
            margin: 25px auto;
        }

        .footer-address {
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .footer-links {
            margin-top: 15px;
        }

        .footer-links a {
            margin: 0 8px;
        }

        @media (max-width: 600px) {
            .email-container {
                margin-top: 0px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <a href="{{ route('front.home') }}">
                <img src="{{ getAppLogo() }}" alt="{{ getAppName() }}" style="max-height: 35px;">
            </a>
        </div>

        <div class="content">
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 0;">
                {{ __('messages.mails.hello') . '!' }}
            </p>
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 15px;">
                {{ __('messages.mails.password_reset_request') }}
            </p>

            <div class="button">
                <a href="{{ $url }}" target="_blank" rel="noopener">
                    {{ __('messages.reset') . ' ' . __('messages.staff.password') }}
                </a>
            </div>

            <p style="font-size: 16px; line-height: 1.5em; margin-top: 20px;">
                {{ __('messages.mails.this_password_reset_link_will_expire_in_count_minutes', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]) }}
                {{ __('messages.mails.no_further_action_is_required') }}
            </p>
            <p style="font-size: 16px; line-height: 1.5em; margin-top: 15px;">
                {{ __('messages.mails.regards') }}<br>
                {{ config('app.name') }}
            </p>

            <table class="subcopy" width="100%" cellpadding="0" cellspacing="0" role="presentation"
                style="border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;">
                <tr>
                    <td>
                        <p style="font-size: 14px; line-height: 1.5em; margin-top: 0; word-break: break-all;">
                            <a href="{{ $url }}" style="color: #3869d4;">{{ $url }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <footer class="footer">
            <div class="footer-logo">
                <img src="{{ getAppLogo() }}" alt="{{ getAppName() }}">
            </div>

            <p class="footer-address">
                <strong>{{ __('messages.other_lang.email_footer.company_description') }}</strong><br>
                {{ __('messages.other_lang.email_footer.business_management') }}<br>
                {{ __('messages.other_lang.email_footer.address_line1') }}<br>
                {{ __('messages.other_lang.email_footer.contact_email') }} <a href="mailto:{{ __('messages.other_lang.email_footer.contact_email_address') }}">{{ __('messages.other_lang.email_footer.contact_email_address') }}</a>
            </p>

            <div class="footer-links">
                <a href="https://2playerz.de/support" target="_blank">{{ __('messages.other_lang.email_footer.impressum') }}</a> ·
                <a href="https://2playerz.de/privacy">{{ __('messages.other_lang.email_footer.privacy') }}</a> ·
                <a href="https://2playerz.de/terms-conditions">{{ __('messages.other_lang.email_footer.terms_conditions') }}</a> ·
                <a href="https://2playerz.de" target="_blank">{{ __('messages.other_lang.email_footer.website') }}</a>
            </div>

            <p style="margin-top: 20px; font-size: 12px;">
                &copy; {{ date('Y') }} {{ getAppName() }}. {{ __('messages.other_lang.email_footer.copyright') }}
            </p>
        </footer>
    </div>
</body>

</html>
