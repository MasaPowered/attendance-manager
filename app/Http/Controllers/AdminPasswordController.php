<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Http\Requests\EditAdminsPasswordRequest;
//2026.06.19 追加
use Illuminate\Support\Facades\Log;

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

        Log::info('Admin password updated', [
            'operator_id' => Auth::id(),
            'target_id'   => $admin->id,
        ]);

        return redirect()
            ->route('admin.password.edit')
            ->with('success_message', 'パスワードを正常に変更しました。');
    }

}
