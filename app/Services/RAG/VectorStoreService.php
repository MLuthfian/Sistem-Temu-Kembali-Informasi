<?php

namespace App\Services\RAG;

use App\Models\Chunk;

class VectorStoreService
{
    protected array $stopwords = [
        "yang", "di", "ke", "dari", "ini", "itu", "untuk", "pada", "adalah", "dan", "atau",
        "dengan", "ia", "dia", "mereka", "kita", "kami", "kamu", "anda", "saya", "aku",
        "bisa", "dapat", "akan", "telah", "sudah", "sedang", "lagi", "pun", "saja", "juga",
        "hanya", "oleh", "sebagai", "dalam", "atas", "bawah", "antara", "tentang", "bagi",
        "secara", "karena", "sehingga", "maka", "jika", "bila", "kalau", "apabila", "namun",
        "tetapi", "melainkan", "lalu", "kemudian", "setelah", "sebelum", "saat", "waktu",
        "ada", "tidak", "bukan", "jangan", "harus", "boleh", "mengapa", "kenapa", "bagaimana",
        "apa", "siapa", "mana", "kapan", "hal", "para", "suatu", "sebuah", "tersebut", "terlalu"
    ];

    protected array $genericProceduralTerms = [
        "bagaimana", "cara", "protokol", "penanganan", "mandiri", "pertolongan", "langkah",
        "tindakan", "penyakit", "apakah", "terapi", "bisa", "pertama", "sendiri", "rumah",
        "panduan", "perawatan", "gejala", "kondisi", "bantuan", "informasi", "aturan",
        "mengobati", "mengatasi", "menyembuhkan", "pengobatan", "merawat", "mencegah", 
        "konsumsi", "memakai", "menggunakan", "tips", "solusi", "resep"
    ];

    protected array $medicalSynonyms = [
        "keseleo" => ["terkilir", "sprain", "strain", "rice", "rest", "ice", "compression", "elevation", "sendi", "pergelangan"],
        "terkilir" => ["keseleo", "sprain", "strain", "rice", "kompres", "es", "ligamen", "harm"],
        "pergelangan" => ["kaki", "sendi", "keseleo", "terkilir", "ankle", "sprain"],
        "patah" => ["fraktur", "tulang", "imobilisasi", "bidai", "pembidaian"],
        "fraktur" => ["patah", "tulang", "imobilisasi", "bidai", "pembidaian"],
        "bidai" => ["pembidaian", "imobilisasi", "patah", "tulang", "fraktur"],
        "batuk" => ["ispa", "pilek", "flu", "tenggorokan", "dahak", "hidrasi", "madu", "saluran napas"],
        "pilek" => ["ispa", "batuk", "hidung", "tersumbat", "flu", "ingus", "salin"],
        "flu" => ["ispa", "batuk", "pilek", "influenza", "virus"],
        "tersumbat" => ["hidung", "salin", "nacl", "ispa", "bilas"],
        "demam" => ["suhu", "panas", "termoregulasi", "antipiretik", "paracetamol", "kompres hangat", "tepid", "sponge"],
        "panas" => ["demam", "suhu", "kompres hangat", "hidrasi", "tepid", "bakar", "tersiram"],
        "diare" => ["mencret", "bab cair", "oralit", "dehidrasi", "lintas diare", "zinc", "cairan"],
        "mencret" => ["diare", "bab cair", "oralit", "dehidrasi", "zinc"],
        "oralit" => ["diare", "hidrasi", "cairan", "elektrolit", "osmolaritas"],
        "luka" => ["lecet", "terbuka", "irigasi", "povidone", "iodine", "kassa", "tetanus", "antiseptik"],
        "lecet" => ["luka", "irigasi", "air mengalir", "povidone", "antiseptik"],
        "bakar" => ["luka bakar", "melepuh", "air mengalir", "kassa", "steril", "panas", "tersiram"],
        "tersiram" => ["luka bakar", "bakar", "panas", "air mengalir", "melepuh"],
        "odol" => ["pasta gigi", "luka bakar", "dilarang", "air mengalir"],
        "kecap" => ["luka bakar", "dilarang", "air mengalir"],
        "dbd" => ["dengue", "demam berdarah", "aedes", "trombosit", "pelana kuda", "hematokrit", "cairan"],
        "dengue" => ["dbd", "demam berdarah", "pelana kuda", "trombosit", "cairan"],
        "hipertensi" => ["darah tinggi", "tensi", "garam", "natrium", "cerdik", "kardiovaskular"],
        "darah tinggi" => ["hipertensi", "tensi", "garam", "natrium", "cerdik"],
        "tensi" => ["hipertensi", "garam", "olahraga", "cerdik"],
        "diabetes" => ["gula darah", "kencing manis", "insulin", "glukosa", "hipoglikemia", "3j", "kaki"],
        "gula darah" => ["diabetes", "kencing manis", "glukosa", "hipoglikemia"],
        "hipoglikemia" => ["diabetes", "gemetar", "keringat dingin", "teh manis", "aturan 15"],
        "pusing" => ["sakit kepala", "tension", "migren", "nyeri kepala", "istirahat", "leher"],
        "sakit kepala" => ["pusing", "migren", "tension", "nyeri kepala", "istirahat"],
        "maag" => ["lambung", "asam lambung", "dispepsia", "gerd", "ulu hati", "antasida"],
        "asam lambung" => ["maag", "dispepsia", "ulu hati", "antasida", "lambung"],
        "dispepsia" => ["maag", "asam lambung", "porsi kecil", "ulu hati"],
        "mimisan" => ["epistaksis", "hidung berdarah", "tekan cuping", "condong depan"],
        "epistaksis" => ["mimisan", "cuping", "hidung"],
        "gizi" => ["nutrisi", "isi piringku", "seimbang", "karbohidrat", "protein", "garam", "gula", "sayur"],
        "nutrisi" => ["gizi", "seimbang", "piringku", "hidrasi"],
        "jurnal" => ["scopus", "sinta", "penelitian", "studi", "bjsm", "jki", "bmc"],
        "kompres" => ["es", "hangat", "tepid", "dingin", "suhu"],
        "es" => ["kompres", "rice", "dingin", "bengkak"]
    ];

    protected ?array $chunks = null;
    protected array $vocab = [];
    protected array $idf = [];
    protected array $tfidfDocs = [];
    protected array $chunkTokens = [];

    public function __construct()
    {
        $this->loadIndex();
    }

    public function loadIndex(): void
    {
        $allChunks = Chunk::all()->toArray();
        $this->chunks = $allChunks;
        $docCount = count($allChunks);

        if ($docCount === 0) {
            return;
        }

        $df = [];
        $tokenizedDocs = [];
        $this->chunkTokens = [];

        foreach ($allChunks as $chunk) {
            $text = ($chunk['judul_dokumen'] ?? '') . ' ' . ($chunk['topik'] ?? '') . ' ' . ($chunk['text'] ?? '');
            $tokens = $this->tokenize($text);
            $tokenizedDocs[] = $tokens;
            $this->chunkTokens[] = array_flip($tokens);

            $unique = array_unique($tokens);
            foreach ($unique as $t) {
                $df[$t] = ($df[$t] ?? 0) + 1;
            }
        }

        $this->vocab = array_keys($df);
        $this->idf = [];
        foreach ($df as $term => $freq) {
            $this->idf[$term] = log(($docCount + 1.0) / ($freq + 1.0)) + 1.0;
        }

        // Build normalized tf-idf vectors
        $this->tfidfDocs = [];
        foreach ($tokenizedDocs as $tokens) {
            $tf = [];
            foreach ($tokens as $t) {
                $tf[$t] = ($tf[$t] ?? 0) + 1;
            }

            $vector = [];
            $normSq = 0.0;
            foreach ($tf as $t => $count) {
                if (isset($this->idf[$t])) {
                    $val = (1.0 + log($count)) * $this->idf[$t];
                    $vector[$t] = $val;
                    $normSq += $val * $val;
                }
            }

            $norm = sqrt($normSq);
            if ($norm > 0) {
                foreach ($vector as $t => $val) {
                    $vector[$t] = $val / $norm;
                }
            }
            $this->tfidfDocs[] = $vector;
        }
    }

    public function tokenize(string $text): array
    {
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', mb_strtolower($text));
        $words = preg_split('/\s+/u', $cleaned, -1, PREG_SPLIT_NO_EMPTY);
        $stopSet = array_flip($this->stopwords);

        $tokens = [];
        foreach ($words as $w) {
            if (mb_strlen($w) > 1 && !isset($stopSet[$w])) {
                $tokens[] = $w;
            }
        }
        return $tokens;
    }

    public function expandQuery(string $query): array
    {
        $baseTokens = $this->tokenize($query);
        $expanded = $baseTokens;

        foreach ($baseTokens as $t) {
            if (isset($this->medicalSynonyms[$t])) {
                foreach ($this->medicalSynonyms[$t] as $syn) {
                    $expanded[] = $syn;
                }
            }
        }

        return [$baseTokens, array_values(array_unique($expanded))];
    }

    public function search(
        string $query,
        int $topK = 4,
        float $minScore = 0.20,
        ?string $layerFilter = null,
        ?string $topicFilter = null
    ): array {
        if (empty($this->chunks) || empty($this->tfidfDocs)) {
            $this->loadIndex();
        }

        if (empty($this->chunks)) {
            return [];
        }

        [$baseTokens, $expandedTokens] = $this->expandQuery($query);
        if (empty($expandedTokens)) {
            return [];
        }

        // Substantive tokens
        $genericSet = array_flip($this->genericProceduralTerms);
        $substantiveTokens = array_filter($baseTokens, fn($t) => !isset($genericSet[$t]));

        $substantiveExpanded = [];
        foreach ($substantiveTokens as $t) {
            $substantiveExpanded[$t] = true;
            if (isset($this->medicalSynonyms[$t])) {
                foreach ($this->medicalSynonyms[$t] as $syn) {
                    $substantiveExpanded[$syn] = true;
                }
            }
        }

        // Build normalized query vector
        $queryTf = [];
        foreach ($expandedTokens as $t) {
            $queryTf[$t] = ($queryTf[$t] ?? 0) + 1;
        }

        $queryVec = [];
        $normSq = 0.0;
        foreach ($queryTf as $t => $count) {
            if (isset($this->idf[$t])) {
                $val = (1.0 + log($count)) * $this->idf[$t];
                $queryVec[$t] = $val;
                $normSq += $val * $val;
            }
        }

        $qNorm = sqrt($normSq);
        if ($qNorm > 0) {
            foreach ($queryVec as $t => $val) {
                $queryVec[$t] = $val / $qNorm;
            }
        } else {
            return [];
        }

        $results = [];
        $baseSet = array_flip($baseTokens);
        $totalBase = count($baseSet);
        $queryLower = mb_strtolower($query);

        foreach ($this->chunks as $idx => $chunk) {
            if ($layerFilter && ($chunk['lapisan'] ?? '') !== $layerFilter) {
                continue;
            }
            if ($topicFilter && mb_strtolower($chunk['topik'] ?? '') !== mb_strtolower($topicFilter)) {
                continue;
            }

            $chunkToks = $this->chunkTokens[$idx];

            // Substantive gate: if query has substantive terms, substantive overlap must be sufficient
            if (!empty($substantiveTokens)) {
                $matchedSubstantive = 0;
                foreach ($substantiveTokens as $st) {
                    if (isset($chunkToks[$st])) {
                        $matchedSubstantive++;
                    } elseif (isset($this->medicalSynonyms[$st])) {
                        foreach ($this->medicalSynonyms[$st] as $syn) {
                            if (isset($chunkToks[$syn])) {
                                $matchedSubstantive++;
                                break;
                            }
                        }
                    }
                }

                $substantiveRatio = $matchedSubstantive / count($substantiveTokens);
                if (count($substantiveTokens) >= 2 && $substantiveRatio < 0.40) {
                    continue;
                }
                if (count($substantiveTokens) === 1 && $matchedSubstantive === 0) {
                    continue;
                }
            }

            // Cosine similarity
            $cosScore = 0.0;
            $docVec = $this->tfidfDocs[$idx];
            foreach ($queryVec as $t => $qVal) {
                if (isset($docVec[$t])) {
                    $cosScore += $qVal * $docVec[$t];
                }
            }

            // Term overlap
            $matchedBase = 0;
            foreach ($baseSet as $t => $_) {
                if (isset($chunkToks[$t])) {
                    $matchedBase++;
                }
            }
            $overlapRatio = $totalBase > 0 ? ($matchedBase / $totalBase) : 0.0;

            // Hybrid score: 40% Cosine + 60% Overlap Ratio
            $hybridScore = (0.40 * $cosScore) + (0.60 * $overlapRatio);

            // Boost topic or document title
            if (!empty($chunk['topik']) && str_contains($queryLower, mb_strtolower($chunk['topik']))) {
                $hybridScore += 0.10;
            }
            if (!empty($chunk['judul_dokumen']) && str_contains($queryLower, mb_strtolower($chunk['judul_dokumen']))) {
                $hybridScore += 0.08;
            }

            $finalScore = round(min($hybridScore, 1.0), 4);

            if ($finalScore >= $minScore) {
                $item = $chunk;
                $item['score'] = $finalScore;
                $results[] = $item;
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $topK);
    }

    public function getStats(): array
    {
        $allChunks = Chunk::all();
        $layer1 = $allChunks->filter(fn($c) => str_contains($c->lapisan ?? '', '1'))->count();
        $layer2 = $allChunks->filter(fn($c) => str_contains($c->lapisan ?? '', '2'))->count();
        $uniqueDocs = $allChunks->pluck('judul_dokumen')->unique()->count();

        return [
            'total_chunks' => $allChunks->count(),
            'unique_documents' => $uniqueDocs,
            'vocab_size' => count($this->vocab),
            'layer1_chunks' => $layer1,
            'layer2_chunks' => $layer2,
            'is_indexed' => !empty($this->chunks)
        ];
    }
}

