<?php

namespace App\Http\Requests;

use App\Http\Requests\DetailSearchFormRequest;

class SelectReportRequest extends DetailSearchFormRequest
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
        return array_merge(parent::rules(), [
            'radio'     => 'required_with:editsubmit|string|max:50',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'radio.required_with' => '報告内容を選択してください。',
            'radio.max'      => 'ラジオボタンの選択が不正です。',
        ]);
    }
}
