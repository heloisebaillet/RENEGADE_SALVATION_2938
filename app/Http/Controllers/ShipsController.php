<?php


namespace App\Http\Controllers;


use App\Models\Ressources;
use App\Models\Ship;
use App\Models\Shipyard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipsController extends Controller
{


    public function Read()
    {
        $user_id = Auth::User()->id;
        $fighter = Ship::where('user_id', $user_id)->where('type', 'fighter')->get();
        $frigate = Ship::where('user_id', $user_id)->where('type', 'frigate')->get();
        $cruiser = Ship::where('user_id', $user_id)->where('type', 'cruiser')->get();
        $destroyer = Ship::where('user_id', $user_id)->where('type', 'destroyer')->get();


        $response = [
            'fighter' => $fighter,
            'frigate' => $frigate,
            'cruiser' => $cruiser,
            'destroyer' => $destroyer,
        ];
        return response()->json($response, 200);
    }
    public function Update(Request $request, $type = null, $operand = null, $nbr_minus = null)
    {
        $user_id = Auth::User()->id;
        $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
        $update = Ship::where('user_id', $user_id)->where('type', $type)->first();
        $shipyard = Shipyard::where('user_id', $user_id)->whereNull('type')->first();

        if ($operand === "add") {
            if ($type == "fighter" && $ore->quantity >= 50 && $shipyard->type === null) {
                $shipyard->type = "fighter";
                $shipyard->save();
                $ore->quantity = $ore->quantity - 50;
                $ore->save();
                $update = $shipyard;
                return Response()->json($update, 201);
            }
            else {
                return Response()->json(['success' => 'false'], 400);
            }
            if ($type == "frigate" && $ore->quantity >= 200 && $shipyard->type === null) {
                $shipyard->type = "frigate";
                $shipyard->save();
                $ore->quantity = $ore->quantity - 200;
                $ore->save();
                $update = $shipyard;
                return Response()->json($update, 201);
            }
            else {
                return Response()->json(['success' => 'false'], 400);
            }
            if ($type == "cruiser" && $ore->quantity >= 800 && $shipyard->type === null) {
                $shipyard->type = "cruiser";
                $shipyard->save();
                $ore->quantity = $ore->quantity - 800;
                $ore->save();
                $update = $shipyard;
                return Response()->json($update, 201);
            }
            else {
                return Response()->json(['success' => 'false'], 400);
            }
            if ($type == "destroyer" && $ore->quantity >= 2000 && $shipyard->type === null) {
                $shipyard->type = "destroyer";
                $shipyard->save();
                $ore->quantity = $ore->quantity - 2000;
                $ore->save();
                $update = $shipyard;
                return Response()->json($update, 201);
            } else {
                return Response()->json(['success' => 'false'], 400);
            }
        } else if ($operand === "remove") {

            if ($type == "fighter" && $update->quantity >= 1) {
                $update->type = "fighter";
                $update->quantity = $update->quantity - $nbr_minus;
                $update->save();
                return Response()->json($update, 201);
            }
            if ($type == "frigate" && $update->quantity >= 1) {
                $update->type = "frigate";
                $update->quantity = $update->quantity - $nbr_minus;
                $update->save();
                return Response()->json($update, 201);
            }
            if ($type == "cruiser" && $update->quantity >= 1) {
                $update->type = "cruiser";
                $update->quantity = $update->quantity - $nbr_minus;
                $update->save();

                return Response()->json($update, 201);
            }
            if ($type == "destroyer" && $update->quantity >= 1) {
                $update->type = "destroyer";
                $update->quantity = $update->quantity - $nbr_minus;
                $update->save();


                return Response()->json($update, 201);
            } else {
                return Response()->json(['success' => 'false'], 400);
            }
        }
    }
}
