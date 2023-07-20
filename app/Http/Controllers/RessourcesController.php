<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RessourcesController extends Controller
{
    public function getRessources()
    {
        $userId = Auth::user()->id;


        $user = User::findOrFail($userId);
        $ressources = [
            'ore' => $user->ore,
            'fuel' => $user->fuel,
            'energy' => $user->energy,
        ];

        return response()->json($ressources);
    }

    public function produceRessources()
    {
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);

        $user->ore += 100;
        $user->fuel += 100;
        $user->save();

        return response()->json(['message' => 'Ressources produced successfully']);
    }
    public function takeRessources()
    {
        if ($type == "mine" || $type == "raffinery" || $type == "powerplant") {

            if ($type == "mine") {
                $mine = Ressources::find();
                $mine->user_id = $user_id;
                $mine->type = $type;
                $mine->quantity = $quantity;
                $mine->save();

                return Response()->json($mine, 201);
            }

            if ($type == "raffinery") {
                $mine = Ressources::find();
                $mine->user_id = $user_id;
                $mine->type = $type;
                $mine->quantity = $quantity;
                $mine->save();

                return Response()->json($raffinery, 201);
            }

            if ($type == "powerplant") {
                $mine = Ressources::find();
                $mine->user_id = $user_id;
                $mine->type = $type;
                $mine->quantity = $quantity;
                $mine->save();

                return Response()->json($powerplant, 201);
            }
        }
    }
}
