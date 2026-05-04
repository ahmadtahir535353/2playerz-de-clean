<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * use Illuminate\Support\Facades\Session;
     */
    public function handle(Request $request, Closure $next): Response
    {

        $langIdLanguage = empty(Session::get('frontLanguageChange'));


        if ($langIdLanguage) {
            $langId = getSettingValue()['front_language'];
            App::setLocale(Language::find($langId)->iso_code);
        } else {
            $langId = getSettingValue()['front_language'];
//            $langId = Session::get('frontLanguageChange');
            App::setLocale(Language::find($langId)->iso_code);
        }

//        dd(App::getLocale());

        return $next($request);
    }
}
