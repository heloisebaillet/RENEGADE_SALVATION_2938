<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RessourcesController extends Controller
{
    public function create()
    {
        $user_id = Auth::user()->id;
        $verify = Ressources::where('user_id', $user_id)->get();
        $type = "";
        $quantity = "";

        // plutôt utiliser where user id
        // $user = User::findOrFail($userId);

        // $user->ore += 100;
        // $user->fuel += 100;
        // $user->save();

        if ($verify != "") {

            if ($type == "ore" || $type == "fuel" || $type == "energy") {

                if ($type == "ore") {
                    $ore = Ressources::find();
                    $ore->user_id = $user_id;
                    $ore->type = $type;
                    $ore->quantity = $quantity;
                    $ore->save();

                    // return Response()->json( $ore, 201);
                    return response()->json(['message' => $ore . ' produced successfully!'], 201);
                }

                if ($type == "fuel") {
                    $fuel = Ressources::find();
                    $fuel->user_id = $user_id;
                    $fuel->type = $type;
                    $fuel->quantity = $quantity;
                    $fuel->save();

                    // return Response()->json($fuel, 201);
                    return response()->json(['message' => $fuel . ' produced successfully!'], 201);
                }

                if ($type == "energy") {
                    $energy = Ressources::find();
                    $energy->user_id = $user_id;
                    $energy->type = $type;
                    $energy->quantity = $quantity;
                    $energy->save();

                    // return Response()->json($energy, 201);
                    return response()->json(['message' => $energy . ' produced successfully!'], 201);
                }
            }
        }
    }
    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        // $user = User::findOrFail($userId);
        $showressources = Ressources::where('user_id', $user_id)->get();
        return response()->json($showressources, 201);
    }

    public function update($type = null)
    {
    }
}
