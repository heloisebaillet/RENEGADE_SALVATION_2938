<?php

namespace App\Models;




use Illuminate\Database\Eloquent\Model;

class Cruiser extends Model
{
    protected $table = 'Cruiser';
    protected $fillable = [
        'created_at',
        'type',
        'quantity',
        'structure_id',
        'updated_at'
    ];
}
class Destroyer extends Model
{
    protected $table = 'fleet';
    protected $fillable = [
        'created_at',
        'type',
        'quantity',
        'structure_id',
        'updated_at'
    ];
}
