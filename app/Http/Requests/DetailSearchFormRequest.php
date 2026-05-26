<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseSearchFormRequest;

class DetailSearchFormRequest extends BaseSearchFormRequest
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
            'arriveradio'  => 'nullable|in:ari,nashi',
            'andorradio'   => 'nullable|in:and,or',
            'leaveradio'   => 'nullable|in:ari,nashi',
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'arriveradio.in' => '出勤報告の選択肢が正しくありません。',
            'andorradio.in' => 'ANDOR検索の選択肢が正しくありません。',
            'leaveradio.in' => '退勤報告の選択肢が正しくありません。',
        ]);
    }
}
