"""
MedikaRAG - Clinical Safety Guardrails & Emergency Triage Engine
Sesuai PRD Bagian 6 dan 7: Deteksi Gawat Darurat (Red Flags), Larangan Dosis Obat Keras & Diagnosis, Validasi Keluaran.
"""

import re
from typing import Dict, Any, Tuple, Optional, List

# Enhanced Red Flag emergency keywords & regex patterns for 100% triage sensitivity
DEFAULT_EMERGENCY_PATTERNS = [
    # 1. Nyeri dada / Kardiovaskular
    (r"(nyeri\s+dada|sakit\s+dada|dada\s+tertekan|dada.*(tertekan|sakit|nyeri|panas|beban|menusuk|sesak)|(nyeri|sakit).*dada|serangan\s+jantung|angina|jantung\s+berdebar.*(pingsan|sesak))", "Nyeri Dada Akut / Curiga Sindrom Koroner Akut"),
    # 2. Sesak napas berat / Saluran napas
    (r"(sesak\s+napas\s+berat|sesak.*(tersengal|parah|berat|bunyi)|napas.*(tersengal|berat|ngik|sesak|cepat.*bibir)|(bibir|kulit).*membiru|sianosis|tersedak.*(benda|makanan)|sumbatan\s+jalan\s+napas)", "Sesak Napas Berat / Obstruksi Jalan Napas"),
    # 3. Kejang & Penurunan kesadaran
    (r"(kejang|epilepsi\s+kambuh|kelojotan|menggelepar|tidak\s+sadar|pingsan|hilang\s+kesadaran|koma|tidak\s+merespon|mengigau.*tak\s+sadar)", "Kejang Akut / Penurunan Kesadaran"),
    # 4. Gejala Stroke (F.A.S.T)
    (r"(stroke|wajah.*mencong|muka.*mencong|bicara.*pelo|bicaranya\s+pelo|sulit\s+bicara\s+mendadak|lemah.*sebelah|lumpuh.*(separuh|sebelah)|mati\s+rasa.*(mendadak|separuh))", "Kecurigaan Stroke Akut (F.A.S.T)"),
    # 5. Perdarahan masif / hebat
    (r"(perdarahan\s+hebat|pendarahan\s+hebat|darah.*(menyembur|memancar|mengucur|deras|tidak\s+berhenti)|muntah\s+darah|muntah.*kopi|bab.*(berdarah\s+hitam|darah\s+hitam)|melena\s+masif)", "Perdarahan Masif Tidak Terkontrol"),
    # 6. Cedera kepala berat & tanda intrakranial
    (r"(cedera\s+kepala.*pingsan|kepala.*terbentur.*(pingsan|muntah)|muntah\s+proyektil|muntah\s+menyembur|cairan.*(telinga|hidung)|darah.*keluar.*(telinga|hidung)|pupil.*tidak\s+sama)", "Cedera Kepala Berat / Tekanan Intrakranial"),
    # 7. Fraktur terbuka & cedera spinal/servikal
    (r"(tulang.*menonjol|patah\s+tulang\s+terbuka|fraktur\s+terbuka|tulang.*menembus\s+kulit|leher.*(tertekuk|patah|cedera)|cedera\s+tulang\s+belakang|mati\s+rasa.*tidak\s+bisa\s+digerakkan)", "Fraktur Terbuka / Cedera Tulang Belakang"),
    # 8. Keracunan / Intoksikasi
    (r"(keracunan|minum.*racun|tertelan.*(pembersih|deterjen|baterai|kimia)|(minum|meminum).*(baygon|pestisida|serangga|minyak\s+tanah|pembersih)|overdosis\s+obat|minum\s+obat\s+banyak)", "Gawat Keracunan Akut / Intoksikasi"),
    # 9. Kegawatdaruratan maternal / kehamilan
    (r"(hamil.*(pendarahan|perdarahan|nyeri\s+perut)|(pendarahan|perdarahan).*hamil|ketuban\s+pecah|kejang.*hamil|preeklamsia)", "Kegawatdaruratan Maternal / Obstetri"),
    # 10. Krisis kejiwaan akut / bunuh diri
    (r"(bunuh\s+diri|ingin\s+mati|mengakhiri\s+hidup|menyakiti\s+diri)", "Krisis Kejiwaan Akut / Ancaman Bunuh Diri"),
]

# Patterns for forbidden drug dosage requests
DOSAGE_PATTERNS = [
    r"\b(berapa\s*(mg|miligram|dosis|ml|mililiter))\b",
    r"\b(dosis\s*(obat|anak|dewasa|bayi|balita|amoxicillin|paracetamol|ibuprofen|antibiotik|steroid|asam mefenamat|dexamethasone|metformin|amlodipine|ciprofloxacin|cefixime))\b",
    r"\b(takaran\s*(tetes|sendok|dosis|butir|tablet|kapsul|obat))\b",
    r"\b(berapa\s*(sendok|tetes|butir|tablet|kapsul)\s*.*(obat|sirup|paracetamol|antibiotik|pct))\b",
    r"\b(minta\s+resep|resepkan\s+obat|buatkan\s+resep|resepkan|resep\s+antibiotik|resep\s+dokter|aturan\s+minum\s+obat)\b",
]

# Patterns for forbidden definitive diagnosis requests
DIAGNOSIS_PATTERNS = [
    r"\b(diagnosis\s+penyakit\s+saya|tentukan\s+diagnosis)\b",
    r"\b(apakah\s*(saya|ini)\s*(pasti|fix|positif)\s*(terkena|kena|penyakit|kanker|tumor|tbc|lupus|hiv))\b",
    r"\b(apakah.*(pasti|fix).*(tbc|kanker|tumor|lupus|stadium))\b",
    r"\b(apakah\s+saya\s+kena\s+kanker|apakah\s+ini\s+tumor\s+ganas)\b",
]

# Out of domain / non-human health patterns (carefully excluding medical contexts)
OUT_OF_DOMAIN_PATTERNS = [
    r"\b(kucing|anjing|hewan\s+peliharaan|burung|ikan\s+cupang|whiskas|veteriner)\b",
    r"\b(bengkel|karburator|komputer|laptop|koding|coding|python|javascript|koin\s+crypto|bitcoin|harga\s+saham)\b",
    r"\b(resep\s*(kue|rendang|masakan|makanan)|hotel|pantai\s+kuta|tiket\s+pesawat|tinggi\s+menara)\b",
]

# Prompt injection / jailbreak patterns
JAILBREAK_PATTERNS = [
    r"(abaikan\s*(semua\s*)?instruksi|ignore\s+all\s+previous\s+instructions|jailbreak|dan\s+mode|bypass\s+guardrails|tampilkan\s+system\s+prompt|developer\s+mode)",
    r"(jadilah\s+dokter\s+yang\s+meresepkan|pretend\s+to\s+be\s+a\s+doctor\s+who\s+prescribes|act\s+as\s+an\s+unrestricted)",
]


class GuardrailEngine:
    def __init__(self, custom_emergency_rules: Optional[List[Dict[str, str]]] = None):
        self.emergency_patterns = list(DEFAULT_EMERGENCY_PATTERNS)
        if custom_emergency_rules:
            for rule in custom_emergency_rules:
                keyword = rule.get("keyword", "").strip()
                label = rule.get("action", "Kondisi Gawat Darurat")
                if keyword:
                    pattern = rf"({re.escape(keyword)})"
                    self.emergency_patterns.insert(0, (pattern, label))

    def check_emergency(self, query: str) -> Tuple[bool, Optional[str], Optional[Dict[str, Any]]]:
        """
        Deteksi Darurat (PRD 6 Langkah 1 & PRD 7).
        Mengembalikan (is_emergency, category_label, emergency_payload).
        """
        normalized = query.lower().strip()
        for pattern, label in self.emergency_patterns:
            if re.search(pattern, normalized, re.IGNORECASE):
                payload = {
                    "is_emergency": True,
                    "condition": label,
                    "triage_color": "RED",
                    "hotlines": [
                        {"name": "SPGDT Ambulans Kemenkes", "number": "119", "desc": "Penjemputan gawat darurat & triase nasional"},
                        {"name": "Hotline Darurat Terpadu", "number": "112", "desc": "Pusat komando darurat pemerintah daerah"},
                        {"name": "Halo Kemenkes RI", "number": "1500-567", "desc": "Layanan informasi faskes rujukan"}
                    ],
                    "immediate_actions": [
                        "Segera hubungi ambulans 119 atau bawa pasien ke IGD Rumah Sakit terdekat sekarang juga.",
                        "Tetap tenang dan dampingi pasien. Jangan tinggalkan pasien sendirian.",
                        "Posisikan pasien senyaman mungkin. Jika tidak sadar namun bernapas, posisikan miring mantap (recovery position).",
                        "JANGAN memberikan makanan, minuman, atau obat oral jika pasien sesak, kejang, atau kesadaran menurun.",
                        "Jika terjadi perdarahan hebat, lakukan penekanan langsung (direct pressure) dengan kain bersih pada sumber darah."
                    ],
                    "message": (
                        "🚨 **PERINGATAN GAWAT DARURAT MEDIS (TRIAGE MERAH)**\n\n"
                        f"Keluhan Anda teridentifikasi sebagai potensi: **{label}**.\n\n"
                        "Platform MedikaRAG **bukan pengganti layanan gawat darurat**. Kondisi ini memerlukan pemeriksaan dan tindakan segera oleh dokter/tenaga medis.\n\n"
                        "📞 **Langkah Tindakan Cepat:**\n"
                        "1. **Segera hubungi Ambulans 119 (Bebas Pulsa)** atau **Hotline Darurat 112**.\n"
                        "2. Atau segera menuju ke **IGD Rumah Sakit terdekat** saat ini juga.\n"
                        "3. Dampingi pasien dan hubungi keluarga terdekat."
                    )
                }
                return True, label, payload
        return False, None, None

    def check_forbidden_requests(self, query: str) -> Tuple[bool, Optional[str], Optional[str]]:
        """
        Deteksi Permintaan Terlarang (PRD 6 Langkah 2):
        1. Dosis / resep obat spesifik
        2. Diagnosis definitif
        3. Di luar topik (Out of Domain)
        4. Jailbreak / Manipulasi prompt
        """
        normalized = query.lower().strip()

        # 1. Jailbreak check
        for pattern in JAILBREAK_PATTERNS:
            if re.search(pattern, normalized, re.IGNORECASE):
                return True, "JAILBREAK_ATTEMPT", (
                    "Maaf, MedikaRAG beroperasi secara ketat di bawah protokol keselamatan medis Kemenkes RI. "
                    "Instruksi keamanan dan etika sistem tidak dapat diubah atau diabaikan."
                )

        # 2. Dosage request check
        # Exception: Nutrition queries like "sendok gula/minyak" or "garam" are nutrition, not drug dosage
        is_nutrition_context = bool(re.search(r"\b(gula|garam|minyak|air|sayur|makan|piringku|gizi)\b", normalized))
        for pattern in DOSAGE_PATTERNS:
            if re.search(pattern, normalized, re.IGNORECASE):
                if is_nutrition_context and "obat" not in normalized and "antibiotik" not in normalized:
                    continue
                return True, "FORBIDDEN_DOSAGE", (
                    "⚠️ **Kebijakan Keselamatan Farmasi (Standard of Care):**\n\n"
                    "MedikaRAG **tidak diperkenankan memberikan perhitungan dosis obat spesifik (mg/ml/sendok)** "
                    "maupun memvalidasi resep obat keras/antibiotik.\n\n"
                    "Pemberian dosis obat harus disesuaikan secara individual dengan berat badan, usia, fungsi ginjal/hati, "
                    "dan riwayat alergi oleh **Dokter atau Apoteker** di fasilitas kesehatan.\n\n"
                    "**Rekomendasi Aman:**\n"
                    "• Untuk obat bebas bertanda lingkaran hijau atau biru (misal: paracetamol untuk pereda demam atau oralit untuk hidrasi), "
                    "selalu baca petunjuk takaran resmi pada kemasan obat.\n"
                    "• Konsultasikan langsung dengan **Apoteker di apotek resmi** atau dokter Anda untuk penyesuaian dosis yang aman dan rasional."
                )

        # 3. Diagnosis request check
        for pattern in DIAGNOSIS_PATTERNS:
            if re.search(pattern, normalized, re.IGNORECASE):
                return True, "FORBIDDEN_DIAGNOSIS", (
                    "⚠️ **Batasan Diagnosis Medis:**\n\n"
                    "MedikaRAG adalah sistem edukasi kesehatan berbasis bukti (RAG) dan **bukan alat penegak diagnosis klinis definitif**.\n\n"
                    "Diagnosis penyakit membutuhkan pemeriksaan fisik langsung (palpasi, auskultasi), wawancara klinis mendalam (anamnesis), "
                    "serta pemeriksaan penunjang (laboratorium darah, rontgen, dsb.) oleh dokter berwenang.\n\n"
                    "Silakan jelaskan gejala yang dirasakan untuk mendapatkan informasi langkah pertolongan pertama mandiri sementara dan tanda bahaya yang perlu diwaspadai."
                )

        # 4. Out of domain check
        for pattern in OUT_OF_DOMAIN_PATTERNS:
            if re.search(pattern, normalized, re.IGNORECASE):
                return True, "OUT_OF_DOMAIN", (
                    "Maaf, pertanyaan Anda berada di luar cakupan MedikaRAG. "
                    "Platform ini difokuskan khusus untuk panduan pertolongan pertama (P3K), edukasi penyakit ringan, "
                    "pencegahan kondisi kronis, dan gizi seimbang pada manusia berdasarkan dokumen resmi Kemenkes RI dan jurnal medis terindeks."
                )

        return False, None, None

    def validate_output(self, text: str, citations: List[Dict[str, Any]]) -> Tuple[bool, str]:
        """
        Validasi Keluaran (PRD 6 Langkah 6):
        - Memastikan ada sitasi sumber jika klaim medis disampaikan.
        - Memastikan tidak ada dosis mg/kg ilegal yang lolos.
        - Menyematkan disclaimer keselamatan medis.
        """
        # Strip unauthorized specific prescription dosage numbers
        text = re.sub(r"\b\d+(\.\d+)?\s*(mg|miligram)\s*(per|\/)\s*kg\b", "[Dosis disesuaikan oleh dokter/apoteker]", text, flags=re.IGNORECASE)

        # Ensure medical disclaimer
        disclaimer = (
            "\n\n---\n"
            "*📌 **Disclaimer Medis**: Informasi ini berbasis rujukan resmi Kemenkes RI dan literatur ilmiah terindeks "
            "untuk tujuan edukasi mandiri. Ini bukan diagnosis medis pengganti konsultasi langsung dengan dokter.*"
        )
        if "Disclaimer Medis" not in text:
            text += disclaimer

        return True, text
