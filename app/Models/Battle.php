<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $table = 'battles';
    protected $fillable = [
        'ships_id',
        'attacker_id',
        'defender_id',
        'ttl_att_pts',
        'ttl_def_pts',
        'resources_looted'
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
