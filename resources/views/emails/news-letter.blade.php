<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>2Playerz Newsletter - Gaming News</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .newsletter {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
        }

        .header {
            /* background-color: #734E96; */
            background: linear-gradient(180deg, #720072, #000);
            padding: 10px;
            text-align: center;
        }

        .header img {
            max-height: 35px;
        }

        .headline {
            color: #5b3f95;
            font-size: 20px;
            text-align: center;
            margin: 0px 30px;
            padding: 30px;
            font-weight: bold;
            border-bottom: 2px solid;
        }
        
        .news-item {
            display: flex;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .news-item:first-of-type {
            border-top: none;
        }
        
        .news-image {
            flex: 0 0 200px;
            width: 200px;
            margin-right: 15px;
        }

        .news-image img {
            width: 200px;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
        }

        .news-content {
            flex: 1;
            position: relative;
        }

        .badge {
            display: inline-block;
            font-size: 12px;
            background-color: #0f9d58;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .badge-mobile {
            display: none !important;
        }
        
        .badge-desktop {
            display: inline-block;
        }

        .badge.news {
            background-color: #673ab7;
        }

        .news-title {
            font-size: 16px;
            font-weight: 500;
        }

        .news-summary {
            font-size: 14px;
            opacity: 90%;
            margin-top: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .button {
            text-align: center;
        }

        .button button {
            background-color: #734D96;
            color: white;
            font-size: 20px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 3px;
            border: none;
            margin: 20px auto;
            cursor: pointer;

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
            .newsletter {
                margin-top: 0px;
            }

            .news-item {
                display: block;
                padding: 15px;
            }

            .news-image {
                width: 100%;
                margin-bottom: 15px;
                flex: none;
            }

            .news-image img {
                width: 100%;
                height: auto;
                max-height: 200px;
            }

            .news-content {
                margin-left: 0;
                padding-left: 0;
            }

            .badge-mobile {
                display: inline-block !important;
            }

            .badge-desktop {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="newsletter">
        <div class="header">
            <img src="https://2playerz.de/uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png" class="logo"
                style="max-height: 35px; display: block; margin: 0 auto;"
                alt="2Playerz Logo"
                width="120"
                height="35">
        </div>

        <div class="headline">{{ __('messages.other_lang.news_letter.headline') ?? 'News des Tages' }}</div>

        @if(isset($posts) && $posts->count() > 0)
        @foreach ($posts as $post)
            @php
                $postUrl = url(route('detailPage', $post->slug ?? '', false));
                $firstVideo = $post->postMultipleVideos->first(); 
                $imageUrl = null;
                
                if ($firstVideo) {
                    $embed_url = $firstVideo->video_embed_code ?? '';
                    preg_match('/(?:youtube\.com\/embed\/|youtu\.be\/)([^\?&"]+)/', $embed_url, $matches);
                    $youtube_id = $matches[1] ?? null;
                    if ($youtube_id) {
                        $imageUrl = 'https://img.youtube.com/vi/' . $youtube_id . '/hqdefault.jpg';
                    } elseif(!empty($post->post_image)) {
                        $imageUrl = url(str_replace(' ', '%20', $post->post_image));
                    }
                } elseif(!empty($post->post_image)) {
                    $imageUrl = url(str_replace(' ', '%20', $post->post_image));
                }
            @endphp
            
            <div class="news-item">
                @if($imageUrl)
                    <div class="news-image">
                        <img src="{{ $imageUrl }}" alt="{{ $post->title ?? 'News' }}" style="display: block; width: 200px; height: 120px; object-fit: cover; border: 0; border-radius: 4px;">
                    </div>
                @endif
                <div class="news-content" style="padding-left: 10px;">
                    @if(isset($post->category))
                        <div class="badge badge-mobile {{ function_exists('getColorClass') ? getColorClass($post->category->id ?? 0) : '' }}" style="background-color: {{ $post->category->color ?? '#B051B0' }}; color: white; font-size: 12px; padding: 2px 8px; border-radius: 4px; margin-bottom: 5px; display: inline-block;">{{ $post->category->name ?? 'News' }}</div>
                    @endif
                    <h3 class="news-title" style="margin: 0 0 8px 0; font-size: 16px; font-weight: 500; line-height: 1.4;">
                        <a href="{{ $postUrl }}" style="text-decoration: none; color: #222;">
                            {{ $post->title ?? __('messages.customer_profile.un_titled') }}
                        </a>
                    </h3>

                    @if(!empty($post->description))
                        <div class="news-summary" style="color: #666; font-size: 14px; line-height: 1.5; margin-top: 5px;">
                            {{ Str::limit(strip_tags($post->description ?? ''), 200) }}
                        </div>
                    @endif
                    <!-- <div style="margin-top: 10px;">-->
                    <!--    <a href="{{ $postUrl }}" style="display: inline-block; background-color: #734D96; color: white; text-decoration: none; font-size: 14px; font-weight: 500; padding: 8px 16px; border-radius: 4px;">Artikel lesen</a>-->
                    <!--</div>-->
                    @if(isset($post->category))
                        <div class="badge badge-desktop {{ function_exists('getColorClass') ? getColorClass($post->category->id ?? 0) : '' }}" style="background-color: {{ $post->category->color ?? '#B051B0' }}; color: white; font-size: 12px; padding: 2px 8px; border-radius: 4px; margin-top: 5px; display: inline-block;">{{ $post->category->name ?? 'News' }}</div>
                    @endif
                </div>
            </div>
        @endforeach
        @else
            <div class="news-item" style="padding: 30px 20px; text-align: center;">
                <p style="color: #666; font-size: 16px; margin: 0;">{{ __('messages.customer_profile.empty_message') ?? 'Keine neuen Artikel heute.' }}</p>
            </div>
        @endif
        
        <div class="button">
            <a href="https://2playerz.de" style="text-decoration: none; display: inline-block; background-color: #734D96; color: white; font-size: 20px; font-weight: 600; padding: 10px 20px; border-radius: 3px; margin: 20px auto;">
                Zu 2Playerz.de
            </a>
        </div>

        <footer class="footer">
            <p style="margin: 0 0 20px 0; line-height: 1.8;">
                {!! __('messages.other_lang.news_letter.auto_generated_message', ['email' => '<strong>' . ($email ?? '') . '</strong>']) !!}
                {{ __('messages.other_lang.news_letter.contact_question') ?? 'Bei Rückfragen kannst du einfach auf diese E-Mail antworten oder unser' }}
                <a href="https://2playerz.de/contact" style="color: #ffffffab; text-decoration: underline;">{{ __('messages.other_lang.news_letter.contact_form_link') ?? 'Kontaktformular' }}</a> {{ __('messages.other_lang.news_letter.contact_question_use') ?? 'verwenden' }}.
            </p>

            <p style="margin: 0 0 25px 0; line-height: 1.8;">
                {{ __('messages.other_lang.news_letter.unsubscribe_question') ?? 'Wenn du diesen Newsletter nicht mehr erhalten möchtest, klicke bitte auf' }}
                <a href="{{ $unsubscribeUrl ?? url('/newsletter/unsubscribe?email=' . urlencode($email ?? '')) }}" style="color: #ffffffab; text-decoration: underline; font-weight: 600;">{{ __('messages.other_lang.news_letter.unsubscribe_link') ?? 'abbestellen' }}</a>.
            </p>

            <div class="footer-logo" style="margin: 25px 0;">
                <img src="https://2playerz.de/uploads/logo/504/01JTHQA9R70QTFSFVR2SA469E3-old.png" alt="2Playerz Logo" style="max-height: 40px; display: block; margin: 0 auto;">
            </div>

            <p class="footer-address" style="line-height: 1.8;">
                <strong>{{ __('messages.other_lang.news_letter.company_description') }}</strong><br>
                {{ __('messages.other_lang.news_letter.business_management') }}<br>
                {{ __('messages.other_lang.news_letter.address_line1') }}<br>
                {{ __('messages.other_lang.news_letter.contact_email') }} <a href="mailto:{{ __('messages.other_lang.news_letter.contact_email_address') }}" style="color: #ffffffab; text-decoration: underline;">{{ __('messages.other_lang.news_letter.contact_email_address') }}</a>
            </p>

            <div class="footer-links" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="https://2playerz.de/support" style="color: #ffffffab; text-decoration: underline; margin: 0 5px;">{{ __('messages.other_lang.news_letter.impressum') }}</a>
                <span style="color: #ffffffab;">&nbsp;|&nbsp;</span>
                <a href="https://2playerz.de/privacy" style="color: #ffffffab; text-decoration: underline; margin: 0 5px;">{{ __('messages.other_lang.news_letter.privacy') }}</a>
                <span style="color: #ffffffab;">&nbsp;|&nbsp;</span>
                <a href="https://2playerz.de/terms-conditions" style="color: #ffffffab; text-decoration: underline; margin: 0 5px;">{{ __('messages.other_lang.news_letter.terms_conditions') }}</a>
            </div>
        </footer>

    </div>

</body>

</html>