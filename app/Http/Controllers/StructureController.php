<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\Shipyard;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class StructureController extends Controller
{   
    
    // creer un batiment par rapport a ses ressources et à son énergie
    public function create(Request $request, $type = null)
    {
        $user_id = Auth::user()->id;
        $level = "1";
        $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
        // A vérifier si l'utilisateur a assez d'énergie
        if ($type == "mine" || $type == "raffinery" || $type == "powerplant" || $type == "shipyard") {
            // Création d'une mine
            if ($type == "mine") {
                if ($ore->quantity >= 300) {
                    $energy_consumption = "1";
                    $mine = new Structure();
                    $mine->user_id = $user_id;
                    $mine->type = $type;
                    $mine->level = $level;
                    $mine->energy_consumption = $energy_consumption;
                    $mine->save();
                    $ore->quantity = $ore->quantity - 300;
                    $ore->save();
                    return Response()->json($mine, 201);
                } else {
                    return Response()->json(['success' => 'false'], 423);
                }
            }
            // Création d'une raffinery
            if ($type == "raffinery") {
                if ($ore->quantity >= 300) {
                    $energy_consumption = "2";
                    $raffinery = new Structure();
                    $raffinery->user_id = $user_id;
                    $raffinery->type = $type;
                    $raffinery->level = $level;
                    $raffinery->energy_consumption = $energy_consumption;
                    $raffinery->save();
                    $ore->quantity = $ore->quantity - 300;
                    $ore->save();
                    return Response()->json($raffinery, 201);
                } else {
                    return Response()->json(['success' => 'false'], 423);
                }
            }
            // Création d'une powerplant
            if ($type == "powerplant") {
                if ($ore->quantity >= 500) {
                    $energy_consumption = "0";
                    $powerplant = new Structure();
                    $powerplant->user_id = $user_id;
                    $powerplant->type = $type;
                    $powerplant->level = $level;
                    $powerplant->energy_consumption = $energy_consumption;
                    $powerplant->save();
                    $ore->quantity = $ore->quantity - 500;
                    $ore->save();
                    return Response()->json($powerplant, 201);
                } else {
                    return Response()->json(['success' => 'false'], 423);
                }
            }
            // Création d'une shipyard
            if ($type == "shipyard") {
                if ($ore->quantity >= 1000) {
                    $energy_consumption = '0';
                    $shipyard = new Shipyard();
                    $shipyard->user_id = $user_id;
                    $shipyard->save();
                    $ore->quantity = $ore->quantity - 1000;
                    $ore->save();
                    return Response()->json($shipyard, 201);
                } else {
                    return Response()->json(['success' => 'false'], 423);
                }
            }
        } else {
            return Response()->json(['success' => 'false'], 423);
        }
    }
    // lit les batiments de l'utilisateur
    public function read()
    {
        // A modifier, quand le controller User sera créé
        $user_id = Auth::User()->id;
        $mine = Structure::where('user_id', $user_id)->where('type', 'mine')->get();
        $raffinery = Structure::where('user_id', $user_id)->where('type', 'raffinery')->get();
        $powerplant = Structure::where('user_id', $user_id)->where('type', 'powerplant')->get();
        $shipyard = Structure::where('user_id', $user_id)->where('type', 'shipyard')->get();
        $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
        $response = [
            'mine' => $mine,
            'raffinery' => $raffinery,
            'powerplant' => $powerplant,
            'shipyard' => $shipyard,
            'ore' => $ore
        ];
        return response()->json($response, 200);
    }
    // Ajoute level +1 quand les ressources le permette
    public function addlevel(Request $request, $id)
    {
        // A modifier, quand le controller User sera créé
        $user_id = Auth::user()->id;
        $minerais = 300;
        $type = Structure::where('user_id', $user_id)->where('id', $id)->first();
        if ($type != "") {
            if ($minerais >= 300) {

                $type->level += 1;
                $type->save();

                return Response()->json($type, 201);
            } else {
                return Response()->json(['success' => 'false'], 400);
            }
        } else {
            return Response()->json(['success' => 'false'], 400);
        }
    }
    public function delete(Request $request, $id = null)
    {  $mine = 150;
        $raffinery = 150;
        $powerplant = 250;
        $shipyard = 500;
        // A modifier, quand le controller User sera créé
        $user_id = Auth::user()->id;
        $type = Structure::where('user_id', $user_id)->where('id', $id)->first();
        $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
        if ($type != "" && $user_id == $type->user_id) {
            $type->delete();
            // a mettre à jour par rapport aux types de suppression
            $ore->quantity = $ore->quantity + $mine ;
            $ore->save();
            return Response()->json(['success' => 'true'], 204);
        }
        // si déja supprimé :
        else if ($type == "") {
            return Response()->json(['success' => 'false'], 400);
        }


        //si pas autorisé
        else {
            return Response()->json(['success' => 'false'], 403);
        }

        // Ajouter après la création du controller ressources,
        // la réinsertion de 50% de la fabrication.
    }
}
