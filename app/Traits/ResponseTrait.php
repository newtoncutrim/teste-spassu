<?php

namespace App\Traits;

trait ResponseTrait
{
    public function success($data = '', $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public function error($message = 'Error', $status = 400, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function notFound($message = 'Registro não encontrado')
    {
        return $this->error($message, 404);
    }
}
