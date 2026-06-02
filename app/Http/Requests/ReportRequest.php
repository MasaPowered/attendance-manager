<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
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
            'report'     => 'required_with:submit|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'report.required_with'   => '報告内容を入力してください。',
            'report.max'             => '255文字以内にしてください。',
        ];
    }
}
