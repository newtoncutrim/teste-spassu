<?php

namespace App\Repository;

use App\Models\Book;
use App\Repository\AbstractRepository;

class BookRepository extends AbstractRepository
{
    public function __construct(Book $model)
    {
        $this->model = $model;
    }

    public function createWithRelations(array $data)
    {

        $book = $this->model->create($data);

        if (isset($data['authors'])) {
            $book->authors()->sync($data['authors']);
        }

        if (isset($data['topics'])) {
            $book->topics()->sync($data['topics']);
        }

        return $book->load('authors', 'topics');
    }

    public function updateWithRelations(int $id, array $data)
    {
        $book = $this->model->findOrFail($id);

        $book->update([
            'title' => $data['title'] ?? $book->title,
            'publisher' => $data['publisher'] ?? $book->publisher,
            'year_of_publication' => $data['year_of_publication'] ?? $book->year_of_publication,
            'price' => $data['price'] ?? $book->price,
            'edition' => $data['edition'] ?? $book->edition,
        ]);

        if (isset($data['authors'])) {
            $book->authors()->sync($data['authors']);
        }

        if (isset($data['topics'])) {
            $book->topics()->sync($data['topics']);
        }

        return $book->load('authors', 'topics');
    }

    public function getAllWithRelations()
    {
        return $this->model->with(['authors', 'topics'])->get();
    }

    public function findByIdWithRelations(int $id)
    {
        return $this->model->with(['authors', 'topics'])->findOrFail($id);
    }
}
