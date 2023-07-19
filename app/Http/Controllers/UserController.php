<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create()
    {
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'firstName' => 'required|string',
            'lastName' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'confpassword' => 'required|same:password',
            'username' => 'required',
            'date_of_birth' => 'required|date|before_or_equal:18 years ago',
            'picture' => 'image|max:2048',
        ]);




        return response()->json(['message' => $validatedData], 201);
    }
}
