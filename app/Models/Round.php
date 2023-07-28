<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{   use HasFactory;
    protected $table = 'rounds';
    protected $fillable = [
        'uuid',
        'user_id',
        'is_defender',
        'nb_fighter',
        'nb_frigate',
        'nb_cruiser',
        'nb_destroyer',
        'nb_round'
    ];


}
