<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaseSearchFormRequest extends FormRequest
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
            'schdate'           => 'nullable|date',
            'schmonth'          => 'nullable|date_format:Y-m',
            'schuser_id'        => 'nullable|integer|max:9999999999',
            'month_shift'       => 'nullable|in:出勤,休,確休,在宅',
            'checkbox'          => 'nullable|in:on', //遅刻ありのチェックボックス
        ];
    }

    public function messages(): array
    {
        return [
            'schmonth.date_format'  => '月の形式が正しくありません。',
            'schuser_id.integer'    => '利用者IDは半角数字で入力してください。',
            'schuser_id.max'        => '利用者IDが長すぎます。',
            'month_shift.in'        => 'シフトの選択肢が正しくありません。',
            'checkbox.in'           => '遅刻ありの選択が正しくありません。',
        ];
    }
}
