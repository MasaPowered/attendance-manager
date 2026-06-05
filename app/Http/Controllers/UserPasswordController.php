<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUsersPasswordRequest;


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

        return redirect()
            ->route('password.edit')
            ->with('success_message', 'パスワードを正常に変更しました。');
    }
}
