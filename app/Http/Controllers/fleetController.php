<?php


namespace App\Http\Controllers;

use App\Models\Fleet;
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
                $fighter = new Fleet();
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
                $frigate = new Fleet();
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
                $cruiser = new Fleet();
                $cruiser->type = $type;
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
                $destroyer = new Fleet();
                $destroyer->type = $type;
                $destroyer->quantity += 1;

                $minerais = 2000;


                if ($minerais >= 2000) {
                    return Response()->json($destroyer, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
        }
    }
}
