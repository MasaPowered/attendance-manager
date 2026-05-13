<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //2026.05.13 Renderなどの本番環境（HTTPS環境）なら、URL生成をHTTPSに強制する
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}
