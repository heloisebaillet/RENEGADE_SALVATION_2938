<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'quantity',
        'attacker_id',
        'defender_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
