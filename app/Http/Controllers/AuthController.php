<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // L'authentification réussie
            return response()->json(['message' => 'Connexion réussie'], 200);
        } else {
            // L'authentification a échoué
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }
    }
}
