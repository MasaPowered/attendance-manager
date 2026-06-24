<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserTableController;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
//2026.05.28 バリデーション追加
use App\Http\Requests\IndexAdminsRequest;
use App\Http\Requests\EditAdminsRequest;
use App\Http\Requests\AddAdminsRequest;
use App\Http\Requests\DeleteAdminsRequest;
use App\Http\Requests\AddCheckAdminsRequest;
use App\Http\Requests\DeleteCheckAdminsRequest;
use App\Http\Requests\AdminLoginRequest;
//2026.06.02 追加
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        return view('admin.login');
    }

    public function post_login(AdminLoginRequest $request)
    {
        
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {

            Log::info('Admin logged in', [
                'operator_id' => Auth::id(),
                'target_id'   => Auth::id(),
                'details'     => [
                    'ip'         => $request->ip(),        // どのIPアドレスからか
                    'user_agent' => $request->userAgent(), // どのブラウザ・端末からか
                ]
            ]);
            return redirect()->route('admin.work_reports.list');
        } else {
            Log::warning('Admin login failed', [
                'operator_id' => null,
                'target_id'   => null,
                'details'     => [
                    'email'      => $request->email,
                    'ip'         => $request->ip(),
                    'user_agent' => $request->userAgent(), 
                ]
            ]);
            return redirect()
                ->route('admin.login')
                ->with('error_message', 'ログインに失敗しました。');
        }
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Admin logged out', [
            'operator_id' => $userId,
            'target_id'   => $userId,
            'details'     => [
                'ip' => $request->ip(),
            ]
        ]);

        return redirect()->route('admin.login');
    }

    public function list()
    {
        $message_array = Admin::all();

        return view('admin.admins.admin_list', ['message_array' => $message_array]);
    }

    public function edit(IndexAdminsRequest $request)
    {
        $message_array = Admin::find($request->radio);

        return view('admin.admins.admin_edit', ['message_array' => $message_array]);
    }

    public function edit_done(EditAdminsRequest $request)
    {
        //マスターアカウント用
        if ($request->id == 1) {
            $admin = Admin::findOrFail($request->id);

            $pass = Hash::make($request->pass);
        
            $admin->password = $pass;
            $admin->save();

            Log::info('Admin updated', [
                'operator_id' => Auth::id(),
                'target_id'   => $admin->id,
            ]);
            $currentUser = Auth::user();

            return redirect()
                ->route('admin.admins.edit', ['radio' => $admin->id])
                ->with('success_message', "{$admin->name}さんの情報を更新しました。");
        }
        
        $admin = Admin::findOrFail($request->id);

        // ログ用キープ
        $oldName = $admin->getOriginal('name');
        $oldEmail = $admin->getOriginal('email');

        $pass = Hash::make($request->pass);
        
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = $pass;

        $admin->save();

        Log::info('Admin updated', [
            'operator_id' => Auth::id(),
            'target_id'   => $admin->id,
            'changes'     => [
                'name'  => "{$oldName} -> {$admin->name}",
                'email' => "{$oldEmail} -> {$admin->email}",
            ]
        ]);
        $currentUser = Auth::user();

        // 自分自身の情報を更新した場合
        if ($admin->id === $currentUser->id) {
            Auth::login($admin);
        }

        //return view('admin.admins.admin_edit_done', ['admin' => $admin]);

        return redirect()
            ->route('admin.admins.edit', ['radio' => $admin->id])
            ->with('success_message', "{$admin->name}さんの情報を更新しました。");
    }

    public function add()
    {
        return view('admin.admins.admin_add');
    }

    public function add_check(AddAdminsRequest $request)
    {
        //2026.05.29 セッションにパスワードを置く
        session(['temp_password' => $request->pass]);

        //暗号化
        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        return view('admin.admins.admin_add_check', ['data' => $data]);
    }

    public function create(AddCheckAdminsRequest $request)
    {
        $admin = Admin::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make(session('temp_password')),
        ]);

        Log::info('Admin created', [
            'operator_id' => Auth::id(),
            'target_id'   => $admin->id,
            'details'     => [
                'name'  => $admin->name,
                'email' => $admin->email,
            ]
        ]);

        return redirect()
            ->route('admin.admins.add')
            ->with('success_message', "{$request->name}さんを追加しました。");
    }

    public function delete()
    {
        $message_array = Admin::all();

        return view('admin.admins.admin_delete', ['message_array' => $message_array]);
    }

    public function delete_check(DeleteAdminsRequest $request)
    {
        $admin = Admin::find($request->radio);

        return view('admin.admins.admin_delete_check', ['admin' => $admin]);
    }

    public function delete_done(DeleteCheckAdminsRequest $request)
    {
        if ($request->id == 1) {
            return redirect()->back()->with('error_message', 'マスターアカウントは削除できません。');
        }

        $admin = Admin::find($request->id);

        $admin->delete();

        Log::info('Admin deleted', [
            'operator_id' => Auth::id(),
            'target_id'   => $admin->id,
            'target_name' => $admin->name,
        ]);
        
        return redirect()
            ->route('admin.admins.delete')
            ->with('success_message', "{$admin->name}さんを削除しました。");
    }
}
