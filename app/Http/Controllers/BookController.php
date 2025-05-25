<?php

namespace App\Http\Controllers;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Services\BookService;
use App\Traits\ApiBaseTrait;

class BookController extends Controller
{
    use ApiBaseTrait;

    public function __construct(BookService $service)
    {
        $this->service = $service;
        $this->storeRequestClass = StoreBookRequest::class;
        $this->updateRequestClass = UpdateBookRequest::class;
    }
}
