<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetLoginTimeRequest extends FormRequest
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
            'logintime_status'      => 'nullable|in:on',
            'start_time'            => 'required_if:logintime_status,on|nullable|date_format:H:i',
            'end_time'              => 'required_if:logintime_status,on|nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'logintime_status.in'               => '選択が正しくありません。',
            'start_time.required_if'            => 'ログイン制限をかける場合は、開始時間を入力してください。',
            'start_time.date_format'            => '開始時間の形式が正しくありません。',
            'end_time.required_if'              => 'ログイン制限をかける場合は、終了時間を入力してください。',
            'end_time.date_format'              => '終了時間の形式が正しくありません。',
        ];
    }
}
