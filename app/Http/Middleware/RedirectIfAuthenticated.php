<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            //2026.05.11 Fortifyが強いならここはユーザには影響ないと思うからadminのHomeだけ表示させるでいいんじゃないか？
            /*if (Auth::guard($guard)->check()) {
                // ガードごとにリダイレクト先を分ける
                if ($guard === 'admin') {
                    return redirect('/report_list');
                }
                return redirect('/user_report_start_add');
            }*/
            if (Auth::guard($guard)->check()) {
                return redirect(route('home'));
            }
        }

        return $next($request);
    }
}
