<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteCheckReportsRequest extends FormRequest
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
            'arriveid'      => 'nullable|array',
            'arriveid.*'    => 'nullable|integer|max:9999999999',
            'leaveid'       => 'nullable|array',
            'leaveid.*'     => 'nullable|integer|max:9999999999',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.work_reports.delete')
                ->with('error_general', '削除に失敗しました。もう一度最初からやり直してください。')
                ->withInput()
        );
    }
}
