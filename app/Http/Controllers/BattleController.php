<?php

namespace App\Http\Controllers;

use App\Models\PlanetarySystem;
use Illuminate\Http\Request;

class BattleController extends Controller
{
    // ...

    public function getPlanetarySystems()
    {
        $planetarySystems = PlanetarySystem::all();
        return response()->json(['status' => 'success', 'planetarySystems' => $planetarySystems]);
    }

    // ...
}
