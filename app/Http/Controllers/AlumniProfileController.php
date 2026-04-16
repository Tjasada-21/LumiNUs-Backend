<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlumniProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json([
            'alumni' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $alumni = $request->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'email' => 'required|string|email|max:255|unique:alumnis,email,' . $alumni->id,
            'date_of_birth' => 'required|date',
            'sex' => 'required|string|max:50',
            'alumni_photo' => 'nullable|string|max:2048',
        ]);

        $alumni->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'alumni' => $alumni->fresh(),
        ]);
    }
}