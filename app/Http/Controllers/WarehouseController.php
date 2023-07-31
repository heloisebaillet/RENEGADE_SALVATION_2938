<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{

    // création d'un entrepôt si l'utilisateur a suffisamment de minerai (500)
    // public function create()
    // {
    //     $user_id = Auth::user()->id;
    //     $verify = Warehouse::where('user_id', $user_id)->get();
    //     if ($verify != "[]") {

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthorized',
    //             'data' => $verify,
    //         ], 401);
    //     } else {

    //         $warehouse = new Warehouse();
    //         $warehouse->user_id = $user_id;
    //         $warehouse->quantity = 2;
    //         $warehouse->save();
    //         return response()->json([
    //             'status' => 'success',
    //         ], 201);
    //     }
    // }

    // lecture des détails de l'entrepôts 
    public function read()
    {
        $user_id = Auth::user()->id;
        // si le paramètre n'est pas vide, choix du type de batiment
        $warehouseDetails = Warehouse::select('quantity')->where('user_id', $user_id)->first();
        return response()->json($warehouseDetails, 200);
    }
    public function update()
    {
        $user_id = Auth::user()->id;
        $ressources_id = Ressources::select('quantity')->where('user_id', $user_id)->where('type', 'ore')->first();
        $warehouse = Warehouse::where('user_id', $user_id)->first();

        if ($ressources_id->quantity >= 500) {
            $warehouse->quantity = $warehouse->quantity +1;
            $warehouse->save();
            $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
            $ore->quantity = $ore->quantity - 500;
            $ore->save();
            return response()->json([
                'status' => 'success',
            ], 201);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => ' no ore',
            ], 400);
        }
    }

    public function delete()
    {
        $user_id = Auth::user()->id;
        $warehouse = Warehouse::where('user_id', $user_id)->first();
        // on ne peux avoir moins de 2 warhouses (les 2 offertes)
        if ($warehouse->quantity >= 3) {
            $warehouse->user_id = $user_id;
            $warehouse->quantity = $warehouse->quantity - 1;
            $warehouse->save();
            $ore = Ressources::where('user_id', $user_id)->where('type', 'ore')->first();
            $ore->quantity = $ore->quantity - 500;
            $ore->save();
            return response()->json([
                'status' => 'success',
            ], 205);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'cant have less 2 warehouses',
            ], 400);
        }
    }
}
