<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanetarySystem extends Model

{

    use HasFactory;

    protected $table = 'planetary_system';
    protected $fillable = [
        'name',
        'x_coord',
        'y_coord',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
