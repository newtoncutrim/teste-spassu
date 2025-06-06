<?php

namespace App\Http\Controllers;

use App\Http\Requests\Topic\StoreTopicRequest;
use App\Http\Requests\Topic\UpdateTopicRequest;
use App\Services\TopicService;
use App\Traits\ApiBaseTrait;

class TopicController extends Controller
{
    use ApiBaseTrait;

    public function __construct(TopicService $service)
    {
        $this->service = $service;
        $this->storeRequestClass = StoreTopicRequest::class;
        $this->updateRequestClass = UpdateTopicRequest::class;
    }
}
