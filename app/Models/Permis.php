<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'front_img',
    'back_img',
    'human_selfie_img',
    'expiry_date',
    'driving_licence_id',
    'isValidated', 'motif_refus'
])]

class Permis extends Model
{
    protected $table = 'permis';

    protected function casts(): array
    {
        return [
            'isValidated' => 'boolean',
            'expiry_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
