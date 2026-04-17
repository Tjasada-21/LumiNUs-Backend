<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'first_name' => 'sometimes|filled|string|max:255',
            'middle_name' => 'sometimes|nullable|string|max:255',
            'last_name' => 'sometimes|filled|string|max:255',
            'phone_number' => 'sometimes|nullable|string|max:50',
            'email' => 'sometimes|filled|string|email|max:255|unique:alumnis,email,' . $alumni->id,
            'date_of_birth' => 'sometimes|filled|date',
            'sex' => 'sometimes|filled|string|max:50',
            'alumni_photo' => 'sometimes|nullable|string|max:2048',
        ]);

        $alumni->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'alumni' => $alumni->fresh(),
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $alumni = $request->user();

        $validated = $request->validate([
            'photo' => 'required|image|max:5120', // max 5MB
        ]);

        $file = $request->file('photo');
        // store in public disk under alumni_photos
        $path = $file->store('alumni_photos', 'public');

        // build public URL
        $url = asset('storage/' . $path);

        // update alumni record
        $alumni->alumni_photo = $url;
        $alumni->save();

        return response()->json([
            'message' => 'Photo uploaded',
            'url' => $url,
            'alumni' => $alumni->fresh(),
        ]);
    }
}