<?php

namespace App\Repository;

use App\Models\Author;
use App\Repository\AbstractRepository;

class AuthorRepository extends AbstractRepository
{
    public function __construct(Author $model)
    {
        $this->model = $model;
    }

    public function findWithBooksAndTopics()
    {
        return $this->model
            ->with(['books.topics'])
            ->paginate(3);
    }
}
