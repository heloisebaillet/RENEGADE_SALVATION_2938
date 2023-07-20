<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RessourcesController extends Controller
{
    public function create()
    {
        $user_id = Auth::user()->id;
        $verify = Ressources::where('user_id', $user_id)->get();


        if ($verify != "[]") {

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => $verify,
            ], 401);
        } else {
            $offer1 = new Ressources();
            $offer1->user_id = $user_id;
            $offer1->type = "ore";
            $offer1->quantity = 1000;
            $offer1->save();
            $offer2 = new Ressources();
            $offer2->user_id = $user_id;
            $offer2->type = "fuel";
            $offer2->quantity = 1000;
            $offer2->save();
            $offer3 = new Ressources();
            $offer3->user_id = $user_id;
            $offer3->type = "energy";
            $offer3->quantity = 20;
            $offer3->save();
            return Response()->json(['success' => 'true'], 201);
        }
    }

    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        $showressources = Ressources::where('user_id', $user_id)->get();
        return response()->json($showressources, 201);
    }

    public function update(Request $request, $type, $operation, $qty)
    {
        $user_id = Auth::user()->id;
        $showwarehouse = Warehouse::select('quantity')->where('user_id', $user_id)->first();
        $showressources = Ressources::select('quantity')->where('user_id', $user_id)->where('type', $type)->first();
        $capacity = ($showwarehouse->quantity * 500);
        $ressources = ($showressources == null ? 0 : $showressources->quantity);
        $capacity_rest = $capacity - $ressources;
        $qty = intval($qty);
        $total_rest = $capacity_rest - $qty;
        if ($operation == "add") {
            if ($total_rest >= 0) {
                $update = Ressources::where('user_id', $user_id)->where('type', $type)->first();
                $update->quantity = $update->quantity + $qty;
                $update->save();
                return response()->json($update, 201);
            } else {
                $update = Ressources::where('user_id', $user_id)->where('type', $type)->first();
                $update->quantity = $capacity;
                $update->save();
                return response()->json([
                    'status' => 'error',
                    'message' => ' warehouse full',
                ], 400);
            }
        } else if ($operation == "remove") {
            if ($ressources >= $qty) {
                $update = Ressources::where('user_id', $user_id)->where('type', $type)->first();
                $update->quantity = $update->quantity - $qty;
                $update->save();
                return response()->json($update, 201);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => ' warehouse empty',
                ], 400);
            }
        }
    }
}
