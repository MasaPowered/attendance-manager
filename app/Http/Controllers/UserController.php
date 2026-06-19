<?php

namespace App\Http\Controllers;

use App\Models\LoginTime;
use App\Models\User;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB; //2024/05/15 sasaki
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
//2026.06.04 バリデーション追加
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\EditUsersRequest;
use App\Http\Requests\AddUsersRequest;
use App\Http\Requests\AddCheckUsersRequest;
use App\Http\Requests\DeleteUsersRequest;
use App\Http\Requests\DeleteCheckUsersRequest;
use App\Http\Requests\SetLoginTimeRequest;
//2026.06.04 追加
use Illuminate\Support\Facades\Log;

//2026.06.04 UserControllerにバリデーション追加

class UserController extends Controller
{
    public function user_list()
    {
        $message_array = User::all();

        return view('admin.users.user_list', ['message_array' => $message_array]);
    }

    public function user_edit(IndexUserRequest $request)
    {
        $message_array = User::find($request->radio);

        return view('admin.users.user_edit', ['message_array' => $message_array]);
    }

    public function user_edit_done(EditUsersRequest $request)
    {
        $user = User::find($request->id);
        // ログ用キープ
        $oldName = $user->getOriginal('name');
        $oldEmail = $user->getOriginal('email');

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->pass);

        $user->save();

        Log::info('User updated', [
            'operator_id' => Auth::id(),
            'target_id'   => $user->id,
            'changes'     => [
                'name'  => "{$oldName} -> {$user->name}",
                'email' => "{$oldEmail} -> {$user->email}",
            ]
        ]);

        //return view('admin.users.user_edit_done', ['user' => $user]);

        return redirect()
            ->route('admin.users.edit', ['radio' => $user->id])
            ->with('success_message', "{$user->name}さんの情報を更新しました。");
    }

    public function add()
    {
        return view('admin.users.user_add');
    }

    public function add_check(AddUsersRequest $request)
    {
        session(['temp_password' => $request->pass]);

        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];


        return view('admin.users.user_add_check', ['data' => $data]);
    }

    public function create(AddCheckUsersRequest $request)
    {
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make(session('temp_password')),
        ]);

        Log::info('User created', [
            'operator_id' => Auth::id(),
            'target_id'   => $user->id,
            'details'     => [
                'name'  => $user->name,
                'email' => $user->email,
            ]
        ]);

        $data = [
            "name" => $request->name,
            "email" => $request->email,
        ];

        //return view('admin.users.user_add_done', ['data' => $data]);

        return redirect()
            ->route('admin.users.add')
            ->with('success_message', "{$request->name}さんを追加しました。");
    }

    public function delete()
    {
        $message_array = User::all();

        return view('admin.users.user_delete', ['message_array' => $message_array]);
    }

    public function delete_check(DeleteUsersRequest $request)
    {
        $user = User::find($request->radio);

        return view('admin.users.user_delete_check', ['user' => $user]);
    }

    public function delete_done(DeleteCheckUsersRequest $request)
    {
        $user = User::find($request->id);

        $user->delete();

        Log::info('User deleted', [
            'operator_id' => Auth::id(),
            'target_id'   => $user->id,
            'target_name' => $user->name,
        ]);

        return redirect()
            ->route('admin.users.delete')
            ->with('success_message', "{$user->name}さんを削除しました。");
    }

    public function logintime_set()
    {
        // ログイン時間取得
        $message_array = LoginTime::find(1);

        return view('admin.users.user_logintime_set', ['message_array' => $message_array]);
    }

    public function post_logintime_set(SetLoginTimeRequest $request)
    {
        $loginTime = LoginTime::findOrFail(1);

        // ログ用キープ
        $old_logintime_status = $loginTime->getOriginal('logintime_status');
        $old_start_time = $loginTime->getOriginal('start_time');
        $old_end_time = $loginTime->getOriginal('end_time');

        $loginTime->logintime_status = $request->boolean('logintime_status');
        $loginTime->start_time = $request->start_time;
        $loginTime->end_time = $request->end_time;

        if ($loginTime->isDirty()) {
            // 値が変わっていたら保存
            $loginTime->save();
            $success_message = "設定を更新しました。";

            Log::info('Logintime_set updated', [
                'operator_id' => Auth::id(),
                'changes'     => [
                    'logintime_status'  => "{$old_logintime_status} -> {$loginTime->logintime_status}",
                    'start_time' => "{$old_start_time} -> {$loginTime->start_time}",
                    'end_time' => "{$old_end_time} -> {$loginTime->end_time}",
                ]
            ]);
        }

        //return view('admin.users.user_logintime_set', ['message_array' => $loginTime, 'success_message' => $success_message]);

        return redirect()
                ->route('admin.users.logintime_set', ['message_array' => $loginTime])
                ->with('success_message', "設定を更新しました。");
    }
}
