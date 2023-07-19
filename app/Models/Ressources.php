<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ressources extends Model
{
    protected $table = 'ressources';

    protected $fillable = [

        'user_id',
        'type',
        'quantity'
    ];
}
