<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanetarySystem extends Model

{
    protected $table = 'planetary_system';
    protected $fillable = [
        'name',
        'x_coord',
        'y_coord',
    ];
}
