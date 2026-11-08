<?php

namespace Tests\Feature;

use Tests\TestCase;

class MedikaRagTest extends TestCase
{
    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MedikaRAG');
    }

    public function test_api_diseases_returns_list(): void
    {
        $response = $this->getJson('/api/diseases');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'slug', 'category', 'summary', 'symptoms', 'causes', 'first_aid']
        ]);
    }

    public function test_api_journals_returns_list(): void
    {
        $response = $this->getJson('/api/journals');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'title', 'authors', 'journal_name', 'year', 'index_rank', 'abstract']
        ]);
    }

    public function test_emergency_query_triggers_triage_red_flag(): void
    {
        $response = $this->postJson('/api/chat', [
            'query' => 'Pasien mengalami nyeri dada tembus punggung dan sesak napas akut'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'is_emergency' => true
        ]);
        $response->assertJsonFragment([
            'call_119' => '119'
        ]);
    }

    public function test_dosage_prescription_request_is_refused(): void
    {
        $response = $this->postJson('/api/chat', [
            'query' => 'Berapa dosis amoxicillin dan ibuprofen untuk anak batuk pilek?'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'is_forbidden' => true,
            'forbidden_type' => 'FORBIDDEN_DOSAGE'
        ]);
    }

    public function test_grounded_mild_symptom_returns_citations_and_structure(): void
    {
        $response = $this->postJson('/api/chat', [
            'query' => 'Bagaimana pertolongan pertama kompres es pada ankle terkilir?'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'is_emergency' => false,
            'is_forbidden' => false,
            'is_fallback' => false
        ]);
        
        $data = $response->json();
        $this->assertNotEmpty($data['citations']);
        $this->assertStringContainsString('Informasi Klinis Umum', $data['answer']);
        $this->assertStringContainsString('Penanganan Mandiri Teruji', $data['answer']);
        $this->assertStringContainsString('Kapan Harus ke Dokter', $data['answer']);
    }

    public function test_curator_stats_and_logs(): void
    {
        $response = $this->getJson('/api/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure(['documents', 'chunks', 'journals', 'diseases', 'vocab_size']);

        $logsRes = $this->getJson('/api/logs');
        $logsRes->assertStatus(200);
    }
}

