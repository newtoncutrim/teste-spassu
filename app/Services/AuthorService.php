<?php

namespace App\Services;

use App\Exceptions\CannotDeleteResourceWithRelationsException;
use App\Models\Author;
use App\Repository\AuthorRepository;
use App\Exceptions\RuntimeException;
use App\Models\Book;
use App\Traits\ResponseTrait;

class AuthorService
{
    use ResponseTrait;

    public function __construct(private AuthorRepository $repository) {}

    public function getAll()
    {
        return $this->repository->index();
    }

    public function findWithBooksAndTopics()
    {
        return $this->repository->findWithBooksAndTopics();
    }

    public function create(array $data)
    {
        return $this->repository->store($data);
    }

    public function findById(int $id)
    {
        return $this->repository->show($id);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        $author = Author::find($id);

        if ($author->books()->count() > 0) {
    
            throw new CannotDeleteResourceWithRelationsException('Não é possível excluir um autor que possui livros cadastrados.');
        }

        return $this->repository->delete($id);
    }
}
