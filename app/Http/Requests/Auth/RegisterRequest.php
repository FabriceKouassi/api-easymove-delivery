<?php

namespace App\Http\Requests\Auth;

use App\Enums\RoleEnum;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class RegisterRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/', 'max:20', Rule::unique('users', 'phone')],
            'email' => ['nullable', 'string', 'email', Rule::unique('users', 'email')],
            'name' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', Rule::in([RoleEnum::CLIENT->value, RoleEnum::CONDUCTEUR->value])],
            'img' => ['nullable', 'file', 'mimes:png,jpg', 'max:2048'],
            'birthday_date' => ['nullable', 'date_format:Y-m-d', 'before:today']
        ];
    }

}
