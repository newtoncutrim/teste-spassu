<?php

namespace App\Repository;

use App\Exceptions\RunTimeException;
use App\Repository\Contract\InterfaceRepository;
use Exception;

class AbstractRepository implements InterfaceRepository
{
    protected $model;

    public function count()
    {
        return $this->model->whereNull('deleted_at')->count();
    }
    public function index(array $data = [])
    {
        return $this->model->all();
    }

    public function show(int $id)
    {
        return $this->model->whereNull('deleted_at')->find($id);
    }

    public function store(array $data = [])
    {
        try {
            return $this->model->create($data);
        } catch (\Throwable $e) {
            throw new RunTimeException("Erro ao criar registro: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $item = $this->model->whereNull('deleted_at')->find($id);
            $item->update($data);
            return $item;
        } catch (\Throwable $e) {
            throw new RunTimeException("Erro ao atualizar registro: " . $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            $item = $this->model->whereNull('deleted_at')->find($id);
            $item->delete();
            return true;
        } catch (\Throwable $e) {
            throw new RunTimeException("Erro ao deletar registro: " . $e->getMessage());
        }
    }
}

