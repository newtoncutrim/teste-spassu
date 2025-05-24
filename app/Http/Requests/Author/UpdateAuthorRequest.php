<?php

namespace App\Http\Requests\Author;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAuthorRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:40',
                'min:3',
                Rule::unique('authors', 'name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })->ignore($this->route('author')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser um texto.',
            'name.max' => 'O campo nome não pode ter mais que 255 caracteres.',
            'name.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'name.unique' => 'Já existe um autor com esse nome.',
        ];
    }
}
