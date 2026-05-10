<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'type',
        'duration',
        'price',
        'includes',
        'features',
        'image_url',
        'is_featured',
    ];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
    ];
}
