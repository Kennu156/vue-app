<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    const CREATED_AT = 'added';
    const UPDATED_AT = 'edited';

    protected $fillable = ['name', 'latitude', 'longitude', 'description'];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
        'added'     => 'datetime',
        'edited'    => 'datetime',
    ];
}