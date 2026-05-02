<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePermisRequest extends ApiFormRequest
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
            'user_phone' => ['required', 'exists:users,phone'],
            'user_id' => ['nullable'],
            'front_img' => ['required', 'image', 'max:5120'],
            'back_img' => ['nullable', 'image', 'max:5120'],
            'human_selfie_img' => ['required', 'image', 'max:5120'],
            'expiry_date' => ['required', 'date_format:Y-m-d'],
            'driving_licence_id' => ['required', 'unique:permis,driving_licence_id'],
        ];
    }
}
