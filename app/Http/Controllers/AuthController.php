<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PlanetarySystem;
use App\Models\Ressources;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'

        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'date_of_birth' => $request->date_of_birth,
            'planetary_system_name' => $request->name
        ]);
        Auth::login($user);
        $user_id = Auth::user()->id;
        // création de la planetary system 
        $x_coord = random_int(1, 999);
        $y_coord = random_int(1, 999);
        $verify = PlanetarySystem::where('x_coord', $x_coord)->where('y_coord', $y_coord)->get();
        if ($verify != "") {
            $planetary_system = new PlanetarySystem();
            $planetary_system->user_id = $user_id;
            $planetary_system->x_coord = $x_coord;
            $planetary_system->y_coord = $y_coord;
            $planetary_system->save();
        } else {
            $x_coord = $x_coord = (rand(1, 999));
            $y_coord = (rand(1, 999));
            $planetary_system = new PlanetarySystem();
            $planetary_system->user_id = $user_id;
            $planetary_system->x_coord = $x_coord;
            $planetary_system->y_coord = $y_coord;
            $planetary_system->save();
        }
        // Création des deux warehouses
        $warehouse = new Warehouse();
        $warehouse->user_id = $user_id;
        $warehouse->quantity = 2;
        $warehouse->save();

        // Création des ressources offertes
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

        return response()->json([
            'status' => 'success',
        ], 201);
    }

    public function logout()
    {
        Auth::logout();

        if (!Auth::check()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
