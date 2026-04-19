<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerkController extends Controller
{
    public function index(Request $request)
    {
        $imagesTable = 'images_perks';

        $perks = DB::table('perks')
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'active');
            })
            ->orderByDesc('created_at')
            ->get();

        $images = collect();

        if (Schema::hasTable($imagesTable)) {
            $images = DB::table($imagesTable)
            ->orderBy('created_at')
            ->orderBy('id')
                ->get()
                ->groupBy('perk_id');
        }

        $payload = $perks->map(function ($perk) use ($images) {
            $perksImages = $images->get($perk->id, collect())->map(function ($image) {
                $imagePath = is_string($image->image_path) ? trim($image->image_path) : null;

                return [
                    'id' => $image->id,
                    'image_path' => $imagePath,
                    'image_url' => $imagePath,
                ];
            })->values();

            return [
                'id' => $perk->id,
                'title' => $perk->title,
                'description' => $perk->description,
                'valid_until' => $perk->valid_until,
                'status' => $perk->status ?? 'active',
                'images' => $perksImages,
            ];
        })->values();

        return response()->json([
            'perks' => $payload,
        ]);
    }
}