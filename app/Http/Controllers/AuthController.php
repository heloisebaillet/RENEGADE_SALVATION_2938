<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Vérifier les champs d'e-mail et de mot de passe
        if (empty($email) || empty($password)) {
            return response()->json(['message' => 'Email and password are required'], 422);
        }

        // Vérifier les informations d'identification de l'utilisateur dans la base de données
        $user = DB::table('users')->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            // L'authentification réussie
            return response()->json(['message' => 'Connexion réussie', 'token' => 'your_access_token'], 200);
        } else {
            // L'authentification a échoué
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }
    }

    // Dans votre contrôleur d'inscription (RegisterController.php par exemple)
    public function register(Request $request)
    {
        $validate = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
        ]);

        // Créez un nouvel utilisateur dans la base de données
        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return response()->json(['message' => 'User registered successfully'], 200);
    }

    // Dans vos routes (web.php ou api.php)


}
