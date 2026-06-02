<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteShiftRequest extends FormRequest
{
    protected $redirectRoute = 'admin.shifts.delete'; //2026.05.29 エラー時に戻るルート
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
            'schmonth' => 'nullable|date_format:Y-m',
        ];
    }

    public function messages(): array
    {
        return [
            'schmonth.date_format' => '月の形式が正しくありません。',
        ];
    }
}
