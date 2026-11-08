<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RAG\GuardrailService;
use App\Services\RAG\VectorStoreService;
use App\Services\RAG\SynthesizerService;
use App\Models\AuditLog;
use App\Models\AppConfig;

class ChatbotController extends Controller
{
    protected GuardrailService $guardrails;
    protected VectorStoreService $vectorStore;
    protected SynthesizerService $synthesizer;

    public function __construct(
        GuardrailService $guardrails,
        VectorStoreService $vectorStore,
        SynthesizerService $synthesizer
    ) {
        $this->guardrails = $guardrails;
        $this->vectorStore = $vectorStore;
        $this->synthesizer = $synthesizer;
    }

    public function chat(Request $request)
    {
        $query = trim($request->input('query', ''));
        $sessionId = $request->input('session_id', 'anon_' . bin2hex(random_bytes(4)));
        $clarification = $request->input('clarification', null);

        if (empty($query)) {
            return response()->json(['error' => 'Pertanyaan tidak boleh kosong.'], 400);
        }

        // 1. Deteksi Gawat Darurat (Red Flags)
        $em = $this->guardrails->checkEmergency($query);
        if ($em['is_emergency']) {
            AuditLog::create([
                'session_id' => $sessionId,
                'query' => $query,
                'is_emergency' => true,
                'is_forbidden' => false,
                'is_fallback' => false,
                'category' => 'EMERGENCY_' . $em['condition'],
                'top_score' => 1.0,
                'citations_count' => 0,
                'response_preview' => mb_substr($em['message'], 0, 200)
            ]);

            return response()->json([
                'is_emergency' => true,
                'condition' => $em['condition'],
                'answer' => $em['message'],
                'hotlines' => $em['hotlines'],
                'immediate_actions' => $em['immediate_actions'],
                'citations' => [],
                'score' => 1.0
            ]);
        }

        // 2. Deteksi Permintaan Terlarang (Dosis Obat / Diagnosis Definitif / Luar Topik)
        $fb = $this->guardrails->checkForbiddenRequests($query);
        if ($fb['is_forbidden']) {
            AuditLog::create([
                'session_id' => $sessionId,
                'query' => $query,
                'is_emergency' => false,
                'is_forbidden' => true,
                'is_fallback' => false,
                'category' => $fb['type'],
                'top_score' => 0.0,
                'citations_count' => 0,
                'response_preview' => mb_substr($fb['message'], 0, 200)
            ]);

            return response()->json([
                'is_emergency' => false,
                'is_forbidden' => true,
                'forbidden_type' => $fb['type'],
                'answer' => $fb['message'],
                'citations' => [],
                'score' => 0.0
            ]);
        }

        // 3. Retrieval Top-K dari Vector Store
        $minThreshold = (float)(AppConfig::where('key', 'min_similarity_threshold')->value('value') ?? 0.20);
        $chunks = $this->vectorStore->search($query, 4, $minThreshold);
        $topScore = !empty($chunks) ? $chunks[0]['score'] : 0.0;

        // 4. Threshold Evaluation: Jika skor di bawah ambang, kirim Fallback
        if (empty($chunks) || $topScore < $minThreshold) {
            $fallbackMsg = AppConfig::where('key', 'fallback_message')->value('value') ?: "Informasi resmi belum tersedia.";

            AuditLog::create([
                'session_id' => $sessionId,
                'query' => $query,
                'is_emergency' => false,
                'is_forbidden' => false,
                'is_fallback' => true,
                'category' => 'FALLBACK_UNAVAILABLE',
                'top_score' => $topScore,
                'citations_count' => 0,
                'response_preview' => mb_substr($fallbackMsg, 0, 200)
            ]);

            return response()->json([
                'is_emergency' => false,
                'is_forbidden' => false,
                'is_fallback' => true,
                'answer' => $fallbackMsg,
                'citations' => [],
                'score' => $topScore
            ]);
        }

        // 5. Sintesis Jawaban dengan Grounding Sitasi (Format 5 Bagian)
        $synth = $this->synthesizer->synthesize($query, $chunks, $clarification);
        $answerText = $synth['answer'] ?? '';
        $citations = $synth['citations'] ?? [];

        // 6. Validasi Keluaran (Nol angka dosis obat keras, penegasan disclaimer)
        $validatedAnswer = $this->guardrails->validateOutput($answerText, $citations);

        // 7. Log Anonim (UU PDP No. 27/2022)
        AuditLog::create([
            'session_id' => $sessionId,
            'query' => $query,
            'is_emergency' => false,
            'is_forbidden' => false,
            'is_fallback' => false,
            'category' => 'GROUNDED_RAG',
            'top_score' => $topScore,
            'citations_count' => count($citations),
            'response_preview' => mb_substr($validatedAnswer, 0, 200)
        ]);

        return response()->json([
            'is_emergency' => false,
            'is_forbidden' => false,
            'is_fallback' => false,
            'answer' => $validatedAnswer,
            'citations' => $citations,
            'score' => $topScore,
            'chunks_used' => count($chunks)
        ]);
    }
}

