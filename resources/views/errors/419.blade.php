<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="title" content="{{ getAppName() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seite abgelaufen | {{(!empty(getSEOTools()->site_title)) ? getSEOTools()->site_title : getAppName()}}</title>
    <link rel="stylesheet" type="text/css" href="{{asset('front_web/scss/bootstrap.css')}}">
    <link href="{{asset('front_web/build/scss/dark-mode.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('front_web/build/scss/custom.css')}}" rel="stylesheet" type="text/css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .dark .error-container {
            background: #1a1a2e;
            color: #fff;
        }
        .oops-icon {
            font-size: 120px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
            line-height: 1;
        }
        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }
        .dark .error-title {
            color: #fff;
        }
        .error-message {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .dark .error-message {
            color: #ccc;
        }
        .btn-refresh {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        .btn-back {
            background: #f0f0f0;
            color: #333;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .dark .btn-back {
            background: #2a2a3e;
            color: #fff;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: #333;
            text-decoration: none;
        }
        .dark .btn-back:hover {
            color: #fff;
        }
        @media (max-width: 768px) {
            .error-container {
                padding: 40px 20px;
                margin: 20px;
            }
            .oops-icon {
                font-size: 80px;
            }
            .error-title {
                font-size: 24px;
            }
            .error-message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
<div class="error-container">
    <div class="oops-icon">🔒</div>
    <h1 class="error-title">Sitzung abgelaufen</h1>
    <p class="error-message">
        Ihre Sitzung ist abgelaufen. Bitte kehren Sie zur Startseite zurück und versuchen Sie es erneut.
    </p>
    <div>
        <a href="{{ url('/') }}" class="btn-refresh">Zur Startseite</a>
        <!-- <a href="javascript:window.history.go(-2)" class="btn-back">Zurück</a> -->
    </div>
</div>
</body>
</html>

