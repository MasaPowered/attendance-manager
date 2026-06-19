<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUsersPasswordRequest;
//2026.06.19 追加
use Illuminate\Support\Facades\Log;

class UserPasswordController extends Controller
{
    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        return view('user.password_edit', ['login_user' => Auth::user()]);
    }

    /**
     * Update the resource in storage.
     */
    public function update(EditUsersPasswordRequest $request)
    {
        $user = User::find(Auth::id());
        $user->password = Hash::make($request->pass);
        $user->save();

        Log::info('User password updated', [
            'operator_id' => Auth::id(),
            'target_id'   => $user->id,
        ]);

        return redirect()
            ->route('password.edit')
            ->with('success_message', 'パスワードを正常に変更しました。');
    }
}
