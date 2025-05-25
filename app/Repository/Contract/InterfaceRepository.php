<?php
namespace App\Repository\Contract;

interface InterfaceRepository
{
    public function count();
    public function show(int $id);
    public function index();
    public function store(array $data = []);
    public function delete(int $id);
    public function update(int $id, array $data);
}
