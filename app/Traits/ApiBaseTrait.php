<?php

namespace App\Traits;

use App\Exceptions\CannotDeleteResourceWithRelationsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

trait ApiBaseTrait
{
    use ResponseTrait;

    protected $service;

    protected  $storeRequestClass = null;
    protected  $updateRequestClass = null;

    public function count(){
        $count = $this->service->count();
        return $this->success($count, 'Count retrieved successfully');
    }
    public function index()
    {
        $data = $this->service->getAll();
        return $this->success($data, 'List retrieved successfully');
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request, $this->storeRequestClass);

        if (empty($data)) {
            return $this->error('No data provided', 422);
        }

        $item = $this->service->create($data);

        if (!$item) {
            return $this->error('Failed to create item', 500);
        }

        return $this->success($item, 'Item created successfully', 201);
    }

    public function show($id)
    {
        $item = $this->service->findById($id);
        if (!$item) {
            return $this->notFound();
        }

        return $this->success($item, 'Item retrieved successfully');
    }

    public function update(Request $request, $id)
    {
        $data = $this->validateRequest($request, $this->updateRequestClass);

        if (empty($data)) {
            return $this->error('No data provided to update', 422);
        }

        $item = $this->service->findById($id);
        if (!$item) {
            return $this->notFound();
        }

        $item = $this->service->update($id, $data);

        if (!$item) {
            return $this->error('Failed to update item', 500);
        }

        return $this->success($item, 'Updated successfully');
    }

    public function destroy($id)
    {
        $item = $this->service->findById($id);
        if (!$item) {
            return $this->notFound();
        }

        try {
            $this->service->delete($id);
        } catch (CannotDeleteResourceWithRelationsException $e) {
            return $this->error($e->getMessage(), 400);
        }

        return $this->success('Item deletado com sucesso');
    }


    protected function validateRequest(Request $request, ?string $requestClass)
    {
        if (!$requestClass) {
            return $request->all();
        }

        return App::make($requestClass)->validated();
    }
}
