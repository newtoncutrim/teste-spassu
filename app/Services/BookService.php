<?php

namespace App\Services;

use App\Repository\BookRepository;

class BookService
{
    public function __construct(private BookRepository $repository) 
    {}

    public function getAll() {
        return $this->repository->getAllWithRelations();
    }

    public function create(array $data) {
        return $this->repository->createWithRelations($data);
    }

    public function findById(int $id) {
        return $this->repository->show($id);
    }

    public function update(int $id, array $data) {
        return $this->repository->updateWithRelations($id, $data);
    }

    public function delete(int $id) {
        return $this->repository->delete($id);
    }
}

