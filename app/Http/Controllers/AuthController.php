<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validate the incoming data from React Native
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|string',
            'year_graduated' => 'required|date',
            'student_id_number' => 'required|string|unique:alumnis,student_id_number',
            'email' => 'required|string|email|max:255|unique:alumnis,email',
            'password' => 'required|string|min:8', // We will hash this before saving
        ]);

        // 2. Create the new Alumni in Supabase
        $alumni = Alumni::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name, // Optional
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'sex' => $request->sex,
            'year_graduated' => $request->year_graduated,
            'student_id_number' => $request->student_id_number,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), // Securely hash the password
        ]);

        // 3. Generate the API Token for the mobile app
        $token = $alumni->createToken('mobile-app-token')->plainTextToken;

        // 4. Return the success response
        return response()->json([
            'alumni' => $alumni,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        // 1. Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Find the Alumni by email
        $alumni = Alumni::where('email', $request->email)->first();

        // 3. Check if Alumni exists and password matches
        if (!$alumni || !Hash::check($request->password, $alumni->password_hash)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 4. Generate a new API Token
        $token = $alumni->createToken('mobile-app-token')->plainTextToken;

        // 5. Return the alumni data and token
        return response()->json([
            'alumni' => $alumni,
            'token' => $token
        ]);
    }
}