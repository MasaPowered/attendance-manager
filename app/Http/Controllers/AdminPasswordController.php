<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Http\Requests\EditAdminsPasswordRequest;

class AdminPasswordController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('admin.password_edit', ['login_admin' => Auth::user()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditAdminsPasswordRequest $request)
    {
        $admin = Admin::find(Auth::id());
        $admin->password = Hash::make($request->pass);
        $admin->save();

        return redirect()
            ->route('admin.password.edit')
            ->with('success_message', 'パスワードを正常に変更しました。');
    }

}
