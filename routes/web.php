<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WorkReportController;
use App\Http\Controllers\ShiftController;
use App\Http\Middleware;
use App\Http\Middleware\LoginCheckMiddleware;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

//2026.05.11 ユーザーだけログアウトするように修正
// ユーザー用ログアウトの上書き
Route::post('/logout', function (Request $request) {
    // 1. webガード（ユーザー）のみログアウト
    Auth::guard('web')->logout();

    return redirect('/login');
})->name('logout');

//2026.05.11 Fortify化のため削除
//Route::get('user_login', [UserReportController::class, 'login']);

//Route::post('user_login', [UserReportController::class, 'post_login']);

//Route::get('user_logout', [UserReportController::class, 'logout']);

//Auth::routes();

//Route::get('login', [AdminController::class, 'login']);

//Route::post('login', [AdminController::class, 'post_login']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// --- ユーザー用（Fortify）のグループ ---
Route::middleware('auth:web')->group(function () {
    Route::get('user_report_start_add', [UserReportController::class, 'report_start'])->name('user_report_start');

    Route::post('user_report_start_add', [UserReportController::class, 'post_report_start']);

    Route::get('user_report_end_add', [UserReportController::class, 'report_end']);

    Route::post('user_report_end_add', [UserReportController::class, 'post_report_end']);
});


// --- 管理者用（Laravel UI / adminガード）のグループ ---
Route::prefix('admin')->name('admin.')->group(function () {
    // ログインしていない時だけアクセスできるルート（guest:admin）
    //2026.05.11　これあるとループが起きる。
    //Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'login'])->name('login');
        Route::post('login', [AdminController::class, 'post_login'])->name('login.post');
        Route::get('admin_logout', [AdminController::class, 'logout']);
    //});

    Route::middleware('auth:admin')->group(function () {
        //2026.05.11 middleware(['Login_check'])が何のためにあるんだろう？
        //Route::get('user_list', [UserController::class, 'user_list'])->middleware(['Login_check']);
        Route::get('user_list', [UserController::class, 'user_list']);

        Route::post('user_edit', [UserController::class, 'user_edit']);

        Route::post('user_edit_done', [UserController::class, 'user_edit_done']);

        Route::get('user_add', [UserController::class, 'add']);

        Route::post('user_add_check', [UserController::class, 'add_check']);

        Route::post('user_add_done', [UserController::class, 'create']);

        Route::get('user_delete', [UserController::class, 'delete']);

        Route::post('user_delete_check', [UserController::class, 'delete_check']);

        Route::post('user_delete_done', [UserController::class, 'delete_done']);

        Route::get('user_logintime_set', [UserController::class, 'logintime_set']);

        Route::post('user_logintime_set', [UserController::class, 'post_logintime_set']);

        //2024/06/22
        Route::get('admin_list', [AdminController::class, 'list']);

        Route::post('admin_edit', [AdminController::class, 'edit']);

        Route::post('admin_edit_done', [AdminController::class, 'edit_done']);

        Route::get('admin_add', [AdminController::class, 'add']);

        Route::post('admin_add_check', [AdminController::class, 'add_check']);

        Route::post('admin_add_done', [AdminController::class, 'create']);

        Route::get('admin_delete', [AdminController::class, 'delete']);

        Route::post('admin_delete_check', [AdminController::class, 'delete_check']);

        Route::post('admin_delete_done', [AdminController::class, 'delete_done']);

        Route::get('report_list', [WorkReportController::class, 'list'])->name('report_list');;

        Route::post('report_list', [WorkReportController::class, 'post_list']);

        Route::post('report_edit', [WorkReportController::class, 'edit']);

        Route::post('report_edit_done', [WorkReportController::class, 'edit_done']);

        Route::get('report_delete', [WorkReportController::class, 'delete']);

        Route::post('report_delete', [WorkReportController::class, 'post_delete']);

        Route::post('report_delete_check', [WorkReportController::class, 'delete_check']);

        Route::post('report_delete_done', [WorkReportController::class, 'delete_done']);

        Route::get('report_list_download', [WorkReportController::class, 'download']);

        Route::post('report_list_download', [WorkReportController::class, 'post_download']);

        Route::post('report_list_download_done', [WorkReportController::class, 'download_done']);

        Route::get('shift_edit', [ShiftController::class, 'edit']);

        Route::post('shift_edit', [ShiftController::class, 'post_edit']);

        Route::get('shift_month_delete', [ShiftController::class, 'delete']);

        Route::post('shift_month_delete', [ShiftController::class, 'post_delete']);

        Route::post('shift_month_delete_check', [ShiftController::class, 'delete_check']);

        Route::post('shift_month_delete_done', [ShiftController::class, 'delete_done']);

        Route::get('shift_import', [ShiftController::class, 'import']);

        Route::post('shift_import_done', [ShiftController::class, 'import_done']);

    });
});

