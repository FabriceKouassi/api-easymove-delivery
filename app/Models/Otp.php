<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Otp extends Pivot
{
    protected $table = 'otp';

    protected $fillable = [
        'phone',
        'code',
        'expires_at',
        'attempts'
    ];


}
