<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\CuratorController;

// RAG AI Chatbot Endpoint
Route::post('/chat', [ChatbotController::class, 'chat']);

// Disease Directory Endpoints
Route::get('/diseases', [DiseaseController::class, 'index']);
Route::get('/diseases/{slug}', [DiseaseController::class, 'show']);

// Scientific Journals Module Endpoints
Route::get('/journals', [JournalController::class, 'index']);

// Curator & Admin Panel Endpoints
Route::get('/stats', [CuratorController::class, 'getStats']);
Route::get('/documents', [CuratorController::class, 'getDocuments']);
Route::post('/documents', [CuratorController::class, 'addDocument']);
Route::post('/reindex', [CuratorController::class, 'reindex']);
Route::get('/emergency-rules', [CuratorController::class, 'getEmergencyRules']);
Route::post('/emergency-rules', [CuratorController::class, 'addEmergencyRule']);
Route::get('/logs', [CuratorController::class, 'getLogs']);
Route::post('/evaluate', [CuratorController::class, 'evaluate']);

