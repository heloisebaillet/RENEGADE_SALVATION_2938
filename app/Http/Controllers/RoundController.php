<?php

namespace App\Http\Controllers;

use App\Models\Round;
use Illuminate\Support\Facades\Auth;

class RoundController extends Controller
{
    public function read()
    {
        $user_id = Auth::user()->id;
        $uuidObject = Round::select('uuid')->where('user_id', $user_id)->first();

        if (!$uuidObject) {
    
            return response()->json(['error' => 'No Battle Found'], 404);
        }

        $uuid = $uuidObject->uuid;

        $rounds = Round::select('planetary_system_name', 'user_id','is_defender','nb_fighter','nb_frigate','nb_cruiser','nb_destroyer','nb_round','rounds.created_at')
        ->where('uuid', $uuid)
        ->leftJoin('users', 'users.id', '=', 'rounds.user_id')
        ->get();



        return response()->json([
            'round' => $rounds
        ], 200);
    }
}

