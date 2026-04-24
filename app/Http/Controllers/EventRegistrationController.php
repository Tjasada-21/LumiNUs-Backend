<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $alumni = $request->user();

        if (!$alumni) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $registrations = EventRegistration::query()
            ->where('alumni_id', $alumni->id)
            ->get(['id', 'event_id', 'rsvp_date', 'registration_confirmation', 'status', 'created_at']);

        return response()->json([
            'registrations' => $registrations,
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $alumni = $request->user();

        if (!$alumni) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->validate([
            'privacy_consent' => 'required|accepted',
            'attendance_consent' => 'required|accepted',
            'email' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
        ]);

        $alreadyRegistered = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('alumni_id', $alumni->id)
            ->exists();

        if ($alreadyRegistered) {
            throw ValidationException::withMessages([
                'event_id' => ['You are already registered for this event.'],
            ]);
        }

        try {
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'alumni_id' => $alumni->id,
                'rsvp_date' => now()->toDateString(),
                'registration_confirmation' => true,
                'status' => 1,
            ]);
        } catch (QueryException $exception) {
            return response()->json([
                'message' => 'Unable to save your registration right now.',
            ], 500);
        }

        return response()->json([
            'message' => 'Event registration saved successfully.',
            'registration' => $registration,
        ], 201);
    }
}