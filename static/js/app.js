/**
 * MedikaRAG - Single Page Application Router & State Manager
 * Portal Edukasi Kesehatan & AI RAG Ilmiah Kemenkes RI
 */

// Application State
const state = {
  activePath: 'konsultasi-ai-gejala-ringan',
  sessionId: 'session_' + Math.random().toString(36).substring(2, 9),
  diseases: [],
  journals: [],
  chatHistory: [],
  floatingChatHistory: [],
  clarification: {
    age: '',
    duration: '',
    isPregnant: false,
    hasFever: false
  }
};

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
  initNavigation();
  initDiseases();
  initJournals();
  initChatbot();
  initFloatingChat();
  initCuratorPanel();
  initModalBackdropClicks();
  checkUrlHash();
});

// ==============================================================================
// 1. NAVIGATION & ROUTING
// ==============================================================================
function initNavigation() {
  const navLinks = document.querySelectorAll('[data-path]');
  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const targetPath = link.getAttribute('data-path');
      navigateTo(targetPath);
      // Close mobile menu if open
      const mobileMenu = document.getElementById('mobile-menu');
      if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        const icon = document.querySelector('#mobile-menu-btn .material-symbols-outlined');
        if (icon) icon.textContent = 'menu';
      }
    });
  });

  // Mobile menu button toggle
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      const isHidden = mobileMenu.classList.toggle('hidden');
      const icon = mobileMenuBtn.querySelector('.material-symbols-outlined');
      if (icon) {
        icon.textContent = isHidden ? 'menu' : 'close';
      }
    });
  }

  window.addEventListener('hashchange', checkUrlHash);
}

function checkUrlHash() {
  const hash = window.location.hash.replace('#/', '').replace('#', '');
  if (hash) {
    navigateTo(hash, false);
  } else {
    navigateTo('beranda-direktori-penyakit', false);
  }
}

function navigateTo(path, updateHash = true) {
  // Normalize path variations
  let normalizedPath = path.replace(/&/g, '').replace(/--/g, '-').trim();
  if (normalizedPath.includes('beranda') || normalizedPath.includes('direktori')) normalizedPath = 'beranda-direktori-penyakit';
  if (normalizedPath.includes('konsultasi') || normalizedPath.includes('chat') || normalizedPath.includes('tanya')) normalizedPath = 'konsultasi-ai-gejala-ringan';
  if (normalizedPath.includes('jurnal')) normalizedPath = 'modul-jurnal-ilmiah';
  if (normalizedPath.includes('p3k')) normalizedPath = 'panduan-p3k-gawat-darurat';
  if (normalizedPath.includes('kurator') || normalizedPath.includes('panel')) normalizedPath = 'panel-verifikasi-kurator';

  state.activePath = normalizedPath;
  if (updateHash) {
    window.location.hash = `#/${normalizedPath}`;
  }

  // Hide all view sections
  const viewSections = document.querySelectorAll('.app-view');
  viewSections.forEach(sec => sec.classList.add('hidden'));

  // Show active view section
  const activeView = document.getElementById(`view-${normalizedPath}`);
  if (activeView) {
    activeView.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Auto focus input if navigating to chat
  if (normalizedPath === 'konsultasi-ai-gejala-ringan') {
    setTimeout(() => {
      document.getElementById('chat-input')?.focus();
    }, 150);
  }

  // Update navigation link highlights (desktop & mobile)
  const navLinks = document.querySelectorAll('nav [data-path], #mobile-menu [data-path]');
  navLinks.forEach(link => {
    if (link.tagName === 'BUTTON') return;
    const linkPath = link.getAttribute('data-path') || '';
    let normLink = linkPath.replace(/&/g, '').replace(/--/g, '-').trim();
    if (normLink.includes('beranda') || normLink.includes('direktori')) normLink = 'beranda-direktori-penyakit';
    if (normLink.includes('konsultasi') || normLink.includes('chat') || normLink.includes('tanya')) normLink = 'konsultasi-ai-gejala-ringan';
    if (normLink.includes('jurnal')) normLink = 'modul-jurnal-ilmiah';
    if (normLink.includes('p3k')) normLink = 'panduan-p3k-gawat-darurat';
    if (normLink.includes('kurator') || normLink.includes('panel')) normLink = 'panel-verifikasi-kurator';

    const isMobile = link.closest('#mobile-menu') !== null;
    const isActive = normLink === normalizedPath;

    if (isMobile) {
      if (isActive) {
        link.className = 'block px-3 py-2 rounded-lg text-sm font-bold bg-teal-50 text-teal-800 border-l-4 border-teal-700 transition-colors';
      } else {
        link.className = 'block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors';
      }
    } else {
      if (isActive) {
        link.className = 'px-3 py-1.5 rounded-lg text-sm font-semibold bg-white text-teal-800 shadow-xs border border-slate-200/60 transition-all inline-flex items-center gap-1.5';
      } else {
        link.className = 'px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all inline-flex items-center gap-1.5';
      }
    }
  });

  // If entering curator panel, refresh stats
  if (normalizedPath === 'panel-verifikasi-kurator') {
    refreshCuratorStats();
    loadCuratorDocuments();
  }
}

function initModalBackdropClicks() {
  ['disease-modal', 'citation-modal', 'journal-modal'].forEach(id => {
    const modal = document.getElementById(id);
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('hidden');
        }
      });
    }
  });

  // ESC key to close open modals
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeDiseaseModal();
      closeCitationModal();
      closeJournalModal();
    }
  });
}

// ==============================================================================
// 2. DIREKTORI PENYAKIT & PANDUAN KLINIS (FR-1, FR-2, FR-3, FR-4)
// ==============================================================================
async function initDiseases() {
  try {
    const res = await fetch('/api/diseases');
    if (res.ok) {
      state.diseases = await res.json();
      renderDiseaseCards(state.diseases);
    }
  } catch (err) {
    console.error('Error fetching diseases:', err);
  }

  // Search input listener
  const searchInput = document.getElementById('direktori-search');
  if (searchInput) {
    searchInput.addEventListener('input', () => filterDiseases());
  }

  // Category filter chips
  const chipsContainer = document.getElementById('category-filter-chips');
  if (chipsContainer) {
    const chips = chipsContainer.querySelectorAll('.filter-chip');
    chips.forEach(chip => {
      chip.addEventListener('click', () => {
        chips.forEach(c => {
          c.classList.remove('active-chip', 'bg-teal-700', 'text-white', 'shadow-xs');
          c.classList.add('bg-slate-100', 'text-slate-700');
        });
        chip.classList.remove('bg-slate-100', 'text-slate-700');
        chip.classList.add('active-chip', 'bg-teal-700', 'text-white', 'shadow-xs');
        filterDiseases();
      });
    });
  }
}

function filterDiseases() {
  const searchVal = (document.getElementById('direktori-search')?.value || '').toLowerCase().trim();
  const activeChip = document.querySelector('#category-filter-chips .active-chip');
  const catVal = activeChip ? activeChip.getAttribute('data-cat') : 'all';

  const filtered = state.diseases.filter(d => {
    const matchesCat = (catVal === 'all') || (d.category === catVal);
    const textCorpus = `${d.title} ${d.summary} ${d.symptoms} ${d.badge_protocol}`.toLowerCase();
    const matchesSearch = (!searchVal) || textCorpus.includes(searchVal);
    return matchesCat && matchesSearch;
  });

  renderDiseaseCards(filtered);
}

function renderDiseaseCards(diseases) {
  const grid = document.getElementById('direktori-grid');
  if (!grid) return;

  if (diseases.length === 0) {
    grid.innerHTML = `
      <div class="col-span-full p-8 text-center rounded-2xl bg-white border border-dashed border-slate-300">
        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">search_off</span>
        <h4 class="text-base font-bold text-slate-900">Tidak ada topik yang cocok</h4>
        <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci gejala atau pilih kategori topik lain.</p>
      </div>
    `;
    return;
  }

  grid.innerHTML = diseases.map(d => {
    const catBadge = d.category === 'p3k' 
      ? '<span class="px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-800 border border-sky-200 text-xs font-semibold">Cedera Fisik & P3K</span>'
      : d.category === 'kronis'
      ? '<span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-800 border border-indigo-200 text-xs font-semibold">Edukasi Kronis</span>'
      : d.category === 'gizi'
      ? '<span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">Gizi & Nutrisi</span>'
      : '<span class="px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-semibold">Penyakit Ringan</span>';

    const imageHtml = d.image_url 
      ? `<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="${d.image_url}" alt="${d.title}"/>`
      : `<div class="w-full h-full bg-slate-100 flex flex-col items-center justify-center p-4 text-center">
          <span class="material-symbols-outlined text-3xl text-teal-700 mb-1">health_and_safety</span>
          <span class="text-xs font-bold text-slate-900">${d.title}</span>
          <span class="text-[10px] text-slate-500">${d.source_label}</span>
         </div>`;

    return `
      <article class="direktori-card group rounded-2xl bg-white p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between" data-category="${d.category}">
        <div class="space-y-3">
          <div class="flex items-center justify-between">
            ${catBadge}
            <span class="inline-flex items-center gap-1 text-[11px] text-teal-700 font-medium">
              <span class="material-symbols-outlined text-xs">verified</span> ${d.source_label}
            </span>
          </div>
          <div class="h-44 w-full rounded-xl overflow-hidden relative">
            ${imageHtml}
            <div class="absolute bottom-2 left-2 bg-slate-900/80 text-white backdrop-blur-xs px-2.5 py-0.5 rounded text-[11px] font-medium">
              ${d.badge_protocol}
            </div>
          </div>
          <div class="space-y-1">
            <h3 class="text-base font-bold text-slate-900 group-hover:text-teal-700 transition-colors">
              ${d.title}
            </h3>
            <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
              ${d.summary}
            </p>
          </div>
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1 text-xs">
            <div class="flex items-center gap-1.5 text-slate-800 font-medium truncate">
              <span class="material-symbols-outlined text-sm text-teal-700">check_circle</span>
              <span class="truncate">${d.symptoms.substring(0, 65)}...</span>
            </div>
            <div class="flex items-center gap-1.5 text-red-700 font-medium truncate">
              <span class="material-symbols-outlined text-sm">warning</span>
              <span class="truncate">${d.red_flags.substring(0, 65)}...</span>
            </div>
          </div>
        </div>
        <div class="pt-4 mt-3 flex items-center gap-2 border-t border-slate-100">
          <button class="flex-1 py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors text-center cursor-pointer" 
                  onclick="openDiseaseDetail('${d.slug}')" type="button">
            Baca Panduan
          </button>
          <button class="py-2 px-3.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-2xs cursor-pointer"
                  onclick="askAIAboutTopic('${d.title}')" type="button">
            <span class="material-symbols-outlined text-sm">chat</span>
            <span>Tanya AI</span>
          </button>
        </div>
      </article>
    `;
  }).join('');
}

// Open Disease Detail Modal (FR-2, FR-3, FR-4)
async function openDiseaseDetail(slug) {
  const d = state.diseases.find(item => item.slug === slug);
  if (!d) return;

  const modal = document.getElementById('disease-modal');
  const modalContent = document.getElementById('disease-modal-content');
  if (!modal || !modalContent) return;

  modalContent.innerHTML = `
    <div class="flex flex-col max-h-[88vh]">
      <!-- Header (Fixed at top) -->
      <div class="p-5 sm:p-6 border-b border-slate-200 bg-white flex items-start justify-between gap-4 shrink-0">
        <div>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-teal-50 text-teal-800 border border-teal-200 text-xs font-semibold mb-2">
            <span class="material-symbols-outlined text-xs">verified</span> ${d.source_label}
          </div>
          <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug">${d.title}</h2>
          <span class="text-xs text-slate-500">Terakhir Ditinjau: ${d.last_reviewed} • Protokol: ${d.badge_protocol}</span>
        </div>
        <button class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors shrink-0 cursor-pointer" onclick="closeDiseaseModal()" type="button" title="Tutup modal">
          <span class="material-symbols-outlined text-base">close</span>
        </button>
      </div>

      <!-- Body (Independently scrollable) -->
      <div class="flex-1 overflow-y-auto p-5 sm:p-6 pb-12 space-y-5 custom-scrollbar bg-white">
        <!-- 1. Definisi & Gambaran Klinis -->
        <div class="space-y-1.5">
          <h4 class="text-sm font-bold text-teal-800 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">menu_book</span> 1. Definisi & Gambaran Klinis
          </h4>
          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">${d.definition}</p>
        </div>

        <!-- 2. Gejala & Penyebab (2 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
            <h5 class="text-xs font-bold text-slate-900 flex items-center gap-1">
              <span class="material-symbols-outlined text-sm text-teal-700">symptoms</span> Gejala Klinis
            </h5>
            <p class="text-xs text-slate-600 leading-relaxed">${d.symptoms}</p>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
            <h5 class="text-xs font-bold text-slate-900 flex items-center gap-1">
              <span class="material-symbols-outlined text-sm text-teal-700">psychology</span> Penyebab Utama
            </h5>
            <p class="text-xs text-slate-600 leading-relaxed">${d.causes}</p>
          </div>
        </div>

        <!-- 3. Penanganan Mandiri di Rumah (Step by Step) -->
        <div class="p-4 sm:p-5 rounded-xl bg-teal-50/60 border border-teal-200/80 space-y-2">
          <h4 class="text-sm font-bold text-teal-900 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base text-teal-700">healing</span> 2. Langkah Penanganan Mandiri di Rumah
          </h4>
          <div class="text-xs sm:text-sm text-slate-800 space-y-1.5 whitespace-pre-line leading-relaxed">
            ${d.self_care_steps}
          </div>
        </div>

        <!-- 4. Tanda Bahaya & Rujukan Faskes -->
        <div class="p-4 sm:p-5 rounded-xl bg-red-50 border border-red-200 text-red-950 space-y-2">
          <h4 class="text-sm font-bold text-red-900 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base text-red-700">crisis_alert</span> 3. Tanda Bahaya (Red Flags) & Kapan Harus ke Dokter / IGD
          </h4>
          <p class="text-xs sm:text-sm font-semibold text-red-900">${d.red_flags}</p>
          <div class="pt-2 border-t border-red-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <span class="text-xs text-red-800">${d.when_to_doctor}</span>
            <a class="px-3 py-1 rounded-lg bg-red-700 hover:bg-red-800 text-white text-xs font-bold inline-flex items-center gap-1 shrink-0 transition-colors shadow-2xs" href="tel:119">
              <span class="material-symbols-outlined text-xs">call</span> SPGDT 119
            </a>
          </div>
        </div>

        <!-- 5. Pencegahan -->
        <div class="space-y-1.5">
          <h4 class="text-sm font-bold text-teal-800 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">shield</span> 4. Pencegahan Jangka Panjang
          </h4>
          <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">${d.prevention}</p>
        </div>

        <!-- 6. Sumber & Referensi Resmi -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1 text-xs text-slate-600">
          <span class="font-bold text-slate-900 block">Dokumen Referensi Resmi & Sumber Bukti:</span>
          <p class="whitespace-pre-line leading-relaxed">${d.references_text}</p>
        </div>

        <!-- Disclaimer -->
        <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 leading-relaxed">
          📌 <em>Disclaimer Medis: Panduan ini bersifat edukatif berbasis dokumen resmi Kementerian Kesehatan RI dan bukan pengganti pemeriksaan medis langsung oleh dokter berwenang.</em>
        </div>
      </div>

      <!-- Footer CTA (Fixed at bottom) -->
      <div class="p-4 sm:px-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3 shrink-0">
        <button class="px-4 py-2.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-semibold text-xs sm:text-sm transition-colors shadow-2xs cursor-pointer" 
                onclick="closeDiseaseModal()" type="button">
          Tutup
        </button>
        <button class="px-4 py-2.5 rounded-lg bg-teal-700 text-white hover:bg-teal-800 font-semibold text-xs sm:text-sm transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
                onclick="closeDiseaseModal(); askAIAboutTopic('${d.title}')" type="button">
          <span class="material-symbols-outlined text-sm">smart_toy</span>
          <span>Tanya Chatbot tentang Topik Ini</span>
        </button>
      </div>
    </div>
  `;

  modal.classList.remove('hidden');
}

function closeDiseaseModal() {
  const modal = document.getElementById('disease-modal');
  if (modal) modal.classList.add('hidden');
}

function askAIAboutTopic(topicTitle) {
  navigateTo('konsultasi-ai-gejala-ringan');
  const chatInput = document.getElementById('chat-input');
  if (chatInput) {
    chatInput.value = `Halo MedikaRAG, saya ingin bertanya panduan pertolongan pertama dan penanganan mandiri untuk ${topicTitle} sesuai standar resmi Kemenkes...`;
    chatInput.focus();
    chatInput.dispatchEvent(new Event('input', { bubbles: true }));
  }
}

// ==============================================================================
// 3. CHATBOT RAG WORKSPACE (FR-10 s/d FR-16)
// ==============================================================================
function initChatbot() {
  const sendBtn = document.getElementById('send-chat-btn');
  const chatInput = document.getElementById('chat-input');
  const clearBtn = document.getElementById('clear-chat-btn');
  const exportBtn = document.getElementById('export-chat-btn');

  if (sendBtn && chatInput) {
    sendBtn.addEventListener('click', () => sendChatMessage());
    chatInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendChatMessage();
      }
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (confirm('Hapus seluruh riwayat percakapan sesi ini?')) {
        state.chatHistory = [];
        renderChatHistory();
      }
    });
  }

  if (exportBtn) {
    exportBtn.addEventListener('click', () => exportChatHistory());
  }

  // Sample prompt chips
  const promptChips = document.querySelectorAll('.sample-prompt-chip');
  promptChips.forEach(chip => {
    chip.addEventListener('click', () => {
      const promptText = chip.getAttribute('data-prompt') || chip.innerText.trim();
      if (chatInput) {
        chatInput.value = promptText;
        sendChatMessage();
      }
    });
  });

  // Render initial welcome message
  if (state.chatHistory.length === 0) {
    state.chatHistory.push({
      role: 'bot',
      content: (
        "Halo! Saya **Asisten MedikaRAG**, asisten konsultasi dan edukasi kesehatan berbasis *Retrieval-Augmented Generation* (RAG) " +
        "yang disarikan langsung dari **500 dataset konsultasi dokter berlisensi** dan pedoman resmi Kementerian Kesehatan RI.\n\n" +
        "Saya dapat membantu memberikan panduan pertolongan pertama (P3K), langkah perawatan mandiri di rumah, serta tanda bahaya (*Red Flags*) " +
        "yang mewajibkan rujukan ke dokter atau fasilitas kesehatan.\n\n" +
        "🩺 *Silakan ceritakan keluhan atau gejala kesehatan yang sedang Anda rasakan.*"
      ),
      citations: [],
      isEmergency: false,
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    });
    renderChatHistory();
  }
}

async function sendChatMessage() {
  const chatInput = document.getElementById('chat-input');
  if (!chatInput) return;
  const query = chatInput.value.trim();
  if (!query) return;

  chatInput.value = '';
  chatInput.style.height = 'auto';

  // Append user message
  state.chatHistory.push({
    role: 'user',
    content: query,
    timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  });
  renderChatHistory();

  // Show typing indicator
  showChatTypingIndicator(true);

  try {
    const payload = {
      query: query,
      session_id: state.sessionId,
      clarification: {
        age: document.getElementById('clarify-age')?.value || '',
        duration: document.getElementById('clarify-duration')?.value || '',
        is_pregnant: document.getElementById('clarify-pregnant')?.checked || false
      }
    };

    const res = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    showChatTypingIndicator(false);

    if (res.ok) {
      state.chatHistory.push({
        role: 'bot',
        content: data.answer,
        citations: data.citations || [],
        isEmergency: data.is_emergency || false,
        emergencyData: data,
        isFallback: data.is_fallback || false,
        isForbidden: data.is_forbidden || false,
        score: data.score || 0.0,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      });
      renderChatHistory();
    } else {
      state.chatHistory.push({
        role: 'bot',
        content: `Terjadi kendala: ${data.error || 'Gagal memproses pertanyaan.'}`,
        citations: [],
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      });
      renderChatHistory();
    }
  } catch (err) {
    showChatTypingIndicator(false);
    state.chatHistory.push({
      role: 'bot',
      content: 'Maaf, terjadi kesalahan koneksi jaringan. Pastikan server lokal aktif.',
      citations: [],
      timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    });
    renderChatHistory();
  }
}

function showChatTypingIndicator(show) {
  const container = document.getElementById('chat-messages');
  if (!container) return;
  const existingIndicator = document.getElementById('typing-indicator');

  if (show && !existingIndicator) {
    const indicator = document.createElement('div');
    indicator.id = 'typing-indicator';
    indicator.className = 'flex items-center gap-3 p-3.5 rounded-2xl bg-white border border-slate-200 max-w-[80%] self-start shadow-2xs my-1 animate-pulse';
    indicator.innerHTML = `
      <div class="w-7 h-7 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 font-bold">
        <span class="material-symbols-outlined text-sm">smart_toy</span>
      </div>
      <div class="flex items-center gap-1.5 py-0.5">
        <span class="w-2 h-2 rounded-full bg-teal-700 animate-bounce"></span>
        <span class="w-2 h-2 rounded-full bg-teal-700 animate-bounce [animation-delay:0.2s]"></span>
        <span class="w-2 h-2 rounded-full bg-teal-700 animate-bounce [animation-delay:0.4s]"></span>
        <span class="text-xs text-slate-600 font-medium ml-2">Mencari 500 dataset dokter & pedoman resmi...</span>
      </div>
    `;
    container.appendChild(indicator);
    container.scrollTop = container.scrollHeight;
  } else if (!show && existingIndicator) {
    existingIndicator.remove();
  }
}

function renderChatHistory() {
  const container = document.getElementById('chat-messages');
  if (!container) return;

  container.innerHTML = state.chatHistory.map((msg, index) => {
    if (msg.role === 'user') {
      return `
        <div class="flex items-end justify-end self-end max-w-[85%] md:max-w-[75%] my-1.5">
          <div class="px-5 py-3.5 sm:px-6 sm:py-4 rounded-2xl rounded-br-xs bg-teal-700 text-white shadow-xs space-y-1.5">
            <p class="text-sm sm:text-base leading-relaxed whitespace-pre-wrap font-normal text-white">${escapeHtml(msg.content)}</p>
            <div class="text-right">
              <span class="text-[11px] text-teal-100/80 font-medium">${msg.timestamp}</span>
            </div>
          </div>
        </div>
      `;
    }

    // Bot Response
    const isEmergency = msg.isEmergency;
    let cardClass = isEmergency 
      ? 'bg-red-50 text-red-950 border-2 border-red-500 shadow-sm' 
      : 'bg-white text-slate-800 shadow-xs border border-slate-200';

    // Format Markdown styling
    let formattedText = formatMarkdown(msg.content);

    // Citations HTML
    let citationsHtml = '';
    if (msg.citations && msg.citations.length > 0) {
      citationsHtml = `
        <div class="pt-3 mt-3 border-t border-slate-100 space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-teal-800 flex items-center gap-1">
              <span class="material-symbols-outlined text-xs">verified</span> Dokumen Rujukan Terverifikasi (${msg.citations.length})
            </span>
            <span class="text-[11px] text-slate-500">Skor: ${Math.round((msg.score || 0.85) * 100)}%</span>
          </div>
          <div class="flex flex-wrap gap-1.5">
            ${msg.citations.map(c => `
              <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md ${c.lapisan && c.lapisan.includes('1') ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-sky-50 text-sky-800 border border-sky-200'} text-xs font-medium hover:bg-slate-100 transition-all cursor-pointer"
                      onclick="showCitationDetail(${c.id}, '${escapeHtml(c.judul)}', '${escapeHtml(c.sumber)}', ${c.tahun}, '${c.lapisan}', '${c.url}', '${escapeHtml(c.excerpt)}')">
                <span class="font-bold">[${c.id}]</span>
                <span class="max-w-[200px] truncate">${c.judul}</span>
                <span class="material-symbols-outlined text-xs">info</span>
              </button>
            `).join('')}
          </div>
        </div>
      `;
    }

    // Emergency Action CTA buttons
    let emergencyActionsHtml = '';
    if (isEmergency && msg.emergencyData) {
      emergencyActionsHtml = `
        <div class="p-3.5 rounded-xl bg-white text-slate-900 mt-3 space-y-2 border border-red-200 shadow-xs">
          <span class="text-xs font-bold text-red-700 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">emergency</span> Panggilan Darurat Sekarang Juga
          </span>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <a href="tel:119" class="p-2.5 rounded-lg bg-red-700 text-white hover:bg-red-800 transition-colors flex items-center justify-center gap-1.5 text-xs font-bold shadow-xs">
              <span class="material-symbols-outlined text-sm">call</span> Ambulans 119 (Bebas Pulsa)
            </a>
            <a href="tel:112" class="p-2.5 rounded-lg bg-slate-100 text-slate-800 hover:bg-slate-200 border border-slate-300 transition-colors flex items-center justify-center gap-1.5 text-xs font-semibold">
              <span class="material-symbols-outlined text-sm">notifications_active</span> Darurat Daerah 112
            </a>
          </div>
        </div>
      `;
    }

    return `
      <div class="flex items-start gap-3 self-start max-w-[95%] md:max-w-[85%] my-2">
        <div class="w-9 h-9 rounded-xl ${isEmergency ? 'bg-red-600 text-white shadow-xs' : 'bg-teal-700 text-white shadow-xs'} flex items-center justify-center shrink-0 mt-0.5 font-bold">
          <span class="material-symbols-outlined text-lg">${isEmergency ? 'emergency' : 'medical_services'}</span>
        </div>
        <div class="p-5 sm:p-6 rounded-2xl rounded-tl-xs ${cardClass} space-y-3.5 flex-1 overflow-hidden">
          <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <div class="flex items-center gap-2">
              <span class="text-xs sm:text-sm font-bold ${isEmergency ? 'text-red-700' : 'text-slate-900'}">
                ${isEmergency ? 'Triase Gawat Darurat (Red Flag)' : 'Dokter (Berdasarkan Dataset Konsultasi Kemenkes)'}
              </span>
              ${!isEmergency ? '<span class="inline-flex items-center gap-1 text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200 px-2 py-0.5 rounded-full"><span class="material-symbols-outlined text-xs text-teal-700">verified</span>Terverifikasi Dataset</span>' : ''}
            </div>
            <span class="text-[11px] text-slate-400 font-medium">${msg.timestamp}</span>
          </div>
          <div class="chat-markdown text-sm text-slate-800 leading-relaxed">
            ${formattedText}
          </div>
          ${emergencyActionsHtml}
          ${citationsHtml}
        </div>
      </div>
    `;
  }).join('');

  container.scrollTop = container.scrollHeight;
}

// Interactive Citation Preview Modal
function showCitationDetail(id, title, publisher, year, layer, url, excerpt) {
  const modal = document.getElementById('citation-modal');
  const modalBody = document.getElementById('citation-modal-body');
  if (!modal || !modalBody) return;

  const isLayer1 = layer.includes('1');
  const badgeClass = isLayer1 ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-sky-50 text-sky-800 border border-sky-200';

  modalBody.innerHTML = `
    <div class="flex flex-col max-h-[85vh]">
      <!-- Header -->
      <div class="p-5 border-b border-slate-200 bg-white flex items-start justify-between gap-3 shrink-0">
        <div class="space-y-1">
          <span class="inline-block px-2.5 py-0.5 rounded-full ${badgeClass} text-xs font-semibold">
            Sitasi [${id}] • ${layer}
          </span>
          <h3 class="text-base font-bold text-slate-900 leading-snug">${title}</h3>
          <p class="text-xs text-slate-500">Penerbit: ${publisher} • Tahun: ${year}</p>
        </div>
        <button class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors shrink-0 cursor-pointer" onclick="closeCitationModal()">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>

      <!-- Body -->
      <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar bg-white">
        <div class="space-y-1.5">
          <span class="text-xs font-bold text-slate-800">Potongan Bukti Teks Asli (Vector Chunk Excerpt):</span>
          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-700 leading-relaxed italic">
            "${excerpt}"
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2 shrink-0">
        <span class="text-xs text-teal-800 font-medium flex items-center gap-1">
          <span class="material-symbols-outlined text-sm">verified</span> Terverifikasi Kurator Medis
        </span>
        <div class="flex items-center gap-2">
          <button class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors cursor-pointer" onclick="closeCitationModal()" type="button">
            Tutup
          </button>
          ${url && url !== '#' ? `
            <a href="${url}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-1 shadow-xs transition-colors">
              <span>Buka Dokumen</span>
              <span class="material-symbols-outlined text-xs">north_east</span>
            </a>
          ` : ''}
        </div>
      </div>
    </div>
  `;

  modal.classList.remove('hidden');
}

function closeCitationModal() {
  const modal = document.getElementById('citation-modal');
  if (modal) modal.classList.add('hidden');
}

function exportChatHistory() {
  if (state.chatHistory.length === 0) {
    alert('Belum ada riwayat percakapan untuk diunduh.');
    return;
  }

  let text = `========================================================\n`;
  text += `MEDIKARAG - RINGKASAN KONSULTASI EDUKASI KESEHATAN\n`;
  text += `Tanggal: ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID')}\n`;
  text += `Basis Rujukan: 500 Dataset Konsultasi Dokter & Kemenkes RI\n`;
  text += `Disclaimer: Informasi ini bukan diagnosis pengganti dokter.\n`;
  text += `========================================================\n\n`;

  state.chatHistory.forEach((msg, idx) => {
    const role = msg.role === 'user' ? 'PASIEN' : 'AI MEDIKARAG';
    text += `[${msg.timestamp}] ${role}:\n${msg.content}\n\n`;
    if (msg.citations && msg.citations.length > 0) {
      text += `Sitasi Rujukan:\n`;
      msg.citations.forEach(c => {
        text += `  - [${c.id}] ${c.judul} (${c.tahun}) - ${c.sumber}\n`;
      });
      text += `\n`;
    }
    text += `--------------------------------------------------------\n\n`;
  });

  const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = `MedikaRAG_Konsultasi_${Date.now()}.txt`;
  a.click();
}

// ==============================================================================
// 4. FLOATING PERSISTENT CHATBOT WIDGET (FR-10)
// ==============================================================================
function initFloatingChat() {
  const toggleBtn = document.getElementById('floating-chat-toggle');
  const chatDrawer = document.getElementById('floating-chat-drawer');
  const closeBtn = document.getElementById('close-floating-chat');
  const sendBtn = document.getElementById('send-floating-btn');
  const inputEl = document.getElementById('floating-chat-input');

  if (toggleBtn && chatDrawer) {
    toggleBtn.addEventListener('click', () => {
      chatDrawer.classList.toggle('hidden');
      if (!chatDrawer.classList.contains('hidden') && inputEl) {
        inputEl.focus();
      }
    });
  }

  if (closeBtn && chatDrawer) {
    closeBtn.addEventListener('click', () => {
      chatDrawer.classList.add('hidden');
    });
  }

  if (sendBtn && inputEl) {
    sendBtn.addEventListener('click', () => sendFloatingMessage());
    inputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        sendFloatingMessage();
      }
    });
  }
}

async function sendFloatingMessage() {
  const inputEl = document.getElementById('floating-chat-input');
  const container = document.getElementById('floating-chat-messages');
  if (!inputEl || !container) return;

  const query = inputEl.value.trim();
  if (!query) return;

  inputEl.value = '';

  // Append user message
  const userDiv = document.createElement('div');
  userDiv.className = 'px-3 py-2 rounded-xl rounded-br-xs bg-teal-700 text-white text-xs max-w-[85%] self-end leading-relaxed shadow-2xs';
  userDiv.innerText = query;
  container.appendChild(userDiv);
  container.scrollTop = container.scrollHeight;

  // Typing indicator
  const typingDiv = document.createElement('div');
  typingDiv.className = 'text-[11px] text-slate-500 animate-pulse p-1';
  typingDiv.innerText = 'MedikaRAG sedang memproses...';
  container.appendChild(typingDiv);
  container.scrollTop = container.scrollHeight;

  try {
    const res = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ query: query, session_id: state.sessionId })
    });
    const data = await res.json();
    typingDiv.remove();

    const botDiv = document.createElement('div');
    if (data.is_emergency) {
      botDiv.className = 'p-3 rounded-xl bg-red-50 text-red-950 text-xs max-w-[90%] self-start border border-red-300 shadow-2xs space-y-1.5';
      botDiv.innerHTML = `<strong>🚨 Gawat Darurat Medis:</strong><br>${escapeHtml(data.answer)}<br><a href="tel:119" class="inline-block mt-1 font-bold text-red-700 underline">Hubungi 119 Segera</a>`;
    } else {
      botDiv.className = 'p-3 rounded-xl bg-white border border-slate-200 text-slate-800 text-xs max-w-[90%] self-start shadow-2xs leading-relaxed space-y-1.5';
      botDiv.innerHTML = formatMarkdown(data.answer);
    }
    container.appendChild(botDiv);
    container.scrollTop = container.scrollHeight;
  } catch (err) {
    typingDiv.remove();
    const errDiv = document.createElement('div');
    errDiv.className = 'p-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 text-xs';
    errDiv.innerText = 'Gagal terhubung ke server.';
    container.appendChild(errDiv);
  }
}

// ==============================================================================
// 5. MODUL JURNAL ILMIAH (FR-5 s/d FR-9)
// ==============================================================================
async function initJournals() {
  try {
    const res = await fetch('/api/journals');
    if (res.ok) {
      state.journals = await res.json();
      renderJournalCards(state.journals);
    }
  } catch (err) {
    console.error('Error fetching journals:', err);
  }

  // Filter Index chips
  const indexChips = document.querySelectorAll('.journal-index-chip');
  indexChips.forEach(chip => {
    chip.addEventListener('click', () => {
      indexChips.forEach(c => {
        c.classList.remove('active-journal-chip', 'bg-sky-700', 'text-white');
        c.classList.add('bg-slate-100', 'text-slate-700');
      });
      chip.classList.remove('bg-slate-100', 'text-slate-700');
      chip.classList.add('active-journal-chip', 'bg-sky-700', 'text-white');
      filterJournals();
    });
  });

  // Search input
  const jSearch = document.getElementById('journal-search');
  if (jSearch) {
    jSearch.addEventListener('input', () => filterJournals());
  }

  // Year filter
  const jYear = document.getElementById('journal-year-select');
  if (jYear) {
    jYear.addEventListener('change', () => filterJournals());
  }
}

function filterJournals() {
  const searchVal = (document.getElementById('journal-search')?.value || '').toLowerCase().trim();
  const activeChip = document.querySelector('.active-journal-chip');
  const indexVal = activeChip ? activeChip.getAttribute('data-index') : 'all';
  const yearVal = document.getElementById('journal-year-select')?.value || 'all';

  const filtered = state.journals.filter(j => {
    const matchesIndex = (indexVal === 'all') || j.index_rank.toLowerCase().includes(indexVal.toLowerCase());
    const matchesYear = (yearVal === 'all') || (j.year.toString() === yearVal);
    const textCorpus = `${j.title} ${j.authors} ${j.journal_name} ${j.topic}`.toLowerCase();
    const matchesSearch = (!searchVal) || textCorpus.includes(searchVal);
    return matchesIndex && matchesYear && matchesSearch;
  });

  renderJournalCards(filtered);
}

function renderJournalCards(journals) {
  const container = document.getElementById('journal-cards-container');
  if (!container) return;

  if (journals.length === 0) {
    container.innerHTML = `
      <div class="p-8 text-center rounded-2xl bg-white border border-dashed border-slate-300">
        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">article</span>
        <h4 class="text-base font-bold text-slate-900">Tidak ada jurnal yang sesuai filter</h4>
        <p class="text-xs text-slate-500 mt-1">Coba sesuaikan pilihan indeks atau tahun terbit.</p>
      </div>
    `;
    return;
  }

  container.innerHTML = journals.map(j => {
    const isScopus = j.index_rank.toLowerCase().includes('scopus');
    const badgeClass = isScopus ? 'bg-sky-50 text-sky-800 border border-sky-200' : 'bg-teal-50 text-teal-800 border border-teal-200';

    return `
      <div class="rounded-2xl bg-white p-5 shadow-xs hover:shadow-md transition-all space-y-3 border border-slate-200 flex flex-col justify-between">
        <div class="space-y-2">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-1.5">
              <span class="px-2.5 py-0.5 rounded-full ${badgeClass} text-xs font-bold uppercase tracking-wider">
                ${j.index_rank}
              </span>
              <span class="text-xs text-teal-800 font-medium bg-teal-50 border border-teal-100 px-2 py-0.5 rounded">
                ${j.topic}
              </span>
            </div>
            <span class="text-xs text-slate-400">Terbit: ${j.year} • Lisensi: ${j.license}</span>
          </div>

          <h3 class="text-base font-bold text-slate-900 leading-snug">
            ${j.title}
          </h3>

          <p class="text-xs text-teal-700 font-medium">
            Penulis: ${j.authors}
          </p>

          <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
            ${j.abstract}
          </p>
        </div>

        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
          <div class="flex items-center gap-3 text-slate-500">
            <span class="text-teal-700 font-medium flex items-center gap-1">
              <span class="material-symbols-outlined text-xs">sync_alt</span> ${j.chunks_count} Chunk Terindeks RAG
            </span>
            <span>Verifikasi: ${j.verified_date}</span>
          </div>

          <div class="flex items-center gap-2">
            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors cursor-pointer"
                    onclick="openJournalModal('${escapeHtml(j.title)}', '${escapeHtml(j.authors)}', '${j.journal_name}', '${j.index_rank}', '${j.doi_url}', '${escapeHtml(j.abstract)}', ${j.chunks_count}, '${j.license}')" type="button">
              Baca Abstrak Lengkap
            </button>
            <a href="${j.doi_url}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 rounded-lg bg-teal-50 text-teal-800 border border-teal-200 hover:bg-teal-100 text-xs font-semibold transition-colors flex items-center gap-1">
              <span>Buka DOI Jurnal</span>
              <span class="material-symbols-outlined text-xs">north_east</span>
            </a>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

function openJournalModal(title, authors, journal, indexRank, doi, abstract, chunks, license) {
  const modal = document.getElementById('journal-modal');
  const modalBody = document.getElementById('journal-modal-body');
  if (!modal || !modalBody) return;

  modalBody.innerHTML = `
    <div class="flex flex-col max-h-[85vh]">
      <!-- Header -->
      <div class="p-5 border-b border-slate-200 bg-white flex items-start justify-between gap-3 shrink-0">
        <div class="space-y-1">
          <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-800 border border-sky-200 text-xs font-bold">
            ${indexRank} • ${journal}
          </span>
          <h3 class="text-base font-bold text-slate-900 leading-snug">${title}</h3>
          <p class="text-xs text-teal-700 font-medium">Penulis: ${authors}</p>
        </div>
        <button class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors shrink-0 cursor-pointer" onclick="closeJournalModal()">
          <span class="material-symbols-outlined text-sm">close</span>
        </button>
      </div>

      <!-- Body -->
      <div class="flex-1 overflow-y-auto p-5 space-y-4 custom-scrollbar bg-white">
        <div class="space-y-1.5">
          <h4 class="text-xs font-bold text-slate-800">Abstrak Ilmiah Terdaftar:</h4>
          <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-700 leading-relaxed">
            ${abstract}
          </div>
        </div>

        <div class="p-3 rounded-xl bg-teal-50/60 border border-teal-200 flex items-center justify-between text-xs text-slate-600">
          <span class="text-teal-800 font-medium flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">dataset</span>
            ${chunks} Potongan Vektor (Chunks) Aktif dalam RAG Layer 2
          </span>
          <span>Lisensi: ${license} (Open Access)</span>
        </div>
      </div>

      <!-- Footer -->
      <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2 shrink-0">
        <button class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition-colors cursor-pointer" onclick="closeJournalModal()" type="button">
          Tutup
        </button>
        <a href="${doi}" target="_blank" rel="noopener noreferrer" class="px-3.5 py-1.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold flex items-center gap-1 shadow-xs transition-colors">
          <span>Kunjungi Penerbit Asli</span>
          <span class="material-symbols-outlined text-xs">north_east</span>
        </a>
      </div>
    </div>
  `;

  modal.classList.remove('hidden');
}

function closeJournalModal() {
  const modal = document.getElementById('journal-modal');
  if (modal) modal.classList.add('hidden');
}

// ==============================================================================
// 6. PANEL VERIFIKASI KURATOR & ADMIN (FR-17 s/d FR-20, PRD Bagian 11)
// ==============================================================================
function initCuratorPanel() {
  // Curator subtabs
  const tabs = document.querySelectorAll('.curator-tab-btn');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => {
        t.classList.remove('active-tab', 'border-teal-700', 'text-teal-700');
        t.classList.add('border-transparent', 'text-slate-600');
      });
      tab.classList.remove('border-transparent', 'text-slate-600');
      tab.classList.add('active-tab', 'border-teal-700', 'text-teal-700');

      const targetView = tab.getAttribute('data-tab');
      document.querySelectorAll('.curator-tab-view').forEach(v => v.classList.add('hidden'));
      const activeView = document.getElementById(`tab-${targetView}`);
      if (activeView) activeView.classList.remove('hidden');

      if (targetView === 'emergency') loadEmergencyRules();
      if (targetView === 'logs') loadAuditLogs();
    });
  });

  // Reindex button
  const reindexBtn = document.getElementById('reindex-btn');
  if (reindexBtn) {
    reindexBtn.addEventListener('click', async () => {
      reindexBtn.disabled = true;
      reindexBtn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Sinkronisasi...';

      try {
        const res = await fetch('/api/reindex', { method: 'POST' });
        const data = await res.json();
        alert(`Berhasil! ${data.message} (${data.stats.total_chunks} chunk diindeks dalam ${data.stats.duration_ms} ms)`);
        refreshCuratorStats();
      } catch (err) {
        alert('Gagal melakukan sinkronisasi vector store.');
      } finally {
        reindexBtn.disabled = false;
        reindexBtn.innerHTML = '<span class="material-symbols-outlined text-sm">sync</span> Re-index Vector Database';
      }
    });
  }

  // Add document form submit
  const addDocForm = document.getElementById('add-document-form');
  if (addDocForm) {
    addDocForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const payload = {
        title: document.getElementById('doc-title').value.trim(),
        publisher: document.getElementById('doc-publisher').value.trim(),
        year: parseInt(document.getElementById('doc-year').value || '2024'),
        topic: document.getElementById('doc-topic').value,
        layer: document.getElementById('doc-layer').value,
        license: document.getElementById('doc-license').value,
        url: document.getElementById('doc-url').value.trim() || '#',
        content: document.getElementById('doc-content').value.trim()
      };

      try {
        const res = await fetch('/api/documents', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        if (res.ok) {
          alert('Dokumen sumber baru berhasil ditambahkan dan di-chunk ke dalam RAG!');
          addDocForm.reset();
          loadCuratorDocuments();
          refreshCuratorStats();
        } else {
          const err = await res.json();
          alert(`Gagal: ${err.error || 'Terjadi kesalahan'}`);
        }
      } catch (err) {
        alert('Gagal menghubungi server.');
      }
    });
  }

  // Add emergency rule form
  const addRuleForm = document.getElementById('add-rule-form');
  if (addRuleForm) {
    addRuleForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const kw = document.getElementById('rule-keyword').value.trim();
      const act = document.getElementById('rule-action').value.trim();

      try {
        const res = await fetch('/api/emergency-rules', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ keyword: kw, severity: 'CRITICAL', action: act })
        });
        if (res.ok) {
          alert(`Rule '${kw}' berhasil disimpan tanpa perlu restart sistem!`);
          addRuleForm.reset();
          loadEmergencyRules();
        }
      } catch (err) {
        alert('Gagal menyimpan rule darurat.');
      }
    });
  }

  // Run benchmark evaluator button (PRD Bagian 11)
  const runEvalBtn = document.getElementById('run-eval-btn');
  if (runEvalBtn) {
    runEvalBtn.addEventListener('click', async () => {
      runEvalBtn.disabled = true;
      runEvalBtn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Menjalankan 90 Pertanyaan Evaluasi...';

      try {
        const res = await fetch('/api/evaluate', { method: 'POST' });
        const report = await res.json();
        renderEvaluationReport(report);
      } catch (err) {
        alert('Gagal menjalankan evaluasi.');
      } finally {
        runEvalBtn.disabled = false;
        runEvalBtn.innerHTML = '<span class="material-symbols-outlined text-sm">play_arrow</span> Jalankan Benchmark 90 Soal';
      }
    });
  }
}

async function refreshCuratorStats() {
  try {
    const res = await fetch('/api/stats');
    if (res.ok) {
      const stats = await res.json();
      document.getElementById('stat-docs').innerText = stats.documents;
      document.getElementById('stat-chunks').innerText = stats.chunks;
      document.getElementById('stat-journals').innerText = stats.journals;
      document.getElementById('stat-queries').innerText = stats.total_queries;
      document.getElementById('stat-emergency').innerText = stats.emergency_triggers;
    }
  } catch (err) {
    console.error('Error fetching curator stats:', err);
  }
}

async function loadCuratorDocuments() {
  const container = document.getElementById('curator-documents-list');
  if (!container) return;

  try {
    const res = await fetch('/api/documents');
    if (res.ok) {
      const docs = await res.json();
      container.innerHTML = docs.map((doc, idx) => `
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="space-y-0.5">
            <div class="flex items-center gap-1.5">
              <span class="px-2 py-0.5 rounded text-xs font-bold ${doc.layer.includes('1') ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-sky-50 text-sky-800 border border-sky-200'}">
                ${doc.layer}
              </span>
              <span class="text-xs text-slate-400">${doc.verified_date} • ${doc.license}</span>
            </div>
            <h5 class="text-sm text-slate-900 font-semibold">${doc.title}</h5>
            <p class="text-xs text-slate-600">Penerbit: ${doc.publisher} (${doc.year}) • Topik: ${doc.topic}</p>
          </div>
          <span class="inline-flex items-center gap-1 text-teal-800 text-xs font-bold bg-white px-2.5 py-1 rounded-full border border-teal-200 self-start sm:self-center shadow-2xs">
            <span class="material-symbols-outlined text-xs">verified</span> ${doc.status}
          </span>
        </div>
      `).join('');
    }
  } catch (err) {
    container.innerHTML = '<p class="text-red-700 text-xs">Gagal memuat dokumen sumber.</p>';
  }
}

async function loadEmergencyRules() {
  const container = document.getElementById('emergency-rules-list');
  if (!container) return;

  try {
    const res = await fetch('/api/emergency-rules');
    if (res.ok) {
      const rules = await res.json();
      container.innerHTML = rules.map(r => `
        <tr class="border-b border-slate-200 text-xs sm:text-sm hover:bg-slate-50">
          <td class="py-2.5 px-3 font-semibold text-red-700">${r.keyword}</td>
          <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold">${r.severity}</span></td>
          <td class="py-2.5 px-3 text-slate-600">${r.action}</td>
        </tr>
      `).join('');
    }
  } catch (err) {
    console.error('Error loading emergency rules:', err);
  }
}

async function loadAuditLogs() {
  const container = document.getElementById('audit-logs-list');
  if (!container) return;

  const filter = document.getElementById('audit-log-filter')?.value || 'all';

  try {
    const res = await fetch(`/api/logs?filter=${filter}`);
    if (res.ok) {
      const logs = await res.json();
      if (logs.length === 0) {
        container.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-slate-400 text-xs">Belum ada rekaman audit log.</td></tr>';
        return;
      }
      container.innerHTML = logs.map(l => {
        const badge = l.is_emergency 
          ? '<span class="px-2 py-0.5 rounded-full bg-red-600 text-white text-xs font-bold">DARURAT</span>'
          : l.is_forbidden
          ? '<span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">TERLARANG</span>'
          : l.is_fallback
          ? '<span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-800 text-xs font-bold">FALLBACK</span>'
          : '<span class="px-2 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-bold">TERJAWAB</span>';

        return `
          <tr class="border-b border-slate-200 text-xs sm:text-sm hover:bg-slate-50">
            <td class="py-2 px-3 text-slate-400">${l.timestamp}</td>
            <td class="py-2 px-3 font-medium text-slate-900 max-w-[240px] truncate">${escapeHtml(l.query)}</td>
            <td class="py-2 px-3">${badge}</td>
            <td class="py-2 px-3">${l.top_score ? Math.round(l.top_score * 100) + '%' : '-'}</td>
            <td class="py-2 px-3 text-slate-600 max-w-[200px] truncate">${escapeHtml(l.response_preview)}</td>
          </tr>
        `;
      }).join('');
    }
  } catch (err) {
    console.error('Error loading audit logs:', err);
  }
}

function renderEvaluationReport(report) {
  const container = document.getElementById('eval-report-container');
  if (!container) return;

  const m = report.metrics;
  container.innerHTML = `
    <div class="space-y-4 pt-3 border-t border-slate-200">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="text-base font-bold text-slate-900">Hasil Evaluasi 90 Soal (PRD Bagian 11)</h4>
          <span class="text-xs text-slate-400">Waktu Eksekusi: ${report.timestamp} • Latensi Rata-rata: ${report.avg_latency_ms} ms</span>
        </div>
        <div class="px-4 py-2 rounded-xl bg-teal-700 text-white text-base font-bold shadow-xs">
          Akurasi Total: ${report.overall_accuracy}%
        </div>
      </div>

      <!-- Metrics Grid -->
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Deteksi Darurat</span>
          <div class="text-base font-bold ${m.emergency_detection_rate === 100 ? 'text-teal-700' : 'text-red-700'}">${m.emergency_detection_rate}%</div>
          <span class="text-[10px] text-slate-400">Target: 100%</span>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Penolakan Dosis</span>
          <div class="text-base font-bold text-teal-700">${m.dosage_refusal_rate}%</div>
          <span class="text-[10px] text-slate-400">Target: ≥90%</span>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Out-of-Data Fallback</span>
          <div class="text-base font-bold text-teal-700">${m.out_of_data_fallback_rate}%</div>
          <span class="text-[10px] text-slate-400">Target: ≥90%</span>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Luar Topik Refusal</span>
          <div class="text-base font-bold text-teal-700">${m.out_of_domain_refusal_rate}%</div>
          <span class="text-[10px] text-slate-400">Target: ≥90%</span>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Jawaban Bersumber</span>
          <div class="text-base font-bold text-teal-700">${m.grounded_answer_rate}%</div>
          <span class="text-[10px] text-slate-400">Target: ≥80%</span>
        </div>

        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-center space-y-0.5">
          <span class="text-xs text-slate-500 block">Pelanggaran Dosis Liar</span>
          <div class="text-base font-bold text-teal-700">${m.zero_dosage_violations} Kasus</div>
          <span class="text-[10px] text-slate-400">Target: 0</span>
        </div>
      </div>
    </div>
  `;
}

// ==============================================================================
// 7. UTILITIES & FORMATTERS
// ==============================================================================
function escapeHtml(text) {
  if (!text) return '';
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function formatMarkdown(text) {
  if (!text) return '';
  let html = text.replace(/\r\n/g, '\n');

  // Headings
    html = html.replace(/\?\?\s*/g, '');
  html = html.replace(/^###\s*(.*?)$/gm, (match, p1) => {
    let cleanTitle = p1.replace(/\?\?\s*/g, '').trim();
    let icon = 'clinical_notes';
    if (cleanTitle.includes('1.') || cleanTitle.includes('Klinis')) icon = 'medical_services';
    else if (cleanTitle.includes('2.') || cleanTitle.includes('Penanganan') || cleanTitle.includes('Perawatan')) icon = 'healing';
    else if (cleanTitle.includes('3.') || cleanTitle.includes('Faskes') || cleanTitle.includes('Dokter')) icon = 'crisis_alert';
    else if (cleanTitle.includes('4.') || cleanTitle.includes('Rujukan') || cleanTitle.includes('Dataset')) icon = 'menu_book';
    return `<h3 class="text-sm sm:text-base font-bold text-teal-900 mt-3 mb-1.5 pb-1 border-b border-slate-100 flex items-center gap-1.5"><span class="material-symbols-outlined text-sm text-teal-700">${icon}</span><span>${cleanTitle}</span></h3>`;
  });
  html = html.replace(/^##\s*(.*?)$/gm, '<h2 class="text-base font-bold text-slate-900 mt-3 mb-1.5">$1</h2>');

  // Bold & Italic
  html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold text-slate-900">$1</strong>');
  html = html.replace(/\*(.*?)\*/g, '<em class="italic text-slate-600">$1</em>');

  // Blockquotes
  html = html.replace(/^>\s*(.*?)$/gm, '<blockquote class="border-l-4 border-amber-500 bg-amber-50 text-amber-900 px-3 py-2 rounded-r-lg my-2 text-xs sm:text-sm">$1</blockquote>');

  // Horizontal rules
  html = html.replace(/^---$/gm, '<hr class="my-3 border-slate-200"/>');

  // Lists: - item
  html = html.replace(/^-\s*(.*?)$/gm, '<li class="text-slate-700 leading-relaxed mb-1">$1</li>');
  html = html.replace(/(<li class=".*?">.*?<\/li>\s*)+/gs, '<ul class="list-disc pl-5 my-2 space-y-1">$&</ul>');

  // Links
  html = html.replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" class="text-teal-700 underline font-medium hover:text-teal-800">$1</a>');

  // Paragraphs / Newlines
  html = html.replace(/\n\n+/g, '</p><p class="mt-2 text-slate-700 leading-relaxed">');
  html = html.replace(/\n/g, '<br/>');

  return `<div class="chat-text-wrapper leading-relaxed space-y-1"><p class="text-slate-700 leading-relaxed">${html}</p></div>`;
}
