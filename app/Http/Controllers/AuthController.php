<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\RegistrationRequest;

class AuthController extends Controller
{
    public function registration(RegistrationRequest $request) 
    {
        $validatedData = $request->validated();
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
            ]);

            //genrate 4 digit verification code
            $verificationCode = rand(1000, 9999);
            $user->verification_code = $verificationCode;
            $user->save();

        return response()->json([
            'user' => [
                'email' => $user->email,
            ],
            // 'token' => $token,
        ]);
    }


    //opt verification
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'verification_code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        //check if the verification code matches
        if ($user->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }


        $user->email_verified_at = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
           'user' => [
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }



    //login
    public function login(Request $request) 
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request['email'])->firstOrFail();

        if($user->email_verified_at === null) {
            return response()->json([
                'message' => 'Email not verified. Please verify your email before logging in.'
            ], 403);
        }

        if (! $user || ! \Hash::check($request['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'token' => $token,
        ]);
    }

}