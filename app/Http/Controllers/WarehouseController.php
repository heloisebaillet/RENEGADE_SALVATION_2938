<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

//use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{

    // création d'un entrepôt si l'utilisateur a suffisamment de minerai (500)
    public function create()
    {
        $user_id = Auth::user()->id;
        // à implémenter lorsque RessourcesController sera fait
        $ressources_id = Ressources::class()->type;
        $capacity = '500';

        if ($ressources_id == 'ore' && $ressources_id->ore >= '500') {
            $warehouse = new Warehouse();
            $warehouse->user_id = $user_id;
            $warehouse->type = $ressources_id;
            $warehouse->capacity = $capacity;
            $warehouse->save();
            return Response()->json('Warehouse in construction!');
        }
    }

    // lecture des détails de l'entrepôts 
    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        // si le paramètre n'est pas vide, choix du type de batiment
        $warehouseDetails = Warehouse::where('user_id', $user_id)->get();
        return response()->json($warehouseDetails, 200);
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        // à arranger plus tard
        return redirect()->route('yourempire')->with('success', '✔️ Warehouse successfully deleted.');;
    }
}
