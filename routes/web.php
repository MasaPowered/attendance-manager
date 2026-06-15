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
use App\Http\Controllers\AdminPasswordController;
use App\Http\Controllers\UserPasswordController;




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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//2026.05.21 後でリソースコントローラに置き換えるからURLとルート名はそのままでいい。
// --- ユーザー用（Fortify）のグループ ---
Route::middleware('auth:web')->group(function () {
    Route::get('report-start-add', [UserReportController::class, 'report_start'])->name('report_start_add');

    Route::post('report-start-add', [UserReportController::class, 'post_report_start'])->name('report_start_add.post');

    Route::get('report-end-add', [UserReportController::class, 'report_end'])->name('report_end_add');

    Route::post('report-end-add', [UserReportController::class, 'post_report_end'])->name('report_end_add.post');

    Route::singleton('password', UserPasswordController::class)->only([
        'edit', 'update'
    ]);
});


// --- 管理者用（Laravel UI / adminガード）のグループ ---
Route::prefix('admin')->name('admin.')->group(function () {
    // ログインしていない時だけアクセスできるルート（guest:admin）
    //2026.05.11　これあるとループが起きる。
    //Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminController::class, 'login'])->name('login');
        Route::post('login', [AdminController::class, 'post_login'])->name('login.post');
        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
    //});

    Route::middleware('auth:admin')->group(function () {

        //Route::get('password-edit', [AdminPasswordController::class, 'edit'])->name('password.edit');
        //Route::post('password-edit', [AdminPasswordController::class, 'update'])->name('password.update');

        Route::singleton('password', AdminPasswordController::class)->only([
            'edit', 'update'
        ]);

        //2026.05.21 後でリソースコントローラに置き換えるからURLとルート名はそのままでいい。
        Route::prefix('users')->name('users.')->group(function () {
            //2026.05.11 middleware(['Login_check'])が何のためにあるんだろう？
            //Route::get('user_list', [UserController::class, 'user_list'])->middleware(['Login_check']);
            Route::get('user_list', [UserController::class, 'user_list'])->name('list');

            Route::get('user_edit', [UserController::class, 'user_edit'])->name('edit');

            Route::post('user_edit_done', [UserController::class, 'user_edit_done'])->name('edit_done');

            Route::get('user_add', [UserController::class, 'add'])->name('add');

            Route::post('user_add_check', [UserController::class, 'add_check'])->name('add_check');

            Route::post('user_add_done', [UserController::class, 'create'])->name('add_done');

            Route::get('user_delete', [UserController::class, 'delete'])->name('delete');

            Route::post('user_delete_check', [UserController::class, 'delete_check'])->name('delete_check');

            Route::post('user_delete_done', [UserController::class, 'delete_done'])->name('delete_done');

            Route::get('logintime-set', [UserController::class, 'logintime_set'])->name('logintime_set');

            Route::post('logintime-set', [UserController::class, 'post_logintime_set'])->name('logintime_set.post');
        });

        //2026.05.21 後でリソースコントローラに置き換えるからURLとルート名はそのままでいい。
        Route::prefix('admins')->name('admins.')->group(function () {
            //2024/06/22
            Route::get('list', [AdminController::class, 'list'])->name('list');

            Route::get('edit', [AdminController::class, 'edit'])->name('edit');

            Route::post('edit_done', [AdminController::class, 'edit_done'])->name('edit_done');

            Route::get('add', [AdminController::class, 'add'])->name('add');

            Route::post('add_check', [AdminController::class, 'add_check'])->name('add_check');

            Route::post('add_done', [AdminController::class, 'create'])->name('add_done');

            Route::get('delete', [AdminController::class, 'delete'])->name('delete');

            Route::post('delete_check', [AdminController::class, 'delete_check'])->name('delete_check');

            Route::post('delete_done', [AdminController::class, 'delete_done'])->name('delete_done');
        });

        Route::prefix('work-reports')->name('work_reports.')->group(function () {
            Route::get('list', [WorkReportController::class, 'list'])->name('list');

            //2026.06.15 ページネーション使う一覧はGETで行う
            //Route::post('list', [WorkReportController::class, 'post_list'])->name('list.post');

            Route::get('edit', [WorkReportController::class, 'edit'])->name('edit');

            Route::post('edit-done', [WorkReportController::class, 'edit_done'])->name('edit_done');

            Route::get('delete', [WorkReportController::class, 'delete'])->name('delete');

            Route::post('delete', [WorkReportController::class, 'post_delete'])->name('delete.post');

            Route::post('delete-check', [WorkReportController::class, 'delete_check'])->name('delete_check');

            Route::post('delete-done', [WorkReportController::class, 'delete_done'])->name('delete_done');

            Route::get('download', [WorkReportController::class, 'download'])->name('download');

            Route::post('download', [WorkReportController::class, 'post_download'])->name('download.post');

            Route::post('download-done', [WorkReportController::class, 'download_done'])->name('download_done');
        });

        Route::prefix('shifts')->name('shifts.')->group(function () {
            Route::get('edit', [ShiftController::class, 'edit'])->name('edit');

            Route::post('edit', [ShiftController::class, 'post_edit'])->name('edit.post');

            Route::get('delete', [ShiftController::class, 'delete'])->name('delete');

            Route::post('delete', [ShiftController::class, 'post_delete'])->name('delete.post');

            Route::post('delete-check', [ShiftController::class, 'delete_check'])->name('delete_check');

            Route::post('delete-done', [ShiftController::class, 'delete_done'])->name('delete_done');

            Route::get('import', [ShiftController::class, 'import'])->name('import');

            Route::post('import', [ShiftController::class, 'import_done'])->name('import.post');
        });

    });
});

