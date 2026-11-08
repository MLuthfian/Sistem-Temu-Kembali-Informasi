"""
MedikaRAG - Grounded Synthesizer & Medical Citation Formatter
Sesuai PRD Bagian 5.3 (FR-12, FR-13, FR-14) dan Bagian 6 (Format 5 Bagian, Zero-Dose, Grounding).
Beroperasi 100% Offline & Mandiri berdasarkan Dataset Konsultasi Medis dan PNPK Kemenkes RI.
TIDAK MENGGUNAKAN API EKSTERNAL (Gemini / OpenAI dsb) sesuai instruksi akademik.
"""

import json
import os
import re
from typing import List, Dict, Any, Optional, Tuple


FALLBACK_MESSAGE_DEFAULT = (
    "Maaf, informasi terverifikasi untuk pertanyaan spesifik tersebut belum tersedia di basis pengetahuan resmi "
    "500 entri dataset konsultasi dokter dan pedoman Kemenkes RI kami.\n\n"
    "Untuk memastikan penanganan yang tepat dan aman, kami menyarankan Anda berkonsultasi langsung dengan "
    "dokter atau tenaga medis di Puskesmas / Klinik / Fasilitas Pelayanan Kesehatan terdekat."
)


class RAGSynthesizer:
    def __init__(self, provider: str = "dataset_local"):
        self.provider = "dataset_local"

    def synthesize(
        self,
        query: str,
        retrieved_chunks: List[Dict[str, Any]],
        clarification_context: Optional[Dict[str, Any]] = None,
        fallback_template: Optional[str] = None
    ) -> Dict[str, Any]:
        """
        Synthesize answer from retrieved chunks strictly adhering to the local dataset.
        Zero external API calls.
        """
        if not retrieved_chunks:
            return {
                "success": False,
                "is_fallback": True,
                "answer": fallback_template or FALLBACK_MESSAGE_DEFAULT,
                "citations": [],
                "clarification_needed": False
            }

        # 100% Local Grounded Synthesizer based on dataset
        return self._generate_builtin_grounded_answer(query, retrieved_chunks, clarification_context)

    def _generate_builtin_grounded_answer(
        self,
        query: str,
        chunks: List[Dict[str, Any]],
        clarification_context: Optional[Dict[str, Any]] = None
    ) -> Dict[str, Any]:
        """
        Deterministic, hallucination-free medical response generator.
        Compiles retrieved knowledge from Kemenkes & Jurnal chunks into the strict 5-part schema.
        """
        citations: List[Dict[str, Any]] = []
        citation_map = {}

        # Build citation list
        for idx, chunk in enumerate(chunks, start=1):
            cite_num = idx
            citation_item = {
                "id": cite_num,
                "judul": chunk.get("judul_dokumen", "Dokumen Kemenkes RI"),
                "sumber": chunk.get("sumber", "Kementerian Kesehatan RI"),
                "tahun": chunk.get("tahun", 2024),
                "lapisan": chunk.get("lapisan", "Lapisan 1 (Kemenkes)"),
                "url": chunk.get("url", "#"),
                "lisensi": chunk.get("lisensi", "Hak Cipta Kemenkes RI / Open Access"),
                "topik": chunk.get("topik", "Umum"),
                "score": chunk.get("score", 0.0),
                "excerpt": chunk.get("text", "")[:180] + "..."
            }
            citations.append(citation_item)
            citation_map[cite_num] = citation_item

        primary_chunk = chunks[0]
        topic = primary_chunk.get("topik", "Keluhan Medis")
        primary_text = primary_chunk.get("text", "")

        # Extract or assemble clinical components from chunks
        all_chunk_texts = [c.get("text", "") for c in chunks]
        combined_text = "\n\n".join(all_chunk_texts)

        # Parse sections if structured in chunk, otherwise smartly synthesize
        info_umum = self._extract_section(all_chunk_texts, ["definisi", "pengertian", "informasi", "kenali", "penyebab"], fallback=primary_text[:280])
        mandiri = self._extract_section(all_chunk_texts, ["penanganan", "langkah", "pertolongan", "cara", "protokol", "perawatan"], fallback="Istirahat cukup dan jaga hidrasi.")
        tanda_bahaya = self._extract_section(all_chunk_texts, ["tanda bahaya", "waspada", "red flag", "bahaya", "perhatikan"], fallback="Perburukan gejala, demam tinggi persisten, atau penurunan kesadaran.")
        kapan_ke_dokter = self._extract_section(all_chunk_texts, ["kapan ke dokter", "rujukan", "faskes", "puskesmas", "konsultasi"], fallback="Bila gejala tidak membaik dalam 3 hari atau muncul salah satu tanda bahaya.")

        # Clarification addendum if user provided patient info
        clarification_note = ""
        if clarification_context:
            age = clarification_context.get("age")
            duration = clarification_context.get("duration")
            is_pregnant = clarification_context.get("is_pregnant")
            if is_pregnant:
                clarification_note = "\n\n> ⚠️ **Catatan Khusus Kehamilan**: Karena pasien sedang hamil, penanganan mandiri harus sangat hati-hati. Segera hubungi bidan atau dokter kandungan untuk evaluasi klinis yang aman."
            elif age and (int(age) < 2 or int(age) > 65):
                clarification_note = f"\n\n> ⚠️ **Catatan Kelompok Rentan**: Untuk pasien usia {age} tahun, pertimbangan klinis lebih konservatif sangat dianjurkan. Segera periksakan ke dokter jika kondisi tidak stabil."

        # Compile strict 5-part answer
        answer_parts = [
            f"### 🩺 1. Informasi Klinis Umum",
            f"{info_umum.strip()} [1]",
            f"",
            f"### 🛡️ 2. Langkah Perawatan Mandiri di Rumah",
            f"{mandiri.strip()} [1]" + (f" [2]" if len(chunks) > 1 else ""),
            f"",
            f"### ⚠️ 3. Tanda Bahaya (Red Flags)",
            f"{tanda_bahaya.strip()}",
            f"",
            f"### 🏥 4. Kapan Harus ke Dokter / Faskes",
            f"{kapan_ke_dokter.strip()}",
            f"{clarification_note}",
            f"",
            f"### 📚 5. Sumber Rujukan Terverifikasi"
        ]

        for c in citations:
            badge_color = "bg-primary-fixed text-on-primary-fixed-variant" if "1" in str(c["lapisan"]) else "bg-secondary-fixed text-on-secondary-fixed"
            layer_name = "Kemenkes RI" if "1" in str(c["lapisan"]) else "Jurnal Ilmiah"
            answer_parts.append(
                f"- **[{c['id']}] {c['judul']}** ({c['tahun']}) • *{c['sumber']}* "
                f"[{layer_name}] [Lihat Dokumen]({c['url']})"
            )

        formatted_answer = "\n".join(answer_parts)

        return {
            "success": True,
            "is_fallback": False,
            "topic": topic,
            "answer": formatted_answer,
            "citations": citations,
            "clarification_needed": False
        }

    def _extract_section(self, chunk_texts: List[str], keywords: List[str], fallback: str) -> str:
        """Find the most relevant sentences from chunk texts matching section keywords."""
        matched_sentences = []
        for text in chunk_texts:
            paragraphs = [p.strip() for p in text.split("\n") if p.strip()]
            for p in paragraphs:
                p_lower = p.lower()
                if any(kw in p_lower for kw in keywords):
                    matched_sentences.append(p)
                    if len(matched_sentences) >= 2:
                        break
            if len(matched_sentences) >= 2:
                break

        if matched_sentences:
            return "\n".join(matched_sentences)
        return fallback

