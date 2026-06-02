<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditShiftRequest extends FormRequest
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
            'schmonth'          => 'nullable|date_format:Y-m',
            'schuser_id'        => 'nullable|integer|max:9999999999',
            'month_shift'       => 'nullable|in:出勤,休,確休,在宅',
            'month_arrivaltime' => 'nullable|date_format:H:i',
            'month_leavetime'   => 'nullable|date_format:H:i',
            'shift.*'           => 'nullable|in:出勤,休,確休,在宅',
            'arrivaltime.*'     => 'nullable|date_format:H:i',
            'leavetime.*'       => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'schmonth.date_format'          => '月の形式が正しくありません。',
            'schuser_id.integer'            => '利用者IDは半角数字で入力してください。',
            'schuser_id.max'                => '利用者IDが長すぎます。',
            'month_shift.in'                => 'シフトの選択肢が正しくありません。',
            'month_arrivaltime.date_format' => '出勤時間の形式が正しくありません。',
            'month_leavetime.date_format'   => '退勤時間の形式が正しくありません。',
            'shift.*.in'                    => ':attributeのシフトの選択肢が正しくありません。',
            'arrivaltime.*.date_format'     => ':attributeの出勤時間の形式が正しくありません。',
            'leavetime.*.date_format'       => ':attributeの退勤時間の形式が正しくありません。',
        ];
    }

    public function attributes()
    {
        $attributes = [];

        $shifts = $this->input('shift', []);

        foreach (array_keys($shifts) as $day) {
            $attributes["shift.$day"] = "{$day}日目";
        }

        return $attributes;
    }
}
