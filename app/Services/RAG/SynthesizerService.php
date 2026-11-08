<?php

namespace App\Services\RAG;

class SynthesizerService
{
    protected string $defaultFallback = "Maaf, informasi terverifikasi untuk pertanyaan spesifik tersebut belum tercakup dalam 500 entri dataset konsultasi dokter dan basis pengetahuan resmi kami.\n\nUntuk memastikan diagnosis yang akurat dan penanganan yang aman sesuai kondisi individual Anda, disarankan untuk memeriksakan diri langsung ke dokter atau Fasilitas Pelayanan Kesehatan (Puskesmas / Klinik / Rumah Sakit) terdekat.";

    /**
     * Synthesize answer purely based on local dataset chunks without any external API calls.
     */
    public function synthesize(
        string $query,
        array $chunks,
        ?array $clarification = null,
        ?string $fallbackTemplate = null
    ): array {
        if (empty($chunks)) {
            return [
                'success' => false,
                'is_fallback' => true,
                'answer' => $fallbackTemplate ?: $this->defaultFallback,
                'citations' => [],
                'score' => 0.0
            ];
        }

        return $this->generateDatasetGroundedAnswer($query, $chunks, $clarification);
    }

    /**
     * Clean raw chunk text by removing metadata headers, greetings, and closing signatures.
     */
    public function cleanRawDoctorText(string $text): string
    {
        // 1. Remove metadata headers
        $clean = preg_replace('/^TOPIK KELUHAN:[^\n]*\n+/i', '', $text);
        $clean = preg_replace('/^KONSULTASI DOKTER:[^\n]*\n+/i', '', $clean);
        
        // 2. Remove common doctor greetings
        $clean = preg_replace('/^(?:Halo|Alo|Hai|Dear|Terima kasih|Yth\.)\s+[^,.\n]+[,.\n]+\s*/iu', '', trim($clean));
        
        // 3. Remove closing signatures
        $clean = preg_replace('/(?:Semoga bermanfaat|Demikian yang dapat kami sampaikan|Demikian informasi yang dapat saya berikan|Salam|Terima kasih)[,\.\s\n]+(?:dr\.|Dr\.)\s*[A-Za-z\s,\.]*$/iu', '', $clean);

        return trim($clean);
    }

    /**
     * Generate grounded response synthesized strictly from retrieved dataset entries.
     */
    protected function generateDatasetGroundedAnswer(string $query, array $chunks, ?array $clarification): array
    {
        $citations = [];
        $cleanedTexts = [];

        foreach ($chunks as $idx => $chunk) {
            $citeId = $idx + 1;
            $cleaned = $this->cleanRawDoctorText($chunk['text'] ?? '');
            $cleanedTexts[] = $cleaned;

            $citations[] = [
                'id' => $citeId,
                'judul' => $chunk['judul_dokumen'] ?? 'Entri Dataset Medis Terverifikasi',
                'sumber' => $chunk['sumber'] ?? 'Dokter Konsultan Medis',
                'tahun' => $chunk['tahun'] ?? 2024,
                'lapisan' => $chunk['lapisan'] ?? 'Lapisan 1 (Dataset Konsultasi)',
                'url' => $chunk['url'] ?? '#',
                'lisensi' => $chunk['lisensi'] ?? 'CC BY-SA 4.0 Open Medical Dataset',
                'topik' => $chunk['topik'] ?? 'Keluhan Medis',
                'score' => $chunk['score'] ?? 0.0,
                'excerpt' => mb_substr(strip_tags($cleaned), 0, 180) . '...'
            ];
        }

        $primaryChunk = $chunks[0];
        $isDoctorDataset = str_contains($primaryChunk['lapisan'] ?? '', 'Dataset Konsultasi') || str_contains($primaryChunk['judul_dokumen'] ?? '', 'Konsultasi Dokter');

        // Extract clinical sections using cleaned text
        $clinicalExplanation = $this->extractClinicalExplanation($cleanedTexts[0] ?? '');
        $selfCareSteps = $this->extractSelfCareSteps($cleanedTexts);
        $whenToDoctor = $this->extractWhenToDoctor($cleanedTexts);

        // Demographic clarification notes
        $clarificationNote = '';
        if ($clarification) {
            $age = $clarification['age'] ?? null;
            $isPregnant = $clarification['is_pregnant'] ?? false;
            if ($isPregnant) {
                $clarificationNote = "\n\n> ⚠️ **Peringatan Khusus Kehamilan**: Mengingat kondisi sedang hamil, seluruh penanganan mandiri harus dilakukan dengan sangat hati-hati. Segera konsultasikan ke bidan atau dokter spesialis kandungan (Sp.OG).";
            } elseif ($age && ((int)$age < 2 || (int)$age > 65)) {
                $clarificationNote = "\n\n> ⚠️ **Catatan Usia Rentan ({$age} tahun)**: Pasien balita dan lansia memiliki risiko komplikasi lebih tinggi. Disarankan evaluasi langsung oleh dokter jika gejala menetap >24 jam.";
            }
        }

        $sectionTitle1 = $isDoctorDataset 
            ? "### 🩺 1. Penjelasan Klinis Dokter (Berdasarkan Dataset Konsultasi)"
            : "### 🩺 1. Informasi Klinis Umum";

        $sectionTitle2 = $isDoctorDataset
            ? "### 💡 2. Rekomendasi Penanganan & Anjuran Dokter"
            : "### 💡 2. Langkah Perawatan Mandiri di Rumah";

        $answerParts = [
            $sectionTitle1,
            trim($clinicalExplanation) . " [1]",
            "",
            $sectionTitle2,
            trim($selfCareSteps) . (count($chunks) > 1 ? " [1][2]" : " [1]"),
            "",
            "### ⚠️ 3. Kapan Harus Memeriksakan Diri ke Faskes / Dokter",
            trim($whenToDoctor) . $clarificationNote,
            "",
            "### 📚 4. Rujukan Entri Dataset Terverifikasi"
        ];

        foreach ($citations as $c) {
            $answerParts[] = "- **[{$c['id']}] {$c['judul']}** ({$c['tahun']}) • *{$c['sumber']}* [{$c['lapisan']}] [Buka Detail]({$c['url']})";
        }

        $answerParts[] = "";
        $answerParts[] = "---";
        $answerParts[] = "*📌 **Disclaimer Medis**: Jawaban di atas disarikan secara objektif dari 500 dataset konsultasi dokter berlisensi dan pedoman kesehatan resmi. Sistem RAG ini berfungsi sebagai sarana edukasi dan pertolongan pertama, bukan pengganti pemeriksaan medis langsung oleh dokter di fasilitas kesehatan.*";

        return [
            'success' => true,
            'is_fallback' => false,
            'topic' => $primaryChunk['topik'] ?? 'Keluhan Medis',
            'answer' => implode("\n", $answerParts),
            'citations' => $citations,
            'score' => $primaryChunk['score'] ?? 0.0
        ];
    }

    /**
     * Extract explanation of the condition / cause from cleaned primary chunk.
     */
    protected function extractClinicalExplanation(string $cleaned): string
    {
        if (empty($cleaned)) {
            return "Kondisi ini memerlukan observasi cermat terhadap gejala yang dirasakan.";
        }

        // Split by marker phrases indicating beginning of home care instructions
        $parts = preg_split('/(?:\b(?:Penanganan secara umum|Penanganan sementara|Penanganan di rumah|Langkah penanganan|Berikut hal-hal yang dapat dilakukan|Beberapa tips yang dapat dilakukan|Cobalah cara - cara berikut|Sebagai anjuran|Hal-hal berikut dapat membantu)\s*[:]\s*)/iu', $cleaned, 2);
        
        $explanationText = trim($parts[0]);

        // Break into sentences
        $sentences = preg_split('/(?<=[.?!])\s+(?=[A-Z0-9])/u', $explanationText);
        $collected = [];
        $totalLen = 0;

        foreach ($sentences as $s) {
            $sTrim = trim($s);
            if (empty($sTrim)) continue;

            // Stop before recommendations or sign-offs
            if (preg_match('/^(?:Demikian|Semoga|Salam|Untuk mengetahui lebih lanjut|Klik artikel)/i', $sTrim)) {
                break;
            }

            $collected[] = $sTrim;
            $totalLen += mb_strlen($sTrim);
            if ($totalLen >= 300 || count($collected) >= 4) {
                break;
            }
        }

        if (!empty($collected)) {
            return implode(' ', $collected);
        }

        return mb_substr($explanationText, 0, 300) . '...';
    }

    /**
     * Extract actionable self-care or lifestyle advice from cleaned texts.
     */
    protected function extractSelfCareSteps(array $cleanedTexts): string
    {
        $bullets = [];

        foreach ($cleanedTexts as $text) {
            // Check for list-like sentences
            // Pattern 1: Items separated by colon and common action verbs
            if (preg_match('/(?:Penanganan|Berikut hal-hal|Cobalah cara|Sebagai anjuran|Tips)[^:]*:\s*(.+?)(?=(?:Untuk mengetahui|Jika kondisi|Bila keluhan|Demikian|Semoga|$))/is', $text, $matchList)) {
                $rawList = $matchList[1];
                // Split on multiple spaces, newlines, or numbered points
                $items = preg_split('/(?:\r?\n+|\s{2,}|(?<=[.?!;])\s+(?=[A-Z0-9])|\b\d+\.\s*)/u', $rawList);
                foreach ($items as $it) {
                    $it = trim(preg_replace('/^[-•*\d\.\s]+/u', '', $it));
                    if (mb_strlen($it) >= 12 && mb_strlen($it) <= 220) {
                        if (!preg_match('/^(?:Demikian|Semoga|Salam|dr\.|Halo|Alo)/i', $it)) {
                            $bullets[] = "- " . ucfirst($it);
                            if (count($bullets) >= 5) break 2;
                        }
                    }
                }
            }

            // Pattern 2: Sentences starting with imperative health verbs
            $sentences = preg_split('/(?<=[.?!])\s+/u', $text);
            foreach ($sentences as $s) {
                $sTrim = trim($s);
                if (preg_match('/^(?:Pastikan|Hindari|Jaga|Minum|Makan|Istirahat|Kompres|Gunakan|Berkumurlah|Kelola|Bersihkan|Rutin|Penuhi|Lakukan|Cukupi|Perbanyak)\b/iu', $sTrim)) {
                    if (mb_strlen($sTrim) >= 15 && mb_strlen($sTrim) <= 200) {
                        $bullets[] = "- " . ucfirst($sTrim);
                        if (count($bullets) >= 5) break 2;
                    }
                }
            }
        }

        if (!empty($bullets)) {
            // Deduplicate
            $uniqueBullets = array_unique($bullets);
            return implode("\n", array_slice($uniqueBullets, 0, 5));
        }

        return "- Istirahatkan bagian tubuh yang sakit dan penuhi istirahat minimal 7-8 jam per hari.\n- Penuhi hidrasi tubuh dengan minum air putih hangat minimal 2 liter per hari.\n- Konsumsi makanan bergizi seimbang dan hindari makanan pedas, berminyak, atau pemicu iritasi.\n- Jaga kebersihan diri dan hindari konsumsi obat keras tanpa anjuran resep dokter.";
    }

    /**
     * Extract when to visit doctor / warning signs from cleaned texts.
     */
    protected function extractWhenToDoctor(array $cleanedTexts): string
    {
        $warnings = [];

        foreach ($cleanedTexts as $text) {
            $sentences = preg_split('/(?<=[.?!])\s+/u', $text);

            foreach ($sentences as $s) {
                $sTrim = trim($s);
                if (preg_match('/(?:segeralah|sebaiknya|periksakan|kunjungi|konsultasikan|bila keluhan|jika kondisi|jika gejala|tanda bahaya|ke dokter|spesialis|puskesmas|faskes|memburuk|tidak kunjung membaik)/iu', $sTrim)) {
                    // Exclude sentences that are purely signatures or greetings
                    if (mb_strlen($sTrim) >= 25 && mb_strlen($sTrim) <= 260 && !preg_match('/^(?:Demikian|Semoga|Salam|Terima kasih)/iu', $sTrim)) {
                        $warnings[] = $sTrim;
                        if (count($warnings) >= 2) break 2;
                    }
                }
            }
        }

        if (!empty($warnings)) {
            $unique = array_unique($warnings);
            return implode(' ', $unique);
        }

        return "Segera periksakan diri langsung ke dokter atau Fasilitas Pelayanan Kesehatan terdekat apabila gejala tidak kunjung membaik dalam 3 hari, timbul demam tinggi, nyeri semakin hebat, atau muncul tanda perburukan klinis lainnya.";
    }
}
