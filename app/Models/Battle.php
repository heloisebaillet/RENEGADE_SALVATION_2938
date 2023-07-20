<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $table = 'battles';
    protected $fillable = [
        'ships_id',
        'attacker_id',
        'defender_id',
        'winner_id',
        'ressources_looted'
    ];
}
