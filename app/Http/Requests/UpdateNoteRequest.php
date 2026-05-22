<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Insira um título válido.',
            'title.max' => 'O título deve ter no máximo 255 caracteres.',

            'content.string' => 'Insira um conteúdo válido.',
        ];
    }
}
