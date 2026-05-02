<?php

namespace App\Http\Controllers;

use App\Models\TracerForms;
use Illuminate\Http\JsonResponse;

class TracerFormController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = TracerForms::query()
            ->select([
                'id',
                'admin_id',
                'form_title',
                'form_header',
                'form_description',
                'status',
                'created_at',
                'updated_at',
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'tracer_forms' => $forms,
        ]);
    }
}
