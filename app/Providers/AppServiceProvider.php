<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SettingsService;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{

    public function boot(): void {
        $settings = app(SettingsService::class)->get_all_settings();
        App::setLocale($settings['language'] ?? 'en');
    }
}
