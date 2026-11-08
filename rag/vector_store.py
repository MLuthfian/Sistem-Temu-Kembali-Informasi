"""
MedikaRAG - Vector Store & Semantic Retrieval Pipeline
Sesuai PRD Bagian 6 (Alur Chatbot RAG, Retrieval Top-K, Lapisan 1 Kemenkes & Lapisan 2 Jurnal).
Menggunakan Hybrid Cosine TF-IDF + BM25 Substantive Term Overlap dengan Semantic Medical Boosting.
"""

import math
import re
from typing import List, Dict, Any, Tuple, Optional
import numpy as np

# Indonesian medical & general stopwords
STOPWORDS_ID = {
    "yang", "di", "ke", "dari", "ini", "itu", "untuk", "pada", "adalah", "dan", "atau",
    "dengan", "ia", "dia", "mereka", "kita", "kami", "kamu", "anda", "saya", "aku",
    "bisa", "dapat", "akan", "telah", "sudah", "sedang", "lagi", "pun", "saja", "juga",
    "hanya", "oleh", "sebagai", "dalam", "atas", "bawah", "antara", "tentang", "bagi",
    "secara", "karena", "sehingga", "maka", "jika", "bila", "kalau", "apabila", "namun",
    "tetapi", "melainkan", "lalu", "kemudian", "setelah", "sebelum", "saat", "waktu",
    "ada", "tidak", "bukan", "jangan", "harus", "boleh", "mengapa", "kenapa", "bagaimana",
    "apa", "siapa", "mana", "kapan", "hal", "para", "suatu", "sebuah", "tersebut", "terlalu"
}

# Generic procedural words that shouldn't falsely match unrelated rare diseases
GENERIC_PROCEDURAL_TERMS = {
    "bagaimana", "cara", "protokol", "penanganan", "mandiri", "pertolongan", "langkah",
    "tindakan", "penyakit", "apakah", "terapi", "bisa", "pertama", "sendiri", "rumah",
    "panduan", "perawatan", "gejala", "kondisi", "bantuan", "informasi", "aturan"
}

# Medical synonym expansion to boost semantic recall in Indonesian healthcare
MEDICAL_SYNONYMS = {
    "keseleo": ["terkilir", "sprain", "strain", "rice", "rest", "ice", "compression", "elevation", "sendi", "pergelangan"],
    "terkilir": ["keseleo", "sprain", "strain", "rice", "kompres", "es", "ligamen", "harm"],
    "pergelangan": ["kaki", "sendi", "keseleo", "terkilir", "ankle", "sprain"],
    "patah": ["fraktur", "tulang", "imobilisasi", "bidai", "pembidaian"],
    "fraktur": ["patah", "tulang", "imobilisasi", "bidai", "pembidaian"],
    "bidai": ["pembidaian", "imobilisasi", "patah", "tulang", "fraktur"],
    "batuk": ["ispa", "pilek", "flu", "tenggorokan", "dahak", "hidrasi", "madu", "saluran napas"],
    "pilek": ["ispa", "batuk", "hidung", "tersumbat", "flu", "ingus", "salin"],
    "flu": ["ispa", "batuk", "pilek", "influenza", "virus"],
    "tersumbat": ["hidung", "salin", "nacl", "ispa", "bilas"],
    "demam": ["suhu", "panas", "termoregulasi", "antipiretik", "paracetamol", "kompres hangat", "tepid", "sponge"],
    "panas": ["demam", "suhu", "kompres hangat", "hidrasi", "tepid", "bakar", "tersiram"],
    "diare": ["mencret", "bab cair", "oralit", "dehidrasi", "lintas diare", "zinc", "cairan"],
    "mencret": ["diare", "bab cair", "oralit", "dehidrasi", "zinc"],
    "oralit": ["diare", "hidrasi", "cairan", "elektrolit", "osmolaritas"],
    "luka": ["lecet", "terbuka", "irigasi", "povidone", "iodine", "kassa", "tetanus", "antiseptik"],
    "lecet": ["luka", "irigasi", "air mengalir", "povidone", "antiseptik"],
    "bakar": ["luka bakar", "melepuh", "air mengalir", "kassa", "steril", "panas", "tersiram"],
    "tersiram": ["luka bakar", "bakar", "panas", "air mengalir", "melepuh"],
    "odol": ["pasta gigi", "luka bakar", "dilarang", "air mengalir"],
    "kecap": ["luka bakar", "dilarang", "air mengalir"],
    "dbd": ["dengue", "demam berdarah", "aedes", "trombosit", "pelana kuda", "hematokrit", "cairan"],
    "dengue": ["dbd", "demam berdarah", "pelana kuda", "trombosit", "cairan"],
    "hipertensi": ["darah tinggi", "tensi", "garam", "natrium", "cerdik", "kardiovaskular"],
    "darah tinggi": ["hipertensi", "tensi", "garam", "natrium", "cerdik"],
    "tensi": ["hipertensi", "garam", "olahraga", "cerdik"],
    "diabetes": ["gula darah", "kencing manis", "insulin", "glukosa", "hipoglikemia", "3j", "kaki"],
    "gula darah": ["diabetes", "kencing manis", "glukosa", "hipoglikemia"],
    "hipoglikemia": ["diabetes", "gemetar", "keringat dingin", "teh manis", "aturan 15"],
    "pusing": ["sakit kepala", "tension", "migren", "nyeri kepala", "istirahat", "leher"],
    "sakit kepala": ["pusing", "migren", "tension", "nyeri kepala", "istirahat"],
    "maag": ["lambung", "asam lambung", "dispepsia", "gerd", "ulu hati", "antasida"],
    "asam lambung": ["maag", "dispepsia", "ulu hati", "antasida", "lambung"],
    "dispepsia": ["maag", "asam lambung", "porsi kecil", "ulu hati"],
    "mimisan": ["epistaksis", "hidung berdarah", "tekan cuping", "condong depan"],
    "epistaksis": ["mimisan", "cuping", "hidung"],
    "gizi": ["nutrisi", "isi piringku", "seimbang", "karbohidrat", "protein", "garam", "gula", "sayur"],
    "nutrisi": ["gizi", "seimbang", "piringku", "hidrasi"],
    "jurnal": ["scopus", "sinta", "penelitian", "studi", "bjsm", "jki", "bmc"],
    "kompres": ["es", "hangat", "tepid", "dingin", "suhu"],
    "es": ["kompres", "rice", "dingin", "bengkak"]
}


def tokenize(text: str, remove_stopwords: bool = True) -> List[str]:
    """Tokenize and normalize text into clean words."""
    cleaned = re.sub(r"[^\w\s]", " ", text.lower())
    tokens = [w for w in cleaned.split() if len(w) > 1]
    if remove_stopwords:
        tokens = [w for w in tokens if w not in STOPWORDS_ID]
    return tokens


class VectorStore:
    def __init__(self):
        self.chunks: List[Dict[str, Any]] = []
        self.vocab: Dict[str, int] = {}
        self.idf: Dict[str, float] = {}
        self.tfidf_matrix: Optional[np.ndarray] = None
        self.chunk_tokens: List[set] = []
        self.is_indexed: bool = False

    def add_chunks(self, new_chunks: List[Dict[str, Any]]):
        """Append chunks to vector store and rebuild index."""
        for chunk in new_chunks:
            self.chunks.append(chunk)
        self.build_index()

    def build_index(self):
        """
        Build TF-IDF Vector Index with Sublinear Term Frequency and Cosine Normalization.
        """
        if not self.chunks:
            self.is_indexed = False
            return

        doc_count = len(self.chunks)
        df: Dict[str, int] = {}
        tokenized_docs: List[List[str]] = []
        self.chunk_tokens = []

        for chunk in self.chunks:
            full_text = f"{chunk.get('judul_dokumen', '')} {chunk.get('topik', '')} {chunk.get('text', '')}"
            tokens = tokenize(full_text)
            tokenized_docs.append(tokens)
            self.chunk_tokens.append(set(tokens))

            unique_terms = set(tokens)
            for term in unique_terms:
                df[term] = df.get(term, 0) + 1

        self.vocab = {term: idx for idx, (term, freq) in enumerate(df.items()) if freq >= 1}
        vocab_size = len(self.vocab)
        self.idf = {}

        for term, freq in df.items():
            if term in self.vocab:
                self.idf[term] = math.log((doc_count + 1.0) / (freq + 1.0)) + 1.0

        matrix = np.zeros((doc_count, vocab_size), dtype=np.float32)

        for i, tokens in enumerate(tokenized_docs):
            if not tokens:
                continue
            tf_counts: Dict[str, int] = {}
            for token in tokens:
                if token in self.vocab:
                    tf_counts[token] = tf_counts.get(token, 0) + 1

            for token, count in tf_counts.items():
                col_idx = self.vocab[token]
                tf_val = 1.0 + math.log(count)
                matrix[i, col_idx] = tf_val * self.idf[token]

            norm = np.linalg.norm(matrix[i])
            if norm > 0:
                matrix[i] = matrix[i] / norm

        self.tfidf_matrix = matrix
        self.is_indexed = True

    def expand_query_with_synonyms(self, query: str) -> Tuple[List[str], List[str]]:
        """Return base tokens and expanded tokens with clinical synonyms."""
        base_tokens = tokenize(query)
        expanded = list(base_tokens)

        for token in base_tokens:
            if token in MEDICAL_SYNONYMS:
                expanded.extend(MEDICAL_SYNONYMS[token][:4])

        return base_tokens, list(set(expanded))

    def search(
        self,
        query: str,
        top_k: int = 4,
        min_score: float = 0.20,
        layer_filter: Optional[str] = None,
        topic_filter: Optional[str] = None
    ) -> List[Dict[str, Any]]:
        """
        Hybrid Top-K Retrieval from Vector Store (PRD 6 Langkah 3):
        Combines Cosine TF-IDF similarity with BM25 substantive term overlap.
        """
        if not self.is_indexed or self.tfidf_matrix is None or not self.chunks:
            return []

        base_tokens, expanded_tokens = self.expand_query_with_synonyms(query)
        if not expanded_tokens:
            return []

        # Substantive tokens: filter out pure procedural words
        substantive_tokens = [t for t in base_tokens if t not in GENERIC_PROCEDURAL_TERMS]
        substantive_set = set(substantive_tokens)

        # Build substantive expanded set (specifically only substantive words and their synonyms)
        substantive_expanded_set = set(substantive_set)
        for t in substantive_tokens:
            if t in MEDICAL_SYNONYMS:
                substantive_expanded_set.update(MEDICAL_SYNONYMS[t])

        # Build Query Vector
        vocab_size = len(self.vocab)
        query_vec = np.zeros((vocab_size,), dtype=np.float32)

        tf_counts: Dict[str, int] = {}
        for token in expanded_tokens:
            if token in self.vocab:
                tf_counts[token] = tf_counts.get(token, 0) + 1

        for token, count in tf_counts.items():
            col_idx = self.vocab[token]
            tf_val = 1.0 + math.log(count)
            query_vec[col_idx] = tf_val * self.idf.get(token, 1.0)

        # L2 normalize query vector
        norm = np.linalg.norm(query_vec)
        if norm > 0:
            query_vec = query_vec / norm
        else:
            return []

        # Cosine Similarity
        cosine_scores = np.dot(self.tfidf_matrix, query_vec)

        # Calculate Hybrid Scores
        results = []
        base_token_set = set(base_tokens)

        for idx, cos_score in enumerate(cosine_scores):
            chunk = self.chunks[idx]

            # Filters
            if layer_filter and chunk.get("lapisan") != layer_filter:
                continue
            if topic_filter and chunk.get("topik", "").lower() != topic_filter.lower():
                continue

            chunk_toks = self.chunk_tokens[idx]

            # CRITICAL: If the query has substantive clinical nouns (e.g. "Huntington", "ALS", "prion"),
            # but NONE of them (or their synonyms) match this chunk, reject the chunk!
            if substantive_expanded_set:
                matched_substantive = substantive_expanded_set.intersection(chunk_toks)
                if not matched_substantive:
                    continue

            # Exact term overlap ratio with user base query
            if base_token_set:
                matched_base = len(base_token_set.intersection(chunk_toks))
                overlap_ratio = matched_base / len(base_token_set)
            else:
                overlap_ratio = 0.0

            # Hybrid score
            hybrid_score = float(0.40 * cos_score + 0.60 * overlap_ratio)

            # Boost for topic match
            query_lower = query.lower()
            if chunk.get("topik", "").lower() in query_lower:
                hybrid_score += 0.10
            if chunk.get("judul_dokumen", "").lower() in query_lower:
                hybrid_score += 0.08

            final_score = round(min(hybrid_score, 1.0), 4)

            if final_score >= min_score:
                item = dict(chunk)
                item["score"] = final_score
                results.append(item)

        results.sort(key=lambda x: x["score"], reverse=True)
        return results[:top_k]

    def get_stats(self) -> Dict[str, Any]:
        """Return Vector Store statistics."""
        layer1_count = sum(1 for c in self.chunks if "1" in str(c.get("lapisan", "")))
        layer2_count = sum(1 for c in self.chunks if "2" in str(c.get("lapisan", "")))
        unique_docs = len(set(c.get("judul_dokumen", "") for c in self.chunks))

        return {
            "total_chunks": len(self.chunks),
            "unique_documents": unique_docs,
            "vocab_size": len(self.vocab),
            "layer1_chunks": layer1_count,
            "layer2_chunks": layer2_count,
            "is_indexed": self.is_indexed
        }
