<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ships extends Model
{
    use HasFactory;
    protected $table = 'Ships';
    protected $fillable = [
        'type',
        'quantity',
        'user_id',
        'attacker_id',
        'defender_id'
    ];
}
