<?php

use App\Http\Middleware\LoginCheckMiddleware;
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
        $middleware->alias([
            'Login_check' => LoginCheckMiddleware::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class, //2026/05/11 sasaki
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            // URLの先頭が 'admin' で始まっていれば、管理者のログイン画面へ
            if ($request->is('admin/*') || $request->is('admin')) {
                return '/admin/login';
            }
            
            // それ以外はユーザーのログイン画面へ
            return '/login';
        });  //2026/05/11 sasaki ログインしてなかった場合の遷移先
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
