<?php

namespace App\Http\Controllers;

use App\Http\Requests\Author\StoreAuthorRequest;
use App\Http\Requests\Author\UpdateAuthorRequest;
use App\Services\AuthorService;
use App\Traits\ApiBaseTrait;
use App\Traits\ResponseTrait;

class AuthorController extends Controller
{
    use ApiBaseTrait;
    use ResponseTrait;

    public function __construct(AuthorService $service)
    {
        $this->service = $service;

        $this->storeRequestClass = StoreAuthorRequest::class;
        $this->updateRequestClass = UpdateAuthorRequest::class;
    }

    public function findWithBooksAndTopics()
    {
        $data = $this->service->findWithBooksAndTopics();
        if (!$data) {
            return $this->error('Autor nao encontrado', 404);
        }
        return $this->success($data);
    }
}
