<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disease;

class DiseaseController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'all');
        $search = trim(strtolower($request->query('search', '')));

        $query = Disease::query();

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $diseases = $query->get();

        if (!empty($search)) {
            $diseases = $diseases->filter(function ($d) use ($search) {
                $text = strtolower($d->title . ' ' . $d->summary . ' ' . $d->symptoms);
                return str_contains($text, $search);
            })->values();
        }

        return response()->json($diseases);
    }

    public function show(string $slug)
    {
        $disease = Disease::where('slug', $slug)->first();
        if (!$disease) {
            return response()->json(['error' => 'Topik penyakit tidak ditemukan.'], 404);
        }
        return response()->json($disease);
    }
}

