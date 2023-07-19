<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ships extends Model
{
    use HasFactory;
    protected $table = 'ships';
    protected $fillable = [
        'type',
        'quantity',
        'user_id'
    ];
}
