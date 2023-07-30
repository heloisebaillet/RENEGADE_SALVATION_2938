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
        'planetary_system_name',
        'is_defender',
        'is_winner',
        'nb_fighter',
        'nb_frigate',
        'nb_cruiser',
        'nb_destroyer',
        'nb_round'
    ];


}
