<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delete extends Model
{
    use HasFactory;
    protected $table = 'delete';
    protected $fillable = [
        'firstname',
        'lastname',
        'date_of_birth',
        'username',
        'planetary_system_name'

    ];
}
