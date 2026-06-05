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
use App\Http\Requests\DeletecheckAdminsRequest;
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
            Log::info('admin(' . Auth::id() . '): login');
            return redirect()->route('admin.work_reports.list');
        } else {
            $msg = 'ログインに失敗しました。';
            return view('admin.login', ['error_message' => $msg]);
        }
    }

    public function logout(Request $request)
    {
        Log::info('admin(' . Auth::id() . '): logout');

        Auth::guard('admin')->logout();

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
        $admin = Admin::findOrFail($request->id);

        // ログ用キープ
        $oldName = $admin->getOriginal('name');
        $oldEmail = $admin->getOriginal('email');

        $pass = Hash::make($request->pass);
        
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = $pass;

        if ($admin->isDirty()) {
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
        }

        return view('admin.admins.admin_edit_done', ['admin' => $admin]);
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
            //2026.04.29 pass→password
            "password" => Hash::make(session('temp_password')),
        ]);

        Log::info('admin(' . Auth::id() . '): admin_create[' . $admin->id . ' ' . $admin->name . ']');

        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        return view('admin.admins.admin_add_done', ['data' => $data]);
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

    public function delete_done(DeletecheckAdminsRequest $request)
    {
        $admin = Admin::find($request->id);

        if ($admin) {
            $admin->delete();

            Log::info('admin(' . Auth::id() . '): admin_delete[' . $request->id . ' ' . $admin->name . ']');
        }

        return view('admin.admins.admin_delete_done');
    }
}
