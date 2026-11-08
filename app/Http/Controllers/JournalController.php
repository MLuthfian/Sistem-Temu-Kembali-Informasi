<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $indexFilter = trim(strtolower($request->query('index', 'all')));
        $yearFilter = $request->query('year', 'all');
        $search = trim(strtolower($request->query('search', '')));

        $query = Journal::query()->orderBy('year', 'desc');

        if ($yearFilter !== 'all') {
            $query->where('year', (int)$yearFilter);
        }

        $journals = $query->get();

        if ($indexFilter !== 'all') {
            $journals = $journals->filter(function ($j) use ($indexFilter) {
                return str_contains(strtolower($j->index_rank), $indexFilter);
            })->values();
        }

        if (!empty($search)) {
            $journals = $journals->filter(function ($j) use ($search) {
                $text = strtolower($j->title . ' ' . $j->authors . ' ' . $j->abstract);
                return str_contains($text, $search);
            })->values();
        }

        return response()->json($journals);
    }
}

