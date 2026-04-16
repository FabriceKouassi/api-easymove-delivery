<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

abstract class ApiFormRequest extends FormRequest
{
    public function expectsJson(): bool
    {
        return true;
    }

    public function wantsJson(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
