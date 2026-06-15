<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFour();

        //2026.05.13 本番環境なら、URL生成をHTTPSに強制する
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        //2026.06.08 ユーザーログインログアウト時のLog追加
        Event::listen(Login::class, function (Login $event) {
            Log::info('User logged in', [
                'operator_id' => $event->user->id,
                'target_id'   => $event->user->id,
                'details'     => [
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            Log::warning('User login failed', [
                'operator_id' => null,
                'target_id'   => null,
                'details'     => [
                    'email'      => $event->credentials['email'] ?? 'unknown',
                    'ip'         => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                Log::info('User logged out', [
                    'operator_id' => $event->user->id,
                    'target_id'   => $event->user->id,
                    'details'     => [
                        'ip' => request()->ip(),
                    ]
                ]);
            }
        });
    }
}
