<?php

namespace App\Http\Controllers;

use App\Models\PlanetarySystem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PlanetarySystemController extends Controller
{

    public function index1()
    {
        $user_id = Auth::User()->id;
        $planetarySystems = PlanetarySystem::select('planetary_system.id', 'planetary_system.x_coord', 'planetary_system.y_coord', 'users.planetary_system_name', 'planetary_system.user_id')
            ->leftJoin('users', 'users.id', '=', 'planetary_system.user_id')
            ->get();

        return response()->json(['planetarySystems' => $planetarySystems], 200);
    }



    //public function create(Request $request)
    //{
    //    $user_id = Auth::user()->id;
    //    // pour le moment : 
    //    $name = $request->name;
    // quand name system implanté dans formulaire d'inscription user :
    //$user_id = Auth::user()->name;
    //    $x_coord = random_int(1, 999);
    //    $y_coord = random_int(1, 999);
    //
    //    $verify = PlanetarySystem::where('x_coord', $x_coord)->where('y_coord', $y_coord)->get();
    //    if ($verify != "") {

    //        $planetary_system = new PlanetarySystem();
    //        $planetary_system->name = $name;
    //        $planetary_system->x_coord = $x_coord;
    //        $planetary_system->y_coord = $y_coord;
    //        $planetary_system->save();

    //        return response()->json('Système ' . $planetary_system->name . ' créé !');
    //    } else {
    //        $x_coord = $x_coord = (rand(1, 999));
    //        $y_coord = (rand(1, 999));

    //        $planetary_system = new PlanetarySystem();
    //        $planetary_system->name = $request->name;
    //        $planetary_system->x_coord = $x_coord;
    //        $planetary_system->y_coord = $y_coord;
    //        $planetary_system->save();

    //        return response()->json('Système ' . $planetary_system->name . ' créé !');
    //    }
    //}

    public function index(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $name = $request->input('name');

        PlanetarySystem::create([
            'name' => $name,
            'x_coord' => random_int(1, 999),
            'y_coord' => random_int(1, 999),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Planetary system name saved successfully']);
    }

    public function destroy(PlanetarySystem $planetary_system)
    {
        $planetary_system->delete();

        return redirect()->route('index')->with('success', '✔️ System successfully deleted.');
    }
}
