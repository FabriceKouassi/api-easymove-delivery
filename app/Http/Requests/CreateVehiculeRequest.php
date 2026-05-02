<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateVehiculeRequest extends ApiFormRequest
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
            'car_img' => ['required', 'image', 'max:5120'],
            'carte_grise_img' => ['required', 'image', 'max:5120'],
            'car_type' => ['required', 'max:5120'],
            'car_brand' => ['required', 'string'],
            'car_model' => ['required', 'string'],
            'car_color' => ['required', 'string'],
            'immatriculation_number' => ['required', 'string', 'unique:vehicules,immatriculation_number'],
            'production_year' => ['required', 'date_format:Y'],
        ];
    }
}
