<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegistrationRequest;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * Generates a 4-digit OTP, stores it hashed, and emails it to the user.
     */
    public function registration(RegistrationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        // Generate a plain 4-digit OTP, hash it before storing
        $plainCode = (string) rand(1000, 9999);

        $user->verification_code         = Hash::make($plainCode);
        $user->verification_code_sent_at = now();
        $user->save();

        // Send OTP to user's email (logs to storage/logs when MAIL_MAILER=log)
        Mail::to($user->email)->send(new VerificationCodeMail($plainCode, $user->name));

        return response()->json([
            'message' => 'Registration successful. A verification code has been sent to your email.',
            'user'    => [
                'email' => $user->email,
            ],
        ], 201);
    }


    /**
     * Verify a user's email with the OTP code.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email'             => 'required|string|email',
            'verification_code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the OTP has expired (10-minute window)
        if (!$user->verification_code_sent_at || $user->verification_code_sent_at->lt(now()->subMinutes(10))) {
            return response()->json(['message' => 'Verification code has expired. Please register again.'], 400);
        }

        // Compare the plain code against the stored hash
        if (!Hash::check($request->verification_code, $user->verification_code)) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        // Mark as verified and clear OTP fields
        $user->email_verified_at         = now();
        $user->verification_code         = null;
        $user->verification_code_sent_at = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Email verified successfully.',
            'user'    => [
                'email' => $user->email,
                'name'  => $user->name,
            ],
            'token' => $token,
        ]);
    }


    /**
     * Log in an existing verified user.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Use first() — not firstOrFail() — so we control the JSON error response
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        if ($user->email_verified_at === null) {
            return response()->json([
                'message' => 'Email not verified. Please verify your email before logging in.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'email' => $user->email,
                'name'  => $user->name,
            ],
            'token' => $token,
        ]);
    }


    /**
     * Log out the currently authenticated user (revoke current token).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}