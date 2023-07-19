<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RessourcesController extends Controller
{
    public function getRessources()
    {
        $userId = Auth::user()->id;


        $user = User::findOrFail($userId);
        $ressources = [
            'ore' => $user->ore,
            'fuel' => $user->fuel,
            'energy' => $user->energy,
        ];

        return response()->json($ressources);
    }

    public function produceRessources()
    {
        $userId = Auth::user()->id;

        $user = User::findOrFail($userId);


        $user->ore += 100;
        $user->fuel += 50;
        $user->save();

        return response()->json(['message' => 'Ressources produced successfully']);
    }
}
