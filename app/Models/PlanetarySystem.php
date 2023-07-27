<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanetarySystem extends Model

{
    protected $table = 'planetary_system';
    protected $fillable = [
        'name',
        'x_coord',
        'y_coord',
    ];

    // Relation avec le modèle User pour récupérer le nom du système planétaire associé
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
