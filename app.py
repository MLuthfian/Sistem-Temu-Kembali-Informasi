"""
MedikaRAG - Backend Web Application & REST APIs
Portal Edukasi Kesehatan & AI RAG Ilmiah Kemenkes RI
Sesuai PRD: FastAPI/Flask, Vector Retrieval, 5 Safe Views, Logging, Evaluator.
"""

import os
import sqlite3
import json
import time
from flask import Flask, request, jsonify, render_template, send_from_directory
from flask_cors import CORS

from rag.guardrails import GuardrailEngine
from rag.vector_store import VectorStore
from rag.synthesizer import RAGSynthesizer
from rag.evaluator import BenchmarkEvaluator

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DB_PATH = os.path.join(BASE_DIR, "data", "medikarag.db")

app = Flask(__name__, template_folder="templates", static_folder="static")
CORS(app)

# Global in-memory RAG instances
vector_store = VectorStore()
guardrail_engine = None
synthesizer = None


def get_db():
    conn = sqlite3.connect(DB_PATH)
    conn.row_factory = sqlite3.Row
    return conn


def init_rag_system():
    global vector_store, guardrail_engine, synthesizer
    print("[*] Initializing MedikaRAG RAG Engine...")

    conn = get_db()
    c = conn.cursor()

    # Load Chunks
    c.execute("SELECT * FROM chunks")
    rows = c.fetchall()
    chunks = [dict(r) for r in rows]

    # Load Emergency Rules
    c.execute("SELECT keyword, severity, action FROM emergency_rules")
    rules = [dict(r) for r in c.fetchall()]

    # Load Config
    c.execute("SELECT key, value FROM config")
    config = {r["key"]: r["value"] for r in c.fetchall()}
    conn.close()

    # Initialize Vector Store
    vector_store = VectorStore()
    vector_store.add_chunks(chunks)

    # Initialize Guardrails
    guardrail_engine = GuardrailEngine(rules)

    # Initialize Synthesizer (100% Local Grounded from Dataset - Zero External API)
    synthesizer = RAGSynthesizer(provider="dataset_local")

    print(f"[+] RAG Engine Ready! {len(chunks)} chunks indexed, {len(rules)} emergency rules active.")


# Initialize on startup
init_rag_system()


# ==============================================================================
# FRONTEND ROUTES
# ==============================================================================
@app.route("/")
def index():
    return render_template("index.html")


# ==============================================================================
# REST API ENDPOINTS
# ==============================================================================
@app.route("/api/chat", methods=["POST"])
def api_chat():
    """
    RAG Chatbot Endpoint (PRD Bagian 5.3 FR-10 s/d FR-16 & Bagian 6 Alur RAG)
    """
    data = request.get_json() or {}
    query = data.get("query", "").strip()
    session_id = data.get("session_id", "anon_session")
    clarification_context = data.get("clarification", None)

    if not query:
        return jsonify({"error": "Pertanyaan tidak boleh kosong"}), 400

    conn = get_db()
    c = conn.cursor()

    # 1. Deteksi Gawat Darurat (Red Flags)
    is_emergency, em_label, em_payload = guardrail_engine.check_emergency(query)
    if is_emergency:
        c.execute("""
            INSERT INTO audit_logs (
                session_id, query, is_emergency, is_forbidden, is_fallback,
                category, top_score, citations_count, response_preview
            ) VALUES (?, ?, 1, 0, 0, ?, 1.0, 0, ?)
        """, (session_id, query, f"EMERGENCY_{em_label}", em_payload["message"][:200]))
        conn.commit()
        conn.close()

        return jsonify({
            "is_emergency": True,
            "condition": em_label,
            "answer": em_payload["message"],
            "hotlines": em_payload["hotlines"],
            "immediate_actions": em_payload["immediate_actions"],
            "citations": [],
            "score": 1.0
        })

    # 2. Deteksi Permintaan Terlarang (Dosis, Diagnosis, Luar Topik, Jailbreak)
    is_forbidden, f_type, f_msg = guardrail_engine.check_forbidden_requests(query)
    if is_forbidden:
        c.execute("""
            INSERT INTO audit_logs (
                session_id, query, is_emergency, is_forbidden, is_fallback,
                category, top_score, citations_count, response_preview
            ) VALUES (?, ?, 0, 1, 0, ?, 0.0, 0, ?)
        """, (session_id, query, f_type, f_msg[:200]))
        conn.commit()
        conn.close()

        return jsonify({
            "is_emergency": False,
            "is_forbidden": True,
            "forbidden_type": f_type,
            "answer": f_msg,
            "citations": [],
            "score": 0.0
        })

    # 3. Retrieval Top-K dari Vector Store
    c.execute("SELECT value FROM config WHERE key = 'min_similarity_threshold'")
    thresh_row = c.fetchone()
    min_threshold = float(thresh_row["value"]) if thresh_row else 0.20

    chunks = vector_store.search(query, top_k=4, min_score=min_threshold)
    top_score = chunks[0]["score"] if chunks else 0.0

    # 4. Threshold Evaluation: Jika skor di bawah ambang, kirim Fallback
    if not chunks or top_score < min_threshold:
        c.execute("SELECT value FROM config WHERE key = 'fallback_message'")
        fb_row = c.fetchone()
        fallback_msg = fb_row["value"] if fb_row else "Informasi resmi belum tersedia."

        c.execute("""
            INSERT INTO audit_logs (
                session_id, query, is_emergency, is_forbidden, is_fallback,
                category, top_score, citations_count, response_preview
            ) VALUES (?, ?, 0, 0, 1, 'FALLBACK_UNAVAILABLE', ?, 0, ?)
        """, (session_id, query, top_score, fallback_msg[:200]))
        conn.commit()
        conn.close()

        return jsonify({
            "is_emergency": False,
            "is_forbidden": False,
            "is_fallback": True,
            "answer": fallback_msg,
            "citations": [],
            "score": top_score
        })

    # 5. Sintesis Jawaban dengan Grounding Sitasi (Format 5 Bagian)
    synth_resp = synthesizer.synthesize(query, chunks, clarification_context)
    answer_text = synth_resp.get("answer", "")
    citations = synth_resp.get("citations", [])

    # 6. Validasi Keluaran (Nol angka dosis obat keras, penegasan disclaimer)
    _, validated_answer = guardrail_engine.validate_output(answer_text, citations)

    # 7. Log Anonim (UU PDP No. 27/2022)
    c.execute("""
        INSERT INTO audit_logs (
            session_id, query, is_emergency, is_forbidden, is_fallback,
            category, top_score, citations_count, response_preview
        ) VALUES (?, ?, 0, 0, 0, 'GROUNDED_RAG', ?, ?, ?)
    """, (session_id, query, top_score, len(citations), validated_answer[:200]))
    conn.commit()
    conn.close()

    return jsonify({
        "is_emergency": False,
        "is_forbidden": False,
        "is_fallback": False,
        "answer": validated_answer,
        "citations": citations,
        "score": top_score,
        "chunks_used": len(chunks)
    })


@app.route("/api/diseases", methods=["GET"])
def api_get_diseases():
    """FR-1: Daftar penyakit & panduan dengan filter kategori."""
    category = request.args.get("category", "all")
    search = request.args.get("search", "").lower().strip()

    conn = get_db()
    c = conn.cursor()

    query = "SELECT * FROM diseases"
    params = []

    if category != "all":
        query += " WHERE category = ?"
        params.append(category)

    c.execute(query, params)
    diseases = [dict(r) for r in c.fetchall()]
    conn.close()

    if search:
        diseases = [d for d in diseases if (search in d["title"].lower() or search in d["summary"].lower() or search in d["symptoms"].lower())]

    return jsonify(diseases)


@app.route("/api/diseases/<slug>", methods=["GET"])
def api_get_disease_detail(slug):
    """FR-2: Halaman detail penyakit format baku."""
    conn = get_db()
    c = conn.cursor()
    c.execute("SELECT * FROM diseases WHERE slug = ?", (slug,))
    row = c.fetchone()
    conn.close()

    if not row:
        return jsonify({"error": "Topik tidak ditemukan"}), 404
    return jsonify(dict(row))


@app.route("/api/journals", methods=["GET"])
def api_get_journals():
    """FR-5 & FR-6: Modul jurnal ilmiah dengan filter indeks, tahun, topik."""
    index_filter = request.args.get("index", "all")
    year_filter = request.args.get("year", "all")
    search = request.args.get("search", "").lower().strip()

    conn = get_db()
    c = conn.cursor()
    c.execute("SELECT * FROM journals ORDER BY year DESC")
    journals = [dict(r) for r in c.fetchall()]
    conn.close()

    if index_filter != "all":
        journals = [j for j in journals if index_filter.lower() in j["index_rank"].lower()]

    if year_filter != "all":
        journals = [j for j in journals if str(j["year"]) == year_filter]

    if search:
        journals = [j for j in journals if (search in j["title"].lower() or search in j["authors"].lower() or search in j["abstract"].lower())]

    return jsonify(journals)


@app.route("/api/documents", methods=["GET", "POST"])
def api_documents():
    """FR-17: Kelola dokumen sumber Kemenkes & Jurnal."""
    conn = get_db()
    c = conn.cursor()

    if request.method == "POST":
        data = request.get_json() or {}
        title = data.get("title", "").strip()
        publisher = data.get("publisher", "Kementerian Kesehatan RI")
        year = int(data.get("year", 2024))
        url = data.get("url", "#")
        topic = data.get("topic", "Penyakit Ringan")
        layer = data.get("layer", "Lapisan 1 (Kemenkes)")
        license_type = data.get("license", "Hak Cipta Kemenkes RI")
        content = data.get("content", "").strip()

        if not title or not content:
            conn.close()
            return jsonify({"error": "Judul dan konten wajib diisi"}), 400

        c.execute("""
            INSERT INTO documents (
                title, publisher, year, url, topic, layer, license, verified_date, status, content
            ) VALUES (?, ?, ?, ?, ?, ?, ?, date('now'), 'Terverifikasi', ?)
        """, (title, publisher, year, url, topic, layer, license_type, content))
        doc_id = c.lastrowid

        # Auto-chunk into chunks table
        words = content.split()
        chunk_size = 70
        overlap = 15
        start = 0
        new_chunks = []
        while start < len(words):
            end = min(start + chunk_size, len(words))
            chunk_slice = " ".join(words[start:end])
            c.execute("""
                INSERT INTO chunks (
                    doc_id, judul_dokumen, sumber, url, topik, lapisan, lisensi, tahun, text
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            """, (doc_id, title, publisher, url, topic, layer, license_type, year, chunk_slice))
            new_chunks.append({
                "doc_id": doc_id,
                "judul_dokumen": title,
                "sumber": publisher,
                "url": url,
                "topik": topic,
                "lapisan": layer,
                "lisensi": license_type,
                "tahun": year,
                "text": chunk_slice
            })
            if end >= len(words):
                break
            start += (chunk_size - overlap)

        conn.commit()
        conn.close()

        # Update Vector Store in-memory
        vector_store.add_chunks(new_chunks)
        return jsonify({"message": "Dokumen berhasil ditambahkan & diindeks", "doc_id": doc_id})

    # GET: List documents
    c.execute("SELECT * FROM documents ORDER BY id DESC")
    docs = [dict(r) for r in c.fetchall()]
    conn.close()
    return jsonify(docs)


@app.route("/api/reindex", methods=["POST"])
def api_reindex():
    """FR-18: Sinkronisasi dan Re-index Vector Database."""
    t_start = time.time()
    init_rag_system()
    duration_ms = round((time.time() - t_start) * 1000, 2)
    stats = vector_store.get_stats()
    stats["duration_ms"] = duration_ms
    return jsonify({"message": "Vector store berhasil di-reindex!", "stats": stats})


@app.route("/api/emergency-rules", methods=["GET", "POST"])
def api_emergency_rules():
    """FR-20: Kelola daftar kata kunci darurat tanpa ubah kode."""
    conn = get_db()
    c = conn.cursor()

    if request.method == "POST":
        data = request.get_json() or {}
        keyword = data.get("keyword", "").strip().lower()
        severity = data.get("severity", "CRITICAL")
        action = data.get("action", "Arahkan ke IGD / 119")

        if not keyword:
            conn.close()
            return jsonify({"error": "Keyword wajib diisi"}), 400

        c.execute("""
            INSERT OR REPLACE INTO emergency_rules (keyword, severity, action)
            VALUES (?, ?, ?)
        """, (keyword, severity, action))
        conn.commit()

        # Reload rules in guardrail engine
        c.execute("SELECT keyword, severity, action FROM emergency_rules")
        rules = [dict(r) for r in c.fetchall()]
        conn.close()

        global guardrail_engine
        guardrail_engine = GuardrailEngine(rules)
        return jsonify({"message": f"Rule '{keyword}' berhasil disimpan!"})

    c.execute("SELECT * FROM emergency_rules ORDER BY id ASC")
    rules = [dict(r) for r in c.fetchall()]
    conn.close()
    return jsonify(rules)


@app.route("/api/logs", methods=["GET"])
def api_logs():
    """FR-19: Lihat log pertanyaan anonim (fallback & red flag audit)."""
    filter_type = request.args.get("filter", "all")
    conn = get_db()
    c = conn.cursor()

    query = "SELECT * FROM audit_logs"
    params = []
    if filter_type == "fallback":
        query += " WHERE is_fallback = 1"
    elif filter_type == "emergency":
        query += " WHERE is_emergency = 1"
    elif filter_type == "forbidden":
        query += " WHERE is_forbidden = 1"

    query += " ORDER BY id DESC LIMIT 100"
    c.execute(query, params)
    logs = [dict(r) for r in c.fetchall()]
    conn.close()
    return jsonify(logs)


@app.route("/api/evaluate", methods=["POST"])
def api_evaluate():
    """PRD Bagian 11: Menjalankan Set Evaluasi 90 Soal & Metrik Kriteria Penerimaan."""
    conn = get_db()
    c = conn.cursor()
    c.execute("SELECT value FROM config WHERE key = 'min_similarity_threshold'")
    row = c.fetchone()
    conn.close()
    thresh = float(row["value"]) if row else 0.20

    evaluator = BenchmarkEvaluator(guardrail_engine, vector_store, synthesizer)
    report = evaluator.run_evaluation(min_threshold=thresh)
    return jsonify(report)


@app.route("/api/stats", methods=["GET"])
def api_stats():
    """Dashboard Stats Overview."""
    conn = get_db()
    c = conn.cursor()
    c.execute("SELECT COUNT(*) as count FROM documents")
    docs_cnt = c.fetchone()["count"]
    c.execute("SELECT COUNT(*) as count FROM chunks")
    chunks_cnt = c.fetchone()["count"]
    c.execute("SELECT COUNT(*) as count FROM journals")
    journals_cnt = c.fetchone()["count"]
    c.execute("SELECT COUNT(*) as count FROM diseases")
    diseases_cnt = c.fetchone()["count"]
    c.execute("SELECT COUNT(*) as count FROM audit_logs")
    logs_cnt = c.fetchone()["count"]
    c.execute("SELECT COUNT(*) as count FROM audit_logs WHERE is_emergency = 1")
    em_cnt = c.fetchone()["count"]
    conn.close()

    v_stats = vector_store.get_stats()

    return jsonify({
        "documents": docs_cnt,
        "chunks": chunks_cnt,
        "journals": journals_cnt,
        "diseases": diseases_cnt,
        "total_queries": logs_cnt,
        "emergency_triggers": em_cnt,
        "vocab_size": v_stats.get("vocab_size", 0),
        "layer1_chunks": v_stats.get("layer1_chunks", 0),
        "layer2_chunks": v_stats.get("layer2_chunks", 0)
    })


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)

