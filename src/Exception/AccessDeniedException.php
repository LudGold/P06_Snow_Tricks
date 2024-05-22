<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AccessDeniedException extends HttpException
{
    public function __construct(string $message = 'Accès refusé.', \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct(403, $message, $previous, $headers, $code);
    }
}
