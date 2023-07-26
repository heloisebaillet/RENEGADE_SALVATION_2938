<?php

namespace App\Http\Controllers;

use App\Models\Ship;
use App\Models\Shipyard;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;

class ShipyardController extends Controller
{
    public function Read()
    {
        $user_id= Auth::id();

        $shipyard= Shipyard::where('user_id', $user_id)->get();
        $fighter = Ship::where('user_id', $user_id)->where('type', 'fighter')->first();
        $full_shipyards_fighters = Shipyard::where('user_id', $user_id)->where('type', 'fighter')->whereRaw('DATE_ADD(updated_at, INTERVAL 1 HOUR) < NOW()')->get();
        $frigate = Ship::where('user_id', $user_id)->where('type', 'frigate')->first();
        $full_shipyards_frigates = Shipyard::where('user_id', $user_id)->where('type', 'frigate')->whereRaw('DATE_ADD(updated_at, INTERVAL 2 HOUR) < NOW()')->get();
        $cruiser = Ship::where('user_id', $user_id)->where('type', 'cruiser')->first();
        $full_shipyards_crusiers = Shipyard::where('user_id', $user_id)->where('type', 'cruiser')->whereRaw('DATE_ADD(updated_at, INTERVAL 4 HOUR) < NOW()')->get();
        $destroyer = Ship::where('user_id', $user_id)->where('type', 'destroyer')->first();
        $full_shipyards_destroyers = Shipyard::where('user_id', $user_id)->where('type', 'destroyer')->whereRaw('DATE_ADD(updated_at, INTERVAL 8 HOUR) < NOW()')->get();

        foreach ($full_shipyards_fighters as $full_shipyards_fighter){
            $full_shipyards_fighter->type = NULL;
            $full_shipyards_fighter->save();
            $fighter->quantity = $fighter->quantity +1;
            $fighter->save();
        }
        foreach ($full_shipyards_frigates as $full_shipyards_frigate){
            $full_shipyards_frigate->type = NULL;
            $full_shipyards_frigate->save();
            $frigate->quantity = $frigate->quantity +1;
            $frigate->save();
        }
        foreach ($full_shipyards_crusiers as $full_shipyards_crusier){
            $full_shipyards_crusier->type = NULL;
            $full_shipyards_crusier->save();
            $cruiser->quantity = $cruiser->quantity +1;
            $cruiser->save();
        }
        foreach ($full_shipyards_destroyers as $full_shipyards_destroyer){
            $full_shipyards_destroyer->type = NULL;
            $full_shipyards_destroyer->save();
            $destroyer->quantity = $destroyer->quantity +1;
            $destroyer->save();
        }
        
            
        return Response()->json($shipyard, 200);
    }
    public function Vacant()
    {
        $user_id= Auth::id(); 
        $shipyard= Shipyard::where('user_id', $user_id)->whereNull('type')->first();
        if ($shipyard == ""){
            return Response()->json(['success' => 'false'], 400);
        }
        else{
        return Response()->json($shipyard, 200);}
    }
}
