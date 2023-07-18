<?php

namespace App\Models;




use Illuminate\Database\Eloquent\Model;

class ships extends Model
{
    protected $table = 'ships';
    protected $fillable = [
        'type',
        'quantity',
        'user_id'
    ];
}
