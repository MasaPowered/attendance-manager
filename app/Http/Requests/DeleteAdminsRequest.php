<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAdminsRequest extends FormRequest
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
            'radio'     => 'required_with:delsubmit|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'radio.required_with'   => '項目を選択してください。',
            'radio.max'             => 'ラジオボタンの選択が不正です。',
        ];
    }
}
