<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
            'title' => 'required|string|max:40|min:3|unique:books,title,' . $this->route('book'),
            'publisher' => 'required|string|max:40|min:3',
            'year_of_publication' => 'required|integer|min:1500|max:' . date('Y'),
            'price' => 'required|numeric|min:0',
            'edition' => 'required|numeric|min:1',
            'authors' => 'required|array',
            'authors.*' => [
                'required',
                'integer',
                Rule::exists('authors', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'topics' => 'required|array',
            'topics.*' => [
                'required',
                'integer',
                Rule::exists('topics', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ];
    }

        public function messages(): array
    {
        return [
            'title.required' => 'O campo nome é obrigatório.',
            'title.string' => 'O campo nome deve ser um texto.',
            'title.max' => 'O campo nome não pode ter mais que 40 caracteres.',
            'title.min' => 'O campo nome deve ter no mínimo 3 caracteres.',
            'title.unique' => 'Já existe um livro com esse nome.',
            'publisher.required' => 'O campo editora é obrigatório.',
            'publisher.string' => 'O campo editora deve ser um texto.',
            'publisher.max' => 'O campo editora não pode ter mais que 40 caracteres.',
            'publisher.min' => 'O campo editora deve ter no mínimo 3 caracteres.',
            'year_of_publication.required' => 'O campo ano de publicação é obrigatório.',
            'year_of_publication.integer' => 'O campo ano de publicação deve ser um número inteiro.',
            'year_of_publication.min' => 'O campo ano de publicação deve ter no mínimo 1800.',
            'year_of_publication.max' => 'O campo ano de publicação deve ter no máximo ' . date('Y'),
            'price.required' => 'O campo preco é obrigatório.',
            'price.numeric' => 'O campo preco deve ser um número.',
            'price.min' => 'O campo preco deve ter no mínimo 0.',
            'edition.required' => 'O campo edição é obrigatório.',
            'edition.numeric' => 'O campo edição deve ser um número.',
            'edition.min' => 'O campo edição deve ter no mínimo 1.',
            'authors.required' => 'O campo autor é obrigatório.',
            'authors.array' => 'O campo autor deve ser um array.',
            'authors.*.exists' => 'O autor selecionado não existe ou foi removido.',
        ];
    }
}
