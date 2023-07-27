<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipyard extends Model
{
    use HasFactory;
    protected $table = 'shipyards';
    protected $fillable = [
        'user_id',
        'type',

    ];
}
