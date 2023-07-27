<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $table = 'battles';
    protected $fillable = [
        'uuid',
        'type',
        'user_id',
        'pt_loose',
        'loose_ships'
    ];

    public function winnerResources()
    {
        return $this->belongsTo(Resource::class, 'winner_id', 'planet_id');
    }

    public function loserResources()
    {
        return $this->belongsTo(Resource::class, 'loser_id', 'planet_id');
    }
}
