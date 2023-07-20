<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressources extends Model
{ use HasFactory;
    protected $table = 'ressources';
    protected $fillable = [
        'user_id',
        'type',
        'quantity'
    ];
}
