<?php

namespace App\Services;

use App\Exceptions\CannotDeleteResourceWithRelationsException;
use App\Models\Topic;
use App\Repository\TopicRepository;
use App\Traits\ResponseTrait;
use App\Exceptions\RuntimeException;
class TopicService
{
    use ResponseTrait;
    public function __construct(private TopicRepository $repository) {}

    public function getAll()
    {
        return $this->repository->index();
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
        $topic = Topic::with('books')->findOrFail($id);

        if ($topic->books()->count() > 0) {
            return throw new CannotDeleteResourceWithRelationsException('Não é possível excluir um tópico que possui livros cadastrados.');
        }

        return $this->repository->delete($id);
    }
}
