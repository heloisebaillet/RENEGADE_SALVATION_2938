<?php

namespace App\Http\Controllers;

use App\Models\PlanetarySystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{


    public function getPlanetarySystems()
    {
        $user_id = Auth::id();

        $planetarySystems = PlanetarySystem::whereHas('user', function ($query) use ($user_id) {
            $query->where('id', '!=', $user_id);
        })->get();

        return response()->json(['status' => 'success', 'planetarySystems' => $planetarySystems]);
    }
}
