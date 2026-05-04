<?php

use App\Http\Middleware\SetLanguage;
use App\Http\Middleware\XSS;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('filament.auth.auth.login'));
        
        $middleware->append([
            \App\Http\Middleware\LastModifiedHeader::class,
        ]);
        
        
        $middleware->alias([
            'analytic' => \App\Http\Middleware\Analytics::class,
            'xss' => XSS::class,
            'setLanguage' => SetLanguage::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
