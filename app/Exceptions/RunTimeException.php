<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RunTimeException extends HttpException
{
    public function __construct($message = 'Run time exception', $code = 500)
    {
        parent::__construct($code, $message);
    }
    
    public function render()
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], $this->getStatusCode());
    }
}