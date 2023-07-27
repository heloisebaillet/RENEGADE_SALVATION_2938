<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgetPasswordController extends Controller
{
    function forgetPassword()
    {
        return redirect()->to(route('forget-password'));
    }

    function forgetPasswordPost(Request $request)
    {
        $request->validate([
            'email' => "required|email|exists:users",
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send("emails.forget-password", ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json(['status' => 'success', 'message' => 'We have sent you an e-mail to reset your password.']);
    }

    function resetPassword($token)
    {
        return redirect('reset-password', compact('token'));
    }

    function resetPasswordPost(Request $request)
    {
        $request->validate([
            'email' => "required|email|exists:users",
            'password' => "required|string|min:4|confirmed",
            'password_confirmation' => "required",
            'token' => "required"
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])->first();

        if (!$updatePassword) {
            return redirect()->to(route('reset.password'))
                ->with('error', 'Invalid');
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_resets')->where([
            'email' => $request->email
        ])->delete();

        return redirect()->to(route('login'))->with('success', 'Password reset successfully');
    }
}