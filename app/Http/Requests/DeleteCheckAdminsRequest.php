<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeletecheckAdminsRequest extends FormRequest
{
    protected $redirectRoute = 'admin.admins.delete'; //2026.05.29 エラー時に戻るルート

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
            'id'    => 'required|integer|exists:admins,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.admins.delete')
                ->with('error_general', '削除に失敗しました。もう一度最初からやり直してください。')
                ->withInput()
        );
    }
}
