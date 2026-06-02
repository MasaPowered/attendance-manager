<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseSearchFormRequest;

class DeleteReportsRequest extends BaseSearchFormRequest
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
            'report_check'     => 'required_with:delsubmit|array',
            'report_check.*' => 'string|max:50',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'report_check.required_with' => '報告内容を選択してください。',
            'report_check.*.max'      => ':position行目のチェックボックスの選択が不正です。',
        ]);
    }
}
