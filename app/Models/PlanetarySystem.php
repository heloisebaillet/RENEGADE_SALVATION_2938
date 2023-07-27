<?php
// PlanetarySystem.php (modèle Eloquent)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanetarySystem extends Model
{
    protected $table = 'planetary_systems'; // Nom de la table dans la base de données

    protected $fillable = ['user_id', 'x_coord', 'y_coord']; // Colonnes pouvant être massivement assignées

    // Définir la relation avec le modèle User si nécessaire
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
