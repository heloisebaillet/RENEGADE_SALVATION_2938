<?php


namespace App\Http\Controllers;

use App\Models\ships;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ShipsController extends Controller
{

    public function create($type = null)
    {
        $user_id = Auth::user()->id;
        if ($type == "cruiser" || $type == "destroyer" || $type == "fighter" || $type == "frigate") {

            if ($type == "fighter") {
                $fighter = new Ships();
                $fighter->user_id = $user_id;
                $fighter->type = $type;
                $fighter->quantity += 1;

                $ore = 50;

                if ($ore >= 50) {
                    return Response()->json($fighter, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "frigate") {
                $frigate = new ships();
                $frigate->user_id = $user_id;
                $frigate->type = $type;
                $frigate->quantity += 1;

                $ore = 200;

                if ($ore >= 200) {
                    return Response()->json($frigate, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "cruiser") {
                $cruiser = new ships();
                $cruiser->type = $type;
                $cruiser->user_id = $user_id;
                $cruiser->quantity += 1;

                $ore = 800;

                if ($ore >= 800) {
                    return Response()->json($cruiser, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }

            if ($type == "destroyer") {
                $destroyer = new ships();
                $destroyer->type = $type;
                $destroyer->user_id = $user_id;

                $ore = 2000;

                if ($ore >= 2000) {
                    return Response()->json($destroyer, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
        }
    }

    public function Update(Request $request, $type = null)
    {
        $user_id = "1";

        $type = ships::where('user_id', $user_id)->first();

        if ($type == "fighter" || $type == "frigate" || $type == "cruiser" || $type == "destroyer") {
            if ($type == "fighter") {
                $ore = 50;
                $type->quantity += 1;
                $type->save();

                if ($ore >= 50) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "frigate") {
                $ore = 200;
                $type->quantity += 1;
                $type->save();

                if ($ore = 200) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "cruiser") {
                $ore = 800;
                $type->quantity += 1;
                $type->save();

                if ($ore >= 800) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
            if ($type == "destroyer") {
                $ore = 2000;
                $type->quantity += 1;
                $type->save();

                if ($ore >= 2000) {
                    return Response()->json($type, 201);
                } else {
                    return Response()->json(['success' => 'false'], 400);
                }
            }
        }
    }
}
