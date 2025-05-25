<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTopicRequest extends FormRequest
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
            'description' => [
                'required',
                'string',
                'max:20',
                'min:3',
                Rule::unique('topics', 'description')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'description.required' => 'O campo descricao é obrigatório.',
            'description.string' => 'O campo descricao deve ser um texto.',
            'description.max' => 'O campo descricao não pode ter mais que 20 caracteres.',
            'topics.*.exists' => 'O tópico selecionado não existe ou foi removido.',
            'description.min' => 'O campo descricao deve ter no mínimo 3 caracteres.',
            'description.unique' => 'Já existe um tópico com essa descrição.',
        ];
    }
}
