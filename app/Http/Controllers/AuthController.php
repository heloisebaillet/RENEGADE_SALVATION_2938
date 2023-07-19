<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = $request->validate([
            'type_user' => ['required'],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required'],
            'username' => ['required', 'unique:users'],
            'date_of_birth' => ['required', 'date'],
        ]);

        $user = User::create([
            'type_user' => $validate['required'],
            'firstname' => $validate['firstname'],
            'lastname' => $validate['lastname'],
            'email' => $validate['email'],
            'password' => Hash::make($validate['password']),
            'username' => $validate['username'],
            'date_of_birth' => $validate['date_of_birth'],
        ]);

        if ($user) {
            return response()->json(['success' => true], 200);
        } else {
            return response()->json(['success' => false], 500);
        }
    }
}
