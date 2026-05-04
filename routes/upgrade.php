<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('upgrade/database', function () {
    if (config('app.upgrade_mode')) {
        Artisan::call('migrate', ['--force' => true]);
    }
});

Route::get('lang-js', function () {
    Artisan::call('lang:js');
});
