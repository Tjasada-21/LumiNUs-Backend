<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TracerFormController extends Controller
{
    public function index(): JsonResponse
    {
        $forms = DB::table('tracer_forms')
            ->select([
                'id',
                'form_title',
                'form_description',
                'form_header',
                'is_active',
                'created_at',
                'updated_at',
            ])
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'tracer_forms' => $forms,
        ]);
    }
}
