<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportShiftRequest extends FormRequest
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
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required'     => 'CSVファイルを選択してください。',
            'csv_file.file'         => 'アップロードされたファイルが不正です。',
            'csv_file.mimes'        => 'ファイル形式はCSV（.csv）のみ対応しています。',
            'csv_file.max'          => 'ファイルサイズが大きすぎます（最大2MBまで）。',
        ];
    }
}
