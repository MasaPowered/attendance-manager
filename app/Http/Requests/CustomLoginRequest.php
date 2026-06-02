<?php

namespace App\Http\Requests;

//2026.06.02 Fortifyログイン画面のバリデーション
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class CustomLoginRequest extends FortifyLoginRequest
{
    public function messages()
    {
        return [
            'email.required'    => 'メールアドレスを入力してください。',
            'email.string'      => '正しいメールアドレスを入力してください。',
            'password.required' => 'パスワードを入力してください。',
        ];
    }
}
