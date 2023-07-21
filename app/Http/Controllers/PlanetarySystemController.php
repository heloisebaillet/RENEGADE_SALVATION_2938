<?php

namespace App\Http\Controllers;

use App\Models\PlanetarySystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PlanetarySystemController extends Controller
{
    public function create(Request $request)
    {
        $user_id = Auth::user()->id;
        $name = $request->name;

        $planetary_system = new PlanetarySystem();
        $planetary_system->name = $name;
        $planetary_system->x_coordinate = random_int(1, 999);
        $planetary_system->y_coordinate = random_int(1, 999);
        $planetary_system->save();

        return response()->json('Système ' . $planetary_system->name . ' créé !');
    }

    public function destroy(PlanetarySystem $planetary_system)
    {
        $planetary_system->delete();

        // à arranger plus tard
        return redirect()->route('index')->with('success', '✔️ System successfully deleted.');;
    }
}
