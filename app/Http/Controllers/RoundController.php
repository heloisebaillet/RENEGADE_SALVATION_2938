<?php

namespace App\Http\Controllers;

use App\Models\Round;
use Illuminate\Support\Facades\Auth;

class RoundController extends Controller
{
    public function read()
    {
        $user_id = Auth::user()->id;
       
        $round = Round::where('user_id', $user_id)->get();
  

        return response()->json([
            'round' => $round
        ], 200);
    }
}

