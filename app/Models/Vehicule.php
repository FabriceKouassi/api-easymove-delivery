<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'car_img', 'carte_grise_img', 'car_type', 'car_brand', 'car_model',
    'car_color', 'immatriculation_number', 'production_year', 'user_id',
    'isValidated', 'motif_refus'
])]
class Vehicule extends Model
{
    protected $table = 'vehicules';

    protected $casts = [
        'isValidated' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
