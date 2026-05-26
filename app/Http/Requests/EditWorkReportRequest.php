<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditWorkReportRequest extends FormRequest
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
            // 日付は必須
            'date' => 'required|date',

            // 出勤時間はstartreportがあるとき必須
            'arrivaltime' => 'required_with:startreport|nullable|date_format:H:i:s',

            // 退勤時間はendreportがあるとき必須
            'leavetime' => 'required_with:endreport|nullable|date_format:H:i:s',

            // 遅刻時間は入力されている時だけチェックする
            'latetime'    => 'nullable',

            // 業務内容は最大1000文字まで
            'startreport' => 'nullable|string|max:1000',

            'endreport' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => '日付を入力してください。',
            'arrivaltime.required_with' => '出勤時間を入力してください。',
            'leavetime.required_with' => '退勤時間を入力してください。',
            'startreport.max'      => '出勤業務内容は1000文字以内で入力してください。',
            'endreport.max'      => '退勤業務内容は1000文字以内で入力してください。',
        ];
    }
}
