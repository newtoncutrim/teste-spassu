<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTopicRequest extends FormRequest
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
            'description' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'O campo descricao é obrigatório.',
            'description.string' => 'O campo descricao deve ser um texto.',
            'description.max' => 'O campo descricao não pode ter mais que 20 caracteres.',
        ];
    }
}
