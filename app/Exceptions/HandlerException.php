<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HandlerException extends Exception
{
    public function ValidatedAccountError()
    {
        throw new HttpException(403, "Merci d'attendre la validation de votre compte.");
    }
}
