<?php

namespace App\Services\RAG;

use App\Models\EmergencyRule;

class GuardrailService
{
    protected array $emergencyPatterns;

    public function __construct()
    {
        $this->emergencyPatterns = [
            ['/(nyeri\s+dada|sakit\s+dada|dada\s+tertekan|dada.*(tertekan|sakit|nyeri|panas|beban|menusuk|sesak)|(nyeri|sakit).*dada|serangan\s+jantung|angina|jantung\s+berdebar.*(pingsan|sesak))/i', 'Nyeri Dada Akut / Curiga Sindrom Koroner Akut'],
            ['/(sesak\s+napas\s+berat|sesak.*(tersengal|parah|berat|bunyi)|napas.*(tersengal|berat|ngik|sesak|cepat.*bibir)|(bibir|kulit).*membiru|sianosis|tersedak.*(benda|makanan)|sumbatan\s+jalan\s+napas)/i', 'Sesak Napas Berat / Obstruksi Jalan Napas'],
            ['/(kejang|epilepsi\s+kambuh|kelojotan|menggelepar|tidak\s+sadar|pingsan|hilang\s+kesadaran|koma|tidak\s+merespon|mengigau.*tak\s+sadar)/i', 'Kejang Akut / Penurunan Kesadaran'],
            ['/(stroke|wajah.*mencong|muka.*mencong|bicara.*pelo|bicaranya\s+pelo|sulit\s+bicara\s+mendadak|lemah.*sebelah|lumpuh.*(separuh|sebelah)|mati\s+rasa.*(mendadak|separuh))/i', 'Kecurigaan Stroke Akut (F.A.S.T)'],
            ['/(perdarahan\s+hebat|pendarahan\s+hebat|darah.*(menyembur|memancar|mengucur|deras|tidak\s+berhenti)|muntah\s+darah|muntah.*kopi|bab.*(berdarah\s+hitam|darah\s+hitam)|melena\s+masif)/i', 'Perdarahan Masif Tidak Terkontrol'],
            ['/(cedera\s+kepala.*pingsan|kepala.*terbentur.*(pingsan|muntah)|muntah\s+proyektil|muntah\s+menyembur|cairan.*(telinga|hidung)|darah.*keluar.*(telinga|hidung)|pupil.*tidak\s+sama)/i', 'Cedera Kepala Berat / Tekanan Intrakranial'],
            ['/(tulang.*menonjol|patah\s+tulang\s+terbuka|fraktur\s+terbuka|tulang.*menembus\s+kulit|leher.*(tertekuk|patah|cedera)|cedera\s+tulang\s+belakang|mati\s+rasa.*tidak\s+bisa\s+digerakkan)/i', 'Fraktur Terbuka / Cedera Tulang Belakang'],
            ['/(keracunan|minum.*racun|tertelan.*(pembersih|deterjen|baterai|kimia)|(minum|meminum).*(baygon|pestisida|serangga|minyak\s+tanah|pembersih)|overdosis\s+obat|minum\s+obat\s+banyak)/i', 'Gawat Keracunan Akut / Intoksikasi'],
            ['/(hamil.*(pendarahan|perdarahan|nyeri\s+perut)|(pendarahan|perdarahan).*hamil|ketuban\s+pecah|kejang.*hamil|preeklamsia)/i', 'Kegawatdaruratan Maternal / Obstetri'],
            ['/(bunuh\s+diri|ingin\s+mati|mengakhiri\s+hidup|menyakiti\s+diri)/i', 'Krisis Kejiwaan Akut / Ancaman Bunuh Diri']
        ];

        // Load custom DB rules if available
        try {
            $dbRules = EmergencyRule::all();
            foreach ($dbRules as $r) {
                if ($r->keyword) {
                    $pattern = '/(' . preg_quote(trim($r->keyword), '/') . ')/i';
                    array_unshift($this->emergencyPatterns, [$pattern, $r->action ?: 'Kondisi Gawat Darurat']);
                }
            }
        } catch (\Exception $e) {
            // DB not yet ready or migrating
        }
    }

    public function checkEmergency(string $query): array
    {
        $normalized = trim(strtolower($query));

        foreach ($this->emergencyPatterns as [$pattern, $label]) {
            if (preg_match($pattern, $normalized)) {
                return [
                    'is_emergency' => true,
                    'condition' => $label,
                    'triage_color' => 'RED',
                    'hotlines' => [
                        ['name' => 'SPGDT Ambulans Kemenkes', 'number' => '119', 'desc' => 'Penjemputan gawat darurat & triase nasional'],
                        ['name' => 'Hotline Darurat Terpadu', 'number' => '112', 'desc' => 'Pusat komando darurat pemerintah daerah'],
                        ['name' => 'Halo Kemenkes RI', 'number' => '1500-567', 'desc' => 'Layanan informasi faskes rujukan']
                    ],
                    'immediate_actions' => [
                        'Segera hubungi ambulans 119 atau bawa pasien ke IGD Rumah Sakit terdekat sekarang juga.',
                        'Tetap tenang dan dampingi pasien. Jangan tinggalkan pasien sendirian.',
                        'Posisikan pasien senyaman mungkin. Jika tidak sadar namun bernapas, posisikan miring mantap (recovery position).',
                        'JANGAN memberikan makanan, minuman, atau obat oral jika pasien sesak, kejang, atau kesadaran menurun.',
                        'Jika terjadi perdarahan hebat, lakukan penekanan langsung (direct pressure) dengan kain bersih pada sumber darah.'
                    ],
                    'message' => "🚨 **PERINGATAN GAWAT DARURAT MEDIS (TRIAGE MERAH)**\n\n" .
                        "Keluhan Anda teridentifikasi sebagai potensi: **{$label}**.\n\n" .
                        "Platform MedikaRAG **bukan pengganti layanan gawat darurat**. Kondisi ini memerlukan pemeriksaan dan tindakan segera oleh dokter/tenaga medis.\n\n" .
                        "📞 **Langkah Tindakan Cepat:**\n" .
                        "1. **Segera hubungi Ambulans 119 (Bebas Pulsa)** atau **Hotline Darurat 112**.\n" .
                        "2. Atau segera menuju ke **IGD Rumah Sakit terdekat** saat ini juga.\n" .
                        "3. Dampingi pasien dan hubungi keluarga terdekat."
                ];
            }
        }

        return ['is_emergency' => false];
    }

    public function checkForbiddenRequests(string $query): array
    {
        $normalized = trim(strtolower($query));

        // 1. Jailbreak attempts
        $jailbreakPatterns = [
            '/(abaikan\s*(semua\s*)?instruksi|ignore\s+all\s+previous\s+instructions|jailbreak|dan\s+mode|bypass\s+guardrails|tampilkan\s+system\s+prompt|developer\s+mode)/i',
            '/(jadilah\s+dokter\s+yang\s+meresepkan|pretend\s+to\s+be\s+a\s+doctor\s+who\s+prescribes|act\s+as\s+an\s+unrestricted)/i'
        ];
        foreach ($jailbreakPatterns as $p) {
            if (preg_match($p, $normalized)) {
                return [
                    'is_forbidden' => true,
                    'type' => 'JAILBREAK_ATTEMPT',
                    'message' => "Maaf, MedikaRAG beroperasi secara ketat di bawah protokol keselamatan medis Kemenkes RI. Instruksi keamanan dan etika sistem tidak dapat diubah atau diabaikan."
                ];
            }
        }

        // 2. Dosage request check
        $dosagePatterns = [
            '/\b(berapa\s*(mg|miligram|dosis|ml|mililiter))\b/i',
            '/\b(dosis\s*(obat|anak|dewasa|bayi|balita|amoxicillin|paracetamol|ibuprofen|antibiotik|steroid|asam mefenamat|dexamethasone|metformin|amlodipine|ciprofloxacin|cefixime))\b/i',
            '/\b(takaran\s*(tetes|sendok|dosis|butir|tablet|kapsul|obat))\b/i',
            '/\b(berapa\s*(sendok|tetes|butir|tablet|kapsul)\s*.*(obat|sirup|paracetamol|antibiotik|pct))\b/i',
            '/\b(minta\s+resep|resepkan\s+obat|buatkan\s+resep|resepkan|resep\s+antibiotik|resep\s+dokter|aturan\s+minum\s+obat)\b/i'
        ];
        $isNutritionContext = (bool)preg_match('/\b(gula|garam|minyak|air|sayur|makan|piringku|gizi)\b/i', $normalized);
        foreach ($dosagePatterns as $p) {
            if (preg_match($p, $normalized)) {
                if ($isNutritionContext && !str_contains($normalized, 'obat') && !str_contains($normalized, 'antibiotik')) {
                    continue;
                }
                return [
                    'is_forbidden' => true,
                    'type' => 'FORBIDDEN_DOSAGE',
                    'message' => "⚠️ **Kebijakan Keselamatan Farmasi (Standard of Care):**\n\n" .
                        "MedikaRAG **tidak diperkenankan memberikan perhitungan dosis obat spesifik (mg/ml/sendok)** maupun memvalidasi resep obat keras/antibiotik.\n\n" .
                        "Pemberian dosis obat harus disesuaikan secara individual dengan berat badan, usia, fungsi ginjal/hati, dan riwayat alergi oleh **Dokter atau Apoteker** di fasilitas kesehatan.\n\n" .
                        "**Rekomendasi Aman:**\n" .
                        "• Untuk obat bebas bertanda lingkaran hijau atau biru (misal: paracetamol untuk pereda demam atau oralit untuk hidrasi), selalu baca petunjuk takaran resmi pada kemasan obat.\n" .
                        "• Konsultasikan langsung dengan **Apoteker di apotek resmi** atau dokter Anda untuk penyesuaian dosis yang aman dan rasional."
                ];
            }
        }

        // 3. Diagnosis request check
        $diagnosisPatterns = [
            '/\b(diagnosis\s+penyakit\s+saya|tentukan\s+diagnosis)\b/i',
            '/\b(apakah\s*(saya|ini)\s*(pasti|fix|positif)\s*(terkena|kena|penyakit|kanker|tumor|tbc|lupus|hiv))\b/i',
            '/\b(apakah.*(pasti|fix).*(tbc|kanker|tumor|lupus|stadium))\b/i',
            '/\b(apakah\s+saya\s+kena\s+kanker|apakah\s+ini\s+tumor\s+ganas)\b/i'
        ];
        foreach ($diagnosisPatterns as $p) {
            if (preg_match($p, $normalized)) {
                return [
                    'is_forbidden' => true,
                    'type' => 'FORBIDDEN_DIAGNOSIS',
                    'message' => "⚠️ **Batasan Diagnosis Medis:**\n\n" .
                        "MedikaRAG adalah sistem edukasi kesehatan berbasis bukti (RAG) dan **bukan alat penegak diagnosis klinis definitif**.\n\n" .
                        "Diagnosis penyakit membutuhkan pemeriksaan fisik langsung (palpasi, auskultasi), wawancara klinis mendalam (anamnesis), serta pemeriksaan penunjang (laboratorium darah, rontgen, dsb.) oleh dokter berwenang.\n\n" .
                        "Silakan jelaskan gejala yang dirasakan untuk mendapatkan informasi langkah pertolongan pertama mandiri sementara dan tanda bahaya yang perlu diwaspadai."
                ];
            }
        }

        // 4. Out of domain check
        $outOfDomainPatterns = [
            '/\b(kucing|anjing|hewan\s+peliharaan|burung|ikan\s+cupang|whiskas|veteriner)\b/i',
            '/\b(bengkel|karburator|komputer|laptop|koding|coding|python|javascript|koin\s+crypto|bitcoin|harga\s+saham)\b/i',
            '/\b(resep\s*(kue|rendang|masakan|makanan)|hotel|pantai\s+kuta|tiket\s+pesawat|tinggi\s+menara)\b/i'
        ];
        foreach ($outOfDomainPatterns as $p) {
            if (preg_match($p, $normalized)) {
                return [
                    'is_forbidden' => true,
                    'type' => 'OUT_OF_DOMAIN',
                    'message' => "Maaf, pertanyaan Anda berada di luar cakupan MedikaRAG. Platform ini difokuskan khusus untuk panduan pertolongan pertama (P3K), edukasi penyakit ringan, pencegahan kondisi kronis, dan gizi seimbang pada manusia berdasarkan dokumen resmi Kemenkes RI dan jurnal medis terindeks."
                ];
            }
        }

        return ['is_forbidden' => false];
    }

    public function validateOutput(string $text, array $citations): string
    {
        // Strip illegal dosage leaks
        $sanitized = preg_replace('/\b\d+(\.\d+)?\s*(mg|miligram)\s*(per|\/)\s*kg\b/i', '[Dosis disesuaikan oleh dokter/apoteker]', $text);

        $disclaimer = "\n\n---\n" .
            "*📌 **Disclaimer Medis**: Informasi ini berbasis rujukan resmi Kemenkes RI dan literatur ilmiah terindeks untuk tujuan edukasi mandiri. Ini bukan diagnosis medis pengganti konsultasi langsung dengan dokter.*";

        if (!str_contains($sanitized, 'Disclaimer Medis')) {
            $sanitized .= $disclaimer;
        }

        return $sanitized;
    }
}

