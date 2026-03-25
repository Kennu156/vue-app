<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Volkswagen extends Model
{
    protected $fillable = [
        'user_id', 'title', 'image', 'description',
        'year', 'model', 'engine', 'mileage', 'price', 'color',
    ];

    protected $casts = [
        'year'    => 'integer',
        'mileage' => 'integer',
        'price'   => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}