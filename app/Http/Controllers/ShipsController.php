<?php


namespace App\Http\Controllers;


use App\Models\Ressources;
use App\Models\Ships;

use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ShipsController extends Controller
{


    public function Read()
    {
        $user_id = Auth::User()->id;
        $fighter = Ships::select('quantity')->where('user_id', $user_id)->where('type', 'fighter')->get();
        $frigate = Ships::select('quantity')->where('user_id', $user_id)->where('type', 'frigate')->get();
        $cruiser = Ships::select('quantity')->where('user_id', $user_id)->where('type', 'cruiser')->get();
        $destroyer = Ships::select('quantity')->where('user_id', $user_id)->where('type', 'destroyer')->get();
        $response = [
            'fighter' => $fighter,
            'frigate' => $frigate,
            'cruiser' => $cruiser,
            'destroyer' => $destroyer,
        ];
        return response()->json($response, 200);
    }
    public function Update(Request $request, $type = null, $operand)
    {
        $user_id = Auth::User()->id;
        $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
        $update = Ships::where('user_id', $user_id)->where('type', $type)->first();

        if ($operand === "add") {
            if ($type == "fighter" && $ore->quantity >= 50) {
                //$fuel_consumption ="";
                $update->type = "fighter";
                $update->quantity = $update->quantity + 1;
                $update->save();
                $ore->quantity = $ore->quantity - 50;
                $ore->save();

                return Response()->json($update, 201);
            }
            if ($type == "frigate" && $ore->quantity >= 200) {
                //$fuel_consumption ="";
                $update->type = "frigate";
                $update->quantity = $update->quantity + 1;
                $update->save();
                $ore->quantity = $ore->quantity - 200;
                $ore->save();

                return Response()->json($update, 201);
            }
            if ($type == "cruiser" && $ore->quantity >= 800) {
                //$fuel_consumption ="";
                $update->type = "cruiser";
                $update->quantity = $update->quantity + 1;
                $update->save();
                $ore->quantity = $ore->quantity - 800;
                $ore->save();

                return Response()->json($update, 201);
            }
            if ($type == "destroyer" && $ore->quantity >= 2000) {
                //$fuel_consumption ="";
                $update->type = "destroyer";
                $update->quantity = $update->quantity + 1;
                $update->save();
                $ore->quantity = $ore->quantity - 2000;
                $ore->save();

                return Response()->json($update, 201);
            } else {
                return Response()->json(['success' => 'false'], 400);
            }
        } else if ($operand === "remove") {

            if ($type == "fighter" && $update->quantity >= 1) {
                //$fuel_consumption ="";
                $update->type = "fighter";
                $update->quantity = $update->quantity - 1;
                $update->save();
                return Response()->json($update, 201);
            }
            if ($type == "frigate" && $update->quantity >= 1) {
                //$fuel_consumption ="";
                $update->type = "frigate";
                $update->quantity = $update->quantity - 1;
                $update->save();
                return Response()->json($update, 201);
            }
            if ($type == "cruiser" && $update->quantity >= 1) {
                //$fuel_consumption ="";
                $update->type = "cruiser";
                $update->quantity = $update->quantity - 1;
                $update->save();

                return Response()->json($update, 201);
            }
            if ($type == "destroyer" && $update->quantity >= 1) {
                //$fuel_consumption ="";
                $update->type = "destroyer";
                $update->quantity = $update->quantity - 1;
                $update->save();
                

                return Response()->json($update, 201);
            } else {
                return Response()->json(['success' => 'false'], 400);
            }
        }
    }
}
