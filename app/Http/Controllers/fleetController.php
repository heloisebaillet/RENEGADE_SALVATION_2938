<?php


namespace App\Http\Controllers;

use App\Models\ships;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class FleetController extends Controller
{

    public function create(Request $request, $type = null)
    {
        $user_id = "1";
        if ($type == "cruiser" || $type == "destroyer" || $type == "fighter" || $type == "frigate") {

            if ($type == "fighter") {
                //$fuel_consumption ="";
                $fighter = new ships();
                $fighter->user_id = $user_id;
                $fighter->type = $type;
                $fighter->quantity += 1;

                $minerais = 50;

                if ($minerais >= 50) {
                    return Response()->json($fighter, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "frigate") {
                //$fuel_consumption ="";
                $frigate = new ships();
                $frigate->user_id = $user_id;
                $frigate->type = $type;
                $frigate->quantity += 1;

                $minerais = 200;

                if ($minerais >= 200) {
                    return Response()->json($frigate, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "cruiser") {
                //$fuel_consumption ="";
                $cruiser = new ships();
                $cruiser->type = $type;
                $cruiser->user_id = $user_id;
                $cruiser->quantity += 1;

                $minerais = 800;

                if ($minerais >= 800) {
                    return Response()->json($cruiser, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "destroyer") {
                //$fuel_consumption ="";
                $destroyer = new ships();
                $destroyer->type = $type;
                $destroyer->user_id = $user_id;

                return Response()->json($destroyer, 201);
            }
        }
    }
    public function Update(Request $request, $type = null)
    {
        $user_id = "1";

        $type = ships::where('user_id', $user_id)->first();

        if ($type == "fighter" || $type == "frigate" || $type == "cruiser" || $type == "destroyer") {
            if ($type == "fighter") {
                $minerais = 50;
                $type->quantity += 1;
                $type->save();

                if ($minerais >= 50) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "frigate") {
                $minerais = 200;
                $type->quantity += 1;
                $type->save();

                if ($minerais >= 200) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "cruiser") {
                $minerais = 800;
                $type->quantity += 1;
                $type->save();

                if ($minerais >= 800) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "destroyer") {
                $minerais = 2000;
                $type->quantity += 1;
                $type->save();

                if ($minerais >= 2000) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
        }
    }
}
