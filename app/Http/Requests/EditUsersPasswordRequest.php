<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUsersPasswordRequest extends FormRequest
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
            'pass.required'        => 'パスワードを入力してください。',
            'pass.min'             => 'パスワードは8文字以上で入力してください。',
            'pass.max'             => 'パスワードは255文字以内で入力してください。',
            'pass.regex'           => 'パスワードには、数字と記号をそれぞれ1文字以上含めてください。',
            
            'pass2.required'       => 'パスワード（再入力）を入力してください。',
            'pass2.same'           => 'パスワード（再入力）が、新しいパスワードと一致しません。',
            'pass2.max'            => 'パスワード（再入力）は255文字以内で入力してください。',
        ];
    }
}
