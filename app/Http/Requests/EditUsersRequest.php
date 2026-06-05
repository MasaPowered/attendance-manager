<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'    => 'required|integer|exists:users,id',
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->id),
            ],
            'pass' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*()_+\-={}\[\]|;:><,.?\/~`])/',
            ],
            'pass2' => [
                'required',
                'string',
                'same:pass',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required'          => '利用者IDが正しく送信されていません。',
            'id.integer'           => '利用者IDの値が不正です。',
            'id.exists'            => '指定された管理者はデータベースに存在しません。',
            
            'name.required'        => '氏名を入力してください。',
            'name.max'             => '氏名は255文字以内で入力してください。',
            
            'email.required'       => 'メールアドレスを入力してください。',
            'email.email'          => '正しいメールアドレスの形式で入力してください。',
            'email.max'            => 'メールアドレスは255文字以内で入力してください。',
            'email.unique'         => 'このメールアドレスは既に他の利用者に使用されています。',
            
            'pass.required'        => 'パスワードを入力してください。',
            'pass.min'             => 'パスワードは8文字以上で入力してください。',
            'pass.max'            => 'パスワードは255文字以内で入力してください。',
            'pass.regex'           => 'パスワードには、数字と記号をそれぞれ1文字以上含めてください。',
            
            'pass2.required'       => 'パスワード（再入力）を入力してください。',
            'pass2.same'           => 'パスワード（再入力）が、新しいパスワードと一致しません。',
            'pass2.max'            => 'パスワード（再入力）は255文字以内で入力してください。',
        ];
    }
}
