<?php

namespace App\Repository;

use App\Models\Topic;
use App\Repository\AbstractRepository;

class TopicRepository extends AbstractRepository
{
    public function __construct(Topic $model)
    {
        $this->model = $model;
    }
}
