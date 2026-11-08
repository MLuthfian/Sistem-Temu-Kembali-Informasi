# 🩺 MedikaRAG - Portal Edukasi Kesehatan & AI RAG Berbasis Dataset Medis (Laravel Edition)

MedikaRAG adalah platform portal edukasi kesehatan terpercaya dan asisten konsultasi AI berbasis **Retrieval-Augmented Generation (RAG)** murni lokal yang dikembangkan menggunakan **Laravel Framework 13.x (PHP 8.3+)**.

Sistem ini dirancang khusus sesuai instruksi akademik untuk **beroperasi 100% secara lokal berbasis dataset medis terverifikasi tanpa bergantung pada external LLM API (OpenAI/Gemini)**. Seluruh jawaban disarikan secara deterministik dari **500 entri dataset konsultasi dokter berlisensi**, Pedoman Nasional Pelayanan Kedokteran (**PNPK Kemenkes RI**), serta jurnal ilmiah terakreditasi (**SINTA 2 hingga Scopus**).

---

## 🌟 Fitur Utama Sistem

### 1. RAG AI Chatbot Berbasis Dataset Dokter (100% Bebas API Eksternal)
- **Dataset Ingestion**: Sebanyak **500 entri konsultasi dokter terverifikasi** telah diimpor dan diindeks ke dalam korpus vektor lokal (569 chunks, 7.667 kosakata TF-IDF/BM25).
- **Format Jawaban Klinis Terstandar (5 Bagian Wajib)**:
  1. 🩺 **Penjelasan Klinis Dokter (Berdasarkan Dataset Konsultasi)**: Ulasan penyebab dan mekanisme penyakit dari entri dataset dokter terkait dengan sitasi `[1]`.
  2. 💡 **Rekomendasi Penanganan & Anjuran Dokter**: Langkah perawatan mandiri, hidrasi, nutrisi, kebersihan, dan kompres yang dianjurkan dokter.
  3. ⚠️ **Kapan Harus Memeriksakan Diri ke Faskes / Dokter**: Tanda bahaya (*red flags*) dan arahan rujukan medis langsung.
  4. 📚 **Rujukan Entri Dataset Terverifikasi**: Menampilkan nomor entri dataset konsultasi `[No. X]`, nama dokter konsultan, topik, dan lapisan dokumen.
  5. ⚖️ **Pernyataan Penyangkal Medis (Medical Disclaimer)**.

### 2. Guardrails Keselamatan Pasien (Standard of Care)
- **100% Emergency Recall**: Triase kedaruratan otomatis mendeteksi tanda bahaya kritis (*nyeri dada menembus, sesak napas berat, kejang, stroke F.A.S.T, perdarahan masif, sianosis, keracunan akut*) dan mengarahkan langsung ke **Ambulans 119 SPGDT** dan **112 Darurat**.
- **100% Dosage Refusal**: Penolakan tegas terhadap permintaan dosis spesifik obat keras/antibiotik dan mengarahkan ke dokter atau apoteker berlisensi.
- **Substantive Term Gating & Fallback Aman**: Jika pertanyaan di luar cakupan korpus atau mengenai penyakit langka yang tidak tercakup dalam dataset, sistem secara aman menampilkan fallback transparan tanpa halusinasi.

### 3. Direktori 15 Topik Penyakit Ringan Resmi Kemenkes RI
- 15 panduan klinis penyakit umum berstandar PNPK Kemenkes RI dengan 8 komponen medis terstruktur.

### 4. Modul Jurnal Ilmiah Medis
- Koleksi literatur klinis dan protokol berbasis bukti ilmiah terakreditasi **SINTA 2**, **SINTA 3**, dan **Scopus**.

### 5. Panduan P3K & Triase Gawat Darurat
- Protokol pertolongan pertama terstandarisasi: **R.I.C.E.** untuk cedera fisik/terkilir, luka bakar, mimisan, dan diare dehidrasi.

### 6. Panel Kurator Medis
- Dasbor pemantauan korpus real-time, statistik vocabulary TF-IDF, log query anonim sesuai **UU Pelindungan Data Pribadi (UU PDP No. 27/2022)**, dan tombol eksekusi benchmark otomatis.

---

## 🚀 Perintah Artisan MedikaRAG

Sistem dilengkapi serangkaian perintah CLI Artisan untuk kemudahan pengujian, pemeliharaan, dan evaluasi:

```bash
# 1. Impor atau perbarui 500 dataset konsultasi dokter ke database
php artisan medikarag:import-dataset

# 2. Uji coba pertanyaan langsung ke engine RAG dari terminal
php artisan medikarag:ask "Radang tenggorokan obat dan penanganan apa yang dianjurkan dokter?"
php artisan medikarag:ask "Anak saya terkena cacar air, bagaimana penanganannya?"
php artisan medikarag:ask "Kulit gatal dan muncul ruam di selangkangan bagaimana mengatasinya?"

# 3. Jalankan suite pengujian integrasi klinis (7 modul)
php artisan medikarag:test

# 4. Jalankan evaluasi benchmark 90 soal
php artisan medikarag:benchmark

# 5. Indeks ulang korpus dokumen ke memori vector store
php artisan medikarag:reindex
```

---

## 📊 Hasil Evaluasi & Pengujian

### Hasil Suite Pengujian Integrasi (`php artisan medikarag:test`)
| Modul Pengujian | Status | Catatan Verifikasi |
|---|---|---|
| Web Interface (Blade Template & Assets) | **PASS** | Status 200, MedikaRAG view loaded |
| Direktori 15 Topik Penyakit Kemenkes | **PASS** | 15 topik penyakit terindeks |
| Modul Jurnal Ilmiah Sinta/Scopus | **PASS** | 6 jurnal terindeks |
| Emergency Guardrail (Red Flag 119/112) | **PASS** | 100% triage hotline aktif |
| Guardrail Refusal Resep & Dosis Obat Keras | **PASS** | Penolakan tegas & edukasi apoteker |
| RAG Retrieval & Grounding (5 Bagian + Sitasi) | **PASS** | 4 sitasi terverifikasi dilampirkan |
| Curator Panel Stats & Monitoring | **PASS** | 569 Chunks, 7.667 Vocabularies |

### Hasil Evaluasi Benchmark 90 Soal (`php artisan medikarag:benchmark`)
| Metrik Evaluasi | Target PRD | Capaian Sistem | Status |
|---|---|---|---|
| **Akurasi Keseluruhan** | >= 90.0% | **90.0%** | **LULUS (PASS)** |
| **Emergency Recall (Red Flags)** | 100.0% | **100.0%** | **LULUS (PASS)** |
| **Dosage Refusal Rate** | 100.0% | **100.0%** | **LULUS (PASS)** |
| **Pelanggaran Dosis (Leak)** | 0 | **0** | **LULUS (PASS)** |
| **Pelanggaran Sitasi** | 0 | **0** | **LULUS (PASS)** |
| **Rata-rata Latensi** | < 2500 ms | **0.7 ms** | **LULUS (SUPERIOR)** |

---

## 🛠️ Stack Teknologi
- **Backend**: Laravel 13.x (PHP 8.3+)
- **Database**: SQLite (`database/database.sqlite`)
- **Vector Retrieval**: Pure PHP In-Memory TF-IDF & Hybrid Overlap Vector Store (Tanpa dependensi eksternal)
- **Frontend**: Laravel Blade + Tailwind CSS CDN + Google Material Symbols
- **Kepatuhan Regulasi**: UU PDP No. 27/2022 (Anonimitas session, zero telemetry data pribadi)
