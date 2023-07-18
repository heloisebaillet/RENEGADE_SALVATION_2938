<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    protected $fillable = [
        'map',
        'name',
        'x_coord',
        'y_coord',
        'localisation',
    ];
}
