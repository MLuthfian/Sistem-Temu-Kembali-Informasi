<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Chunk;
use App\Models\EmergencyRule;
use App\Models\AuditLog;
use App\Models\Disease;
use App\Models\Journal;
use App\Models\AppConfig;
use App\Services\RAG\VectorStoreService;
use App\Services\RAG\BenchmarkEvaluatorService;

class CuratorController extends Controller
{
    protected VectorStoreService $vectorStore;
    protected BenchmarkEvaluatorService $evaluator;

    public function __construct(
        VectorStoreService $vectorStore,
        BenchmarkEvaluatorService $evaluator
    ) {
        $this->vectorStore = $vectorStore;
        $this->evaluator = $evaluator;
    }

    public function getDocuments()
    {
        return response()->json(Document::orderBy('id', 'desc')->get());
    }

    public function addDocument(Request $request)
    {
        $title = trim($request->input('title', ''));
        $content = trim($request->input('content', ''));
        $publisher = $request->input('publisher', 'Kementerian Kesehatan RI');
        $year = (int)$request->input('year', 2024);
        $url = $request->input('url', '#');
        $topic = $request->input('topic', 'Penyakit Ringan');
        $layer = $request->input('layer', 'Lapisan 1 (Kemenkes)');
        $license = $request->input('license', 'Hak Cipta Kemenkes RI');

        if (empty($title) || empty($content)) {
            return response()->json(['error' => 'Judul dan konten dokumen wajib diisi.'], 400);
        }

        $doc = Document::create([
            'title' => $title,
            'publisher' => $publisher,
            'year' => $year,
            'url' => $url,
            'topic' => $topic,
            'layer' => $layer,
            'license' => $license,
            'verified_date' => date('Y-m-d'),
            'status' => 'Terverifikasi',
            'content' => $content
        ]);

        // Auto chunking into ~70 words with overlap
        $words = explode(' ', $content);
        $chunkSize = 70;
        $overlap = 15;
        $start = 0;
        $totalWords = count($words);

        while ($start < $totalWords) {
            $end = min($start + $chunkSize, $totalWords);
            $chunkWords = array_slice($words, $start, $end - $start);
            $chunkText = implode(' ', $chunkWords);

            Chunk::create([
                'doc_id' => $doc->id,
                'judul_dokumen' => $doc->title,
                'sumber' => $doc->publisher,
                'url' => $doc->url,
                'topik' => $doc->topic,
                'lapisan' => $doc->layer,
                'lisensi' => $doc->license,
                'tahun' => $doc->year,
                'text' => $chunkText,
            ]);

            if ($end >= $totalWords) break;
            $start += ($chunkSize - $overlap);
        }

        // Re-index memory
        $this->vectorStore->loadIndex();

        return response()->json([
            'message' => 'Dokumen berhasil ditambahkan & diindeks ke RAG!',
            'doc_id' => $doc->id
        ]);
    }

    public function reindex()
    {
        $tStart = microtime(true);
        $this->vectorStore->loadIndex();
        $durationMs = round((microtime(true) - $tStart) * 1000, 2);

        $stats = $this->vectorStore->getStats();
        $stats['duration_ms'] = $durationMs;

        return response()->json([
            'message' => 'Vector store berhasil di-reindex!',
            'stats' => $stats
        ]);
    }

    public function getEmergencyRules()
    {
        return response()->json(EmergencyRule::orderBy('id', 'asc')->get());
    }

    public function addEmergencyRule(Request $request)
    {
        $keyword = trim(strtolower($request->input('keyword', '')));
        $action = trim($request->input('action', 'Arahkan ke IGD / 119'));
        $severity = $request->input('severity', 'CRITICAL');

        if (empty($keyword)) {
            return response()->json(['error' => 'Kata kunci wajib diisi.'], 400);
        }

        $rule = EmergencyRule::updateOrCreate(
            ['keyword' => $keyword],
            ['severity' => $severity, 'action' => $action]
        );

        return response()->json([
            'message' => "Rule '{$keyword}' berhasil disimpan tanpa perlu restart sistem!",
            'rule' => $rule
        ]);
    }

    public function getLogs(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $query = AuditLog::query()->orderBy('id', 'desc')->limit(100);

        if ($filter === 'emergency') {
            $query->where('is_emergency', true);
        } elseif ($filter === 'forbidden') {
            $query->where('is_forbidden', true);
        } elseif ($filter === 'fallback') {
            $query->where('is_fallback', true);
        }

        return response()->json($query->get());
    }

    public function evaluate()
    {
        $threshold = (float)(AppConfig::where('key', 'min_similarity_threshold')->value('value') ?? 0.20);
        $report = $this->evaluator->runEvaluation($threshold);
        return response()->json($report);
    }

    public function getStats()
    {
        $vStats = $this->vectorStore->getStats();

        return response()->json([
            'documents' => Document::count(),
            'chunks' => Chunk::count(),
            'journals' => Journal::count(),
            'diseases' => Disease::count(),
            'total_queries' => AuditLog::count(),
            'emergency_triggers' => AuditLog::where('is_emergency', true)->count(),
            'vocab_size' => $vStats['vocab_size'] ?? 0,
            'layer1_chunks' => $vStats['layer1_chunks'] ?? 0,
            'layer2_chunks' => $vStats['layer2_chunks'] ?? 0
        ]);
    }
}

