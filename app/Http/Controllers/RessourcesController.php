<?php

namespace App\Http\Controllers;

use App\Models\Ressources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RessourcesController extends Controller
{
    public function create()
    {
        $user_id = 1;
        $verify = Ressources::where('user_id', $user_id)->get();
        

        if ($verify != "[]") {

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => $verify,
            ], 401);
        }
        else {
            $offer1 = new Ressources();
            $offer1->user_id = $user_id;
            $offer1->type = "ore";
            $offer1->quantity= 1000;
            $offer1->save();
            $offer2 = new Ressources();
            $offer2->user_id = $user_id;
            $offer2->type = "fuel";
            $offer2->quantity= 1000;
            $offer2->save();
            $offer3 = new Ressources();
            $offer3->user_id = $user_id;
            $offer3->type = "energy";
            $offer3->quantity= 20;
            $offer3->save();
            return Response()->json(201);

        }
    }

    public function read(Request $request)
    {
        $user_id = Auth::user()->id;
        // $user = User::findOrFail($userId);
        $showressources = Ressources::where('user_id', $user_id)->get();
        return response()->json($showressources, 201);
    }

    public function update(Request $request, $type, $operation, $qty)
    {
    }
}
