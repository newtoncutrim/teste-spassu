<?php

namespace App\Exceptions;

use Exception;

class CannotDeleteResourceWithRelationsException extends Exception
{
    public function __construct(
        string $message = 'Não é possível excluir este recurso porque ele possui registros relacionados.'
    ) {
        parent::__construct($message);
    }
}
