<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <meta content="web_standard" name="shell-type"/>
  <title>MedikaRAG - Portal Edukasi Kesehatan & AI RAG Ilmiah Kemenkes RI</title>
  
  <!-- Google Fonts & Material Symbols -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
  
  <!-- Custom Styles -->
  <link rel="stylesheet" href="{{ asset('static/css/style.css') }}"/>

  <!-- Tailwind CSS CDN & Clean Clinical Design System Config -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            // Authoritative Medical Teal (Kemenkes Identity)
            "primary": "#0f766e",
            "primary-container": "#0d9488",
            "on-primary": "#ffffff",
            "on-primary-container": "#f0fdfa",
            "primary-fixed": "#ccfbf1",
            "primary-fixed-dim": "#99f6e4",
            "on-primary-fixed": "#134e4a",
            "on-primary-fixed-variant": "#115e59",

            // Medical Navy / Sapphire (Scientific Jurnal)
            "secondary": "#0284c7",
            "secondary-container": "#0369a1",
            "on-secondary": "#ffffff",
            "on-secondary-container": "#f0f9ff",
            "secondary-fixed": "#e0f2fe",
            "secondary-fixed-dim": "#bae6fd",
            "on-secondary-fixed": "#0c4a6e",
            "on-secondary-fixed-variant": "#075985",

            // Urgent Red (Hospital Emergency & Triase)
            "error": "#dc2626",
            "on-error": "#ffffff",
            "error-container": "#fef2f2",
            "on-error-container": "#991b1b",
            "tertiary": "#b91c1c",
            "tertiary-container": "#991b1b",
            "on-tertiary": "#ffffff",
            "on-tertiary-container": "#fef2f2",
            "tertiary-fixed": "#fee2e2",
            "tertiary-fixed-dim": "#fecaca",
            "on-tertiary-fixed": "#7f1d1d",

            // Crisp Clean Clinical Neutral Slate (No Muddy Purple Tint)
            "background": "#f8fafc",
            "surface": "#f8fafc",
            "surface-bright": "#ffffff",
            "surface-container-lowest": "#ffffff",
            "surface-container-low": "#f8fafc",
            "surface-container": "#f1f5f9",
            "surface-container-high": "#e2e8f0",
            "surface-container-highest": "#cbd5e1",
            "surface-variant": "#e2e8f0",
            "surface-dim": "#e2e8f0",
            "inverse-surface": "#0f172a",
            "inverse-on-surface": "#f8fafc",

            // Accessible High-Contrast Typography
            "on-surface": "#0f172a",
            "on-surface-variant": "#475569",
            "on-background": "#0f172a",
            "outline": "#94a3b8",
            "outline-variant": "#e2e8f0"
          },
          borderRadius: {
            "DEFAULT": "0.375rem",
            "lg": "0.5rem",
            "xl": "0.75rem",
            "2xl": "1rem",
            "full": "9999px"
          },
          spacing: {
            "space-2xs": "0.25rem",
            "space-xs": "0.5rem",
            "space-sm": "0.75rem",
            "space-md": "1rem",
            "space-lg": "1.5rem",
            "space-xl": "2rem",
            "space-2xl": "3rem",
            "space-3xl": "4rem",
            "margin": "2rem",
            "margin-mobile": "1rem"
          },
          fontFamily: {
            "sans": ["Inter", "system-ui", "sans-serif"],
            "title-sm": ["Plus Jakarta Sans", "Inter", "sans-serif"],
            "label-md": ["Inter", "sans-serif"],
            "body-sm": ["Inter", "sans-serif"],
            "code-citation": ["Inter", "ui-monospace", "monospace"],
            "title-md": ["Plus Jakarta Sans", "Inter", "sans-serif"],
            "body-md": ["Inter", "sans-serif"],
            "headline-sm": ["Plus Jakarta Sans", "Inter", "sans-serif"],
            "body-lg": ["Inter", "sans-serif"],
            "label-sm": ["Inter", "sans-serif"],
            "headline-md": ["Plus Jakarta Sans", "Inter", "sans-serif"],
            "display": ["Plus Jakarta Sans", "Inter", "sans-serif"],
            "headline-lg": ["Plus Jakarta Sans", "Inter", "sans-serif"]
          }
        }
      }
    };
  </script>
</head>

<body class="bg-slate-50 font-sans text-slate-900 min-h-screen custom-scrollbar flex flex-col justify-between">

  <!-- ============================================================================== -->
  <!-- 1. FIXED TOP HEADER WITH EMERGENCY RIBBON & NAVIGATION -->
  <!-- ============================================================================== -->
  <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
    <!-- Main Navigation Bar -->
    <div class="h-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
      <!-- Brand Identity (Authentic Emblem & Typography) -->
      <div class="flex items-center gap-3 shrink-0">
        <a class="flex items-center gap-2.5 cursor-pointer group" data-path="beranda-direktori-penyakit" href="#/beranda-direktori-penyakit">
          <div class="w-9 h-9 rounded-xl bg-teal-700 flex items-center justify-center text-white shadow-xs group-hover:bg-teal-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
              <circle cx="12" cy="12" r="9" stroke-width="1.8"></circle>
            </svg>
          </div>
          <div class="flex flex-col">
            <div class="flex items-center gap-1.5">
              <span class="font-bold text-lg text-slate-900 tracking-tight leading-none group-hover:text-teal-800 transition-colors">MedikaRAG</span>
              <span class="text-[10px] font-bold uppercase tracking-wider bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded">Kemenkes</span>
            </div>
            <span class="text-[11px] text-slate-500 font-medium leading-tight mt-0.5">Edukasi & Konsultasi RAG Terpercaya</span>
          </div>
        </a>
      </div>

      <!-- Main Path Navigation Links (Segmented Pill Track) -->
      <nav class="hidden lg:flex items-center gap-1 p-1 bg-slate-100/80 rounded-xl border border-slate-200/60 text-sm font-medium">
        <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all cursor-pointer" 
           data-path="beranda-direktori-penyakit" href="#/beranda-direktori-penyakit">
          Beranda & Direktori
        </a>
        <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all inline-flex items-center gap-1.5 cursor-pointer" 
           data-path="konsultasi-ai-gejala-ringan" href="#/konsultasi-ai-gejala-ringan">
          <span>Konsultasi AI RAG</span>
          <span class="inline-flex items-center px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 uppercase tracking-wide">Live</span>
        </a>
        <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all cursor-pointer" 
           data-path="modul-jurnal-ilmiah" href="#/modul-jurnal-ilmiah">
          Jurnal Ilmiah
        </a>
        <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all cursor-pointer" 
           data-path="panduan-p3k-gawat-darurat" href="#/panduan-p3k-gawat-darurat">
          Panduan P3K
        </a>
        <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white/60 transition-all cursor-pointer" 
           data-path="panel-verifikasi-kurator" href="#/panel-verifikasi-kurator">
          Panel Kurator
        </a>
      </nav>

      <!-- Action Button & Mobile Menu Toggle -->
      <div class="flex items-center gap-2 shrink-0">
        <button class="inline-flex items-center gap-1.5 bg-teal-700 hover:bg-teal-800 active:bg-teal-900 text-white px-3.5 py-1.5 rounded-xl font-medium text-xs sm:text-sm shadow-xs hover:shadow transition-all cursor-pointer" 
                data-path="konsultasi-ai-gejala-ringan" type="button">
          <span class="material-symbols-outlined text-sm">smart_toy</span>
          <span>Buka Chatbot RAG</span>
        </button>

        <!-- Mobile Menu Toggle Button -->
        <button id="mobile-menu-btn" type="button" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors cursor-pointer" aria-label="Menu Navigasi">
          <span class="material-symbols-outlined text-2xl">menu</span>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Navigation -->
    <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200/80 bg-white/98 backdrop-blur-md px-4 py-3 space-y-1 shadow-lg">
      <a class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors" 
         data-path="beranda-direktori-penyakit" href="#/beranda-direktori-penyakit">
        Beranda & Direktori
      </a>
      <a class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors" 
         data-path="konsultasi-ai-gejala-ringan" href="#/konsultasi-ai-gejala-ringan">
        <span>Konsultasi AI RAG</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">Live</span>
      </a>
      <a class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors" 
         data-path="modul-jurnal-ilmiah" href="#/modul-jurnal-ilmiah">
        Jurnal Ilmiah
      </a>
      <a class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors" 
         data-path="panduan-p3k-gawat-darurat" href="#/panduan-p3k-gawat-darurat">
        Panduan P3K
      </a>
      <a class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition-colors" 
         data-path="panel-verifikasi-kurator" href="#/panel-verifikasi-kurator">
        Panel Kurator
      </a>
    </div>
  </header>


  <!-- ============================================================================== -->
  <!-- MAIN CONTAINER (ROUTED VIEWS) -->
  <!-- ============================================================================== -->
  <main class="w-full pt-20 max-w-7xl mx-auto px-4 sm:px-8 min-h-[calc(100vh-220px)] pb-12">

    <!-- ========================================================================== -->
    <!-- VIEW 1: BERANDA & DIREKTORI PENYAKIT -->
    <!-- ========================================================================== -->
    <div id="view-beranda-direktori-penyakit" class="app-view flex flex-col w-full space-y-8">
      
      <!-- HERO SECTION (CLEAN, AUTHORITATIVE, ZERO AI SLOP) -->
      <section class="rounded-2xl bg-white p-6 sm:p-8 lg:p-10 shadow-sm border border-slate-200 bg-medical-grid relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
          
          <!-- Left Column: Editorial & Clinical Search -->
          <div class="lg:col-span-8 flex flex-col space-y-5">
            <!-- Trust Badge Pill -->
            <div class="inline-flex items-center gap-2 self-start px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-semibold">
              <span class="material-symbols-outlined text-sm">verified</span>
              <span>Dokumen Resmi Kementerian Kesehatan RI & Jurnal Biomedis Terakreditasi</span>
            </div>
            
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-900 tracking-tight leading-tight">
              Portal Edukasi Kesehatan & Triase Gejala Ringan Terpercaya
            </h1>
            
            <p class="text-base text-slate-600 max-w-2xl leading-relaxed">
              Informasi klinis terstandar berbasis Pedoman Nasional Pelayanan Kedokteran (PNPK) Kemenkes RI dan jurnal ilmiah internasional terakreditasi. Dilengkapi asisten AI RAG dengan pelacakan sitasi transparan tanpa spekulasi diagnosis.
            </p>
            
            <!-- Prominent Clinical Search Bar -->
            <div class="pt-1 flex flex-col space-y-3 max-w-2xl">
              <div class="relative flex items-center bg-white rounded-xl shadow-sm border border-slate-300 focus-within:border-teal-600 focus-within:ring-2 focus-within:ring-teal-100 transition-all">
                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-lg pointer-events-none">search</span>
                <input class="w-full pl-11 pr-28 py-3 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none" 
                       id="direktori-search" placeholder="Cari gejala, penyakit, atau cedera (misal: batuk pilek, kaki terkilir, demam)..." type="text"/>
                <button class="absolute right-1.5 px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 shadow-sm" 
                        onclick="filterDiseases()" type="button">
                  <span>Cari</span>
                  <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </button>
              </div>
              
              <!-- Quick Filter Category Chips -->
              <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs" id="category-filter-chips">
                <button class="filter-chip active-chip px-3 py-1 rounded-full font-semibold bg-teal-700 text-white shadow-sm whitespace-nowrap transition-all" data-cat="all" type="button">Semua Topik</button>
                <button class="filter-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 whitespace-nowrap transition-all" data-cat="ringan" type="button">Penyakit Ringan</button>
                <button class="filter-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 whitespace-nowrap transition-all" data-cat="p3k" type="button">Cedera Fisik & P3K</button>
                <button class="filter-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 whitespace-nowrap transition-all" data-cat="kronis" type="button">Edukasi Kronis</button>
                <button class="filter-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 whitespace-nowrap transition-all" data-cat="gizi" type="button">Gizi & Nutrisi</button>
              </div>
            </div>

            <!-- Trust Pillars Row -->
            <div class="pt-2 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-600 border-t border-slate-100">
              <span class="inline-flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-teal-700 text-sm">shield</span> Standar PNPK Kemenkes</span>
              <span class="inline-flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-sky-700 text-sm">menu_book</span> SINTA 2 & Scopus</span>
              <span class="inline-flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-teal-700 text-sm">lock</span> Kepatuhan UU PDP No. 27/2022</span>
              <span class="inline-flex items-center gap-1 font-medium"><span class="material-symbols-outlined text-red-700 text-sm">crisis_alert</span> Triase Kedaruratan 119</span>
            </div>
          </div>
          
          <!-- Right Column: Clinical AI Consultation Card -->
          <div class="lg:col-span-4 flex flex-col">
            <div class="rounded-xl bg-slate-50 p-6 border border-slate-200/90 shadow-sm flex flex-col justify-between space-y-4">
              <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-bold">
                  <span class="material-symbols-outlined text-xl">smart_toy</span>
                </div>
                <span class="text-[11px] font-bold uppercase tracking-wider bg-teal-700 text-white px-2 py-0.5 rounded">
                  Asisten RAG Terverifikasi
                </span>
              </div>
              
              <div class="space-y-1.5">
                <h3 class="text-base font-bold text-slate-900">
                  Konsultasi Gejala Ringan & Triase
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                  Dapatkan rekomendasi penanganan awal berbasis dokumen resmi dengan pelacakan sitasi transparan.
                </p>
              </div>
              
              <!-- Checklist of Safeguards -->
              <ul class="space-y-2 text-xs text-slate-700 border-t border-b border-slate-200/70 py-3">
                <li class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-teal-700 text-sm">check_circle</span>
                  <span>100% Mengacu Dokumen Kemenkes RI</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-teal-700 text-sm">check_circle</span>
                  <span>Tolak Resep & Hitung Dosis Obat Keras</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-teal-700 text-sm">check_circle</span>
                  <span>Deteksi Tanda Bahaya (Red Flags) Instan</span>
                </li>
              </ul>
              
              <a class="w-full py-2.5 px-4 bg-teal-700 hover:bg-teal-800 text-white rounded-lg text-xs font-semibold text-center flex items-center justify-center gap-1.5 transition-colors shadow-sm" 
                 data-path="konsultasi-ai-gejala-ringan" href="#/konsultasi-ai-gejala-ringan">
                <span class="material-symbols-outlined text-sm">chat</span>
                <span>Mulai Konsultasi Mandiri</span>
              </a>
            </div>
          </div>

        </div>
      </section>

      <!-- DIREKTORI EDUKASI KESEHATAN (15 TOPIK RESMI KEMENKES) -->
      <section class="flex flex-col space-y-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-2 pb-1 border-b border-slate-200">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Direktori Klinis Terverifikasi</span>
            <h2 class="text-xl font-bold text-slate-900 mt-0.5">Topik Penyakit Ringan & Penanganan Mandiri</h2>
            <p class="text-xs text-slate-500">Pedoman tindakan awal langkah demi langkah bersumber resmi Kemenkes RI.</p>
          </div>
          
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-teal-800 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-full">
              15 Modul Klinis Terindeks
            </span>
          </div>
        </div>
        
        <!-- Dynamic Clinical Condition Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="direktori-grid">
          <!-- Cards populated via app.js -->
        </div>
      </section>

      <!-- LAPISAN BUKTI ILMIAH (RAG KNOWLEDGE BASE) -->
      <section class="rounded-xl bg-white p-6 shadow-sm border border-slate-200 space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-sky-700">
              <span class="material-symbols-outlined text-sm">dataset</span>
              <span>RAG Retrieval Pipeline • Lapisan 2</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-0.5">Basis Pengetahuan Ilmiah Terindeks (SINTA & Scopus)</h2>
            <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
              Setiap rekomendasi sistem divalidasi silang terhadap korpus artikel ilmiah peer-reviewed bereputasi tinggi.
            </p>
          </div>
          
          <a class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors shrink-0" 
             data-path="modul-jurnal-ilmiah" href="#/modul-jurnal-ilmiah">
            <span>Repositori Jurnal Lengkap</span>
            <span class="material-symbols-outlined text-xs">north_east</span>
          </a>
        </div>
        
        <!-- 3 Featured Journal Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Journal 1: Scopus Q1 -->
          <div class="rounded-lg bg-slate-50 p-4 border border-slate-200 flex flex-col justify-between space-y-3">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <span class="bg-sky-100 text-sky-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                  Scopus Q1 • CiteScore 6.8
                </span>
                <span class="text-[11px] text-slate-400 font-medium">2024</span>
              </div>
              <h4 class="text-xs font-bold text-slate-900 leading-snug">
                Evidence-Based Conservative Management of Acute Lateral Ankle Sprains: Systematic Review
              </h4>
              <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                British Journal of Sports Medicine. Mengevaluasi efikasi kompresi dingin dan pembebanan bertahap (P.O.L.I.C.E vs R.I.C.E).
              </p>
            </div>
            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
              <span class="font-medium text-teal-700">DOI: 10.1136/bjsports-2023-107</span>
              <span class="font-semibold text-slate-700">14 Chunk Vektor</span>
            </div>
          </div>
          
          <!-- Journal 2: SINTA 2 -->
          <div class="rounded-lg bg-slate-50 p-4 border border-slate-200 flex flex-col justify-between space-y-3">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <span class="bg-teal-100 text-teal-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                  SINTA 2 Terakreditasi
                </span>
                <span class="text-[11px] text-slate-400 font-medium">2023</span>
              </div>
              <h4 class="text-xs font-bold text-slate-900 leading-snug">
                Rasionalitas Penggunaan Antipiretik dan Hidrasi Oral pada Pasien Demam Komunitas Tropis
              </h4>
              <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                Jurnal Kedokteran Indonesia (JKI - FKUI). Analisis uji klinis efektivitas kompres hangat suam kuku dengan hidrasi terukur.
              </p>
            </div>
            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
              <span class="font-medium text-teal-700">DOI: 10.23886/ejki.11.234</span>
              <span class="font-semibold text-slate-700">9 Chunk Vektor</span>
            </div>
          </div>
          
          <!-- Journal 3: Scopus Q2 -->
          <div class="rounded-lg bg-slate-50 p-4 border border-slate-200 flex flex-col justify-between space-y-3">
            <div class="space-y-1.5">
              <div class="flex items-center justify-between">
                <span class="bg-sky-100 text-sky-800 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                  Scopus Q2 • BioMed Central
                </span>
                <span class="text-[11px] text-slate-400 font-medium">2024</span>
              </div>
              <h4 class="text-xs font-bold text-slate-900 leading-snug">
                Safety Guardrails in Healthcare Retrieval-Augmented Generation: Triage Sensitivity Benchmarks
              </h4>
              <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                BMC Medical Informatics. Evaluasi zero-shot triage red flag detection pada keluhan darurat pasien di faskes primer.
              </p>
            </div>
            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[10px] text-slate-500">
              <span class="font-medium text-teal-700">DOI: 10.1186/s12911-024-02451</span>
              <span class="font-semibold text-slate-700">22 Chunk Vektor</span>
            </div>
          </div>
        </div>
      </section>

      <!-- 4 PILAR ETIKA MEDIS & TATA KELOLA AI -->
      <section class="space-y-3">
        <div class="text-center max-w-xl mx-auto space-y-1">
          <span class="text-xs font-bold uppercase tracking-wider text-teal-700">Standard of Care</span>
          <h2 class="text-lg font-bold text-slate-900">4 Prinsip Keselamatan Pasien & Tata Kelola AI Medis</h2>
          <p class="text-xs text-slate-500">Memastikan integritas klinis, penolakan halusinasi, dan privasi penuh pasien.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col space-y-2">
            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
              <span class="material-symbols-outlined text-base">do_not_disturb_on</span>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Nol Resep Obat Keras</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              AI menolak menghitung dosis obat keras, antibiotik, dan zat terlarang tanpa dokter fisik, mencegah resistensi antimikroba.
            </p>
          </div>
          
          <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col space-y-2">
            <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
              <span class="material-symbols-outlined text-base">source</span>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Sitasi Dokumen Transparan</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Setiap klaim dilengkapi tautan sitasi dokumen asli [1][2] yang dapat diverifikasi langsung oleh pasien maupun tenaga medis.
            </p>
          </div>
          
          <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col space-y-2">
            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-700 flex items-center justify-center font-bold">
              <span class="material-symbols-outlined text-base">emergency</span>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Triase Red Flag 119/112</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Sistem mengenali keluhan gawat darurat dan langsung mengunci respons dengan tombol darurat panggilan ambulans nasional.
            </p>
          </div>
          
          <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col space-y-2">
            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
              <span class="material-symbols-outlined text-base">shield_person</span>
            </div>
            <h3 class="text-sm font-bold text-slate-900">Privasi Anonim (UU PDP)</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
              Tidak menyimpan NIK, nama lengkap, atau identitas pribadi. Kepatuhan mutlak regulasi UU Perlindungan Data Pribadi No. 27/2022.
            </p>
          </div>
        </div>
      </section>
    </div>


    <!-- ========================================================================== -->
    <!-- VIEW 2: KONSULTASI AI GEJALA RINGAN (RAG CHATBOT WORKSPACE) -->
    <!-- ========================================================================== -->
    <div id="view-konsultasi-ai-gejala-ringan" class="app-view hidden flex flex-col w-full space-y-5">
      
      <!-- Workspace Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 sm:p-5 rounded-xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-teal-700 text-white flex items-center justify-center shadow-sm">
            <span class="material-symbols-outlined text-xl">smart_toy</span>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-base font-bold text-slate-900">
                Konsultasi AI Gejala Ringan (RAG)
              </h2>
              <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[10px] font-bold tracking-wide uppercase">
                Pipeline Aktif
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Asisten edukasi berbasis bukti resmi Kemenkes RI & Jurnal Ilmiah. Tidak melayani diagnosis klinis definitif atau peresepan obat keras.
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <button id="clear-chat-btn" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer" type="button">
            <span class="material-symbols-outlined text-xs">delete</span>
            <span>Hapus Sesi</span>
          </button>
          <button id="export-chat-btn" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors flex items-center gap-1 cursor-pointer" type="button">
            <span class="material-symbols-outlined text-xs">download</span>
            <span>Unduh Catatan</span>
          </button>
        </div>
      </div>

      <!-- Patient Context Toolbar (Usia, Durasi, Kehamilan) -->
      <div class="p-3.5 rounded-xl bg-slate-100/90 border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-1.5 text-slate-800 font-semibold">
          <span class="material-symbols-outlined text-sm text-teal-700">tune</span>
          <span>Konteks Pasien (Opsional):</span>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex items-center gap-1">
            <label for="clarify-age" class="text-slate-500 font-medium">Usia:</label>
            <input id="clarify-age" type="number" min="0" max="110" placeholder="Thn" class="w-16 px-2 py-1 bg-white border border-slate-300 rounded text-center text-xs focus:outline-none focus:ring-1 focus:ring-teal-700"/>
          </div>
          <div class="flex items-center gap-1">
            <label for="clarify-duration" class="text-slate-500 font-medium">Durasi:</label>
            <select id="clarify-duration" class="px-2 py-1 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-teal-700">
              <option value="">Pilih durasi...</option>
              <option value="kurang dari 24 jam">&lt; 24 Jam</option>
              <option value="1-3 hari">1 - 3 Hari</option>
              <option value="lebih dari 3 hari">&gt; 3 Hari</option>
            </select>
          </div>
          <label class="flex items-center gap-1.5 cursor-pointer text-slate-700 font-medium">
            <input id="clarify-pregnant" type="checkbox" class="rounded text-teal-700 focus:ring-teal-700"/>
            <span>Sedang Hamil</span>
          </label>
        </div>
      </div>

      <!-- Quick Suggestion Prompts -->
      <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
        <span class="text-slate-400 font-medium shrink-0">Contoh Pertanyaan:</span>
        <button class="sample-prompt-chip px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap transition-colors cursor-pointer" data-prompt="Kaki saya terkilir saat main futsal, bagaimana pertolongan pertamanya?">
          ⚽ Kaki terkilir main futsal
        </button>
        <button class="sample-prompt-chip px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap transition-colors cursor-pointer" data-prompt="Anak saya demam dan batuk 2 hari, apa yang bisa dilakukan di rumah?">
          👶 Anak batuk pilek 2 hari
        </button>
        <button class="sample-prompt-chip px-2.5 py-1 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 whitespace-nowrap transition-colors cursor-pointer" data-prompt="Lutut lecet berdarah jatuh dari sepeda, bagaimana cara membersihkannya?">
          🩹 Lutut lecet jatuh sepeda
        </button>
        <button class="sample-prompt-chip px-2.5 py-1 rounded-full bg-red-100 hover:bg-red-200 text-red-800 font-semibold whitespace-nowrap transition-colors cursor-pointer" data-prompt="Dada kiri saya tiba-tiba sakit tertekan dan menjalar ke lengan!">
          🚨 Uji Triase Gawat Darurat (Red Flag)
        </button>
      </div>

      <!-- Chat Workspace Messages Box -->
      <div id="chat-messages" class="h-[520px] rounded-xl bg-white border border-slate-200 p-4 sm:p-6 overflow-y-auto space-y-4 shadow-inner custom-scrollbar flex flex-col">
        <!-- Rendered dynamically by app.js -->
      </div>

      <!-- Chat Input Area -->
      <div class="relative flex items-center bg-white rounded-xl shadow-sm border border-slate-300 focus-within:border-teal-700 focus-within:ring-2 focus-within:ring-teal-100 p-1.5 transition-all">
        <textarea id="chat-input" rows="2" class="w-full pl-3 pr-24 py-1.5 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none resize-none" 
                  placeholder="Ketikkan keluhan atau pertanyaan kesehatan Anda (misal: penanganan luka lecet, kaki terkilir, demam tinggi)..."></textarea>
        
        <button id="send-chat-btn" class="absolute right-2 px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 shadow-sm cursor-pointer" type="button">
          <span>Kirim</span>
          <span class="material-symbols-outlined text-xs">send</span>
        </button>
      </div>

      <div class="text-center">
        <p class="text-[11px] text-slate-400">
          🔒 Sesi Anonim Sesuai UU PDP No. 27/2022. MedikaRAG bukan dokter dan tidak menggantikan pemeriksaan fisik langsung.
        </p>
      </div>
    </div>


    <!-- ========================================================================== -->
    <!-- VIEW 3: MODUL JURNAL ILMIAH (SINTA 2 - SCOPUS) -->
    <!-- ========================================================================== -->
    <div id="view-modul-jurnal-ilmiah" class="app-view hidden flex flex-col w-full space-y-6">
      
      <!-- Module Header -->
      <div class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-3">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-100 text-sky-800 text-xs font-bold">
          <span class="material-symbols-outlined text-sm">menu_book</span> Basis Bukti Biomedis Terakreditasi
        </div>
        <h2 class="text-xl font-bold text-slate-900">Modul Jurnal Ilmiah (SINTA 2 s/d Scopus Q1-Q4)</h2>
        <p class="text-xs text-slate-600 max-w-3xl leading-relaxed">
          Repositori literatur peer-reviewed terindeks resmi yang berfungsi sebagai Lapisan Bukti Ilmiah (Layer 2) pada pipeline RAG. Hanya artikel dengan lisensi terbuka (Open Access / CC BY) yang diindeks teks lengkapnya.
        </p>

        <!-- Search & Filter Controls -->
        <div class="pt-2 grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
          <div class="md:col-span-6 relative">
            <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-base pointer-events-none">search</span>
            <input id="journal-search" class="w-full pl-9 pr-4 py-2 rounded-lg bg-slate-50 border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-1 focus:ring-sky-700" placeholder="Cari judul jurnal, penulis, atau kata kunci..." type="text"/>
          </div>

          <div class="md:col-span-3">
            <select id="journal-year-select" class="w-full px-3 py-2 rounded-lg bg-slate-50 border border-slate-300 text-xs text-slate-900 focus:outline-none">
              <option value="all">Semua Tahun Terbit</option>
              <option value="2024">Tahun 2024</option>
              <option value="2023">Tahun 2023</option>
              <option value="2022">Tahun 2022</option>
            </select>
          </div>

          <div class="md:col-span-3 text-right">
            <span class="text-xs font-bold text-sky-800 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-200 inline-block">
              Verifikasi SJR / Kemendikbudristek
            </span>
          </div>
        </div>

        <!-- Index Filter Chips -->
        <div class="flex items-center gap-1.5 overflow-x-auto pt-1 text-xs">
          <button class="journal-index-chip active-journal-chip px-3 py-1 rounded-full font-semibold bg-sky-700 text-white transition-all whitespace-nowrap" data-index="all">Semua Indeks</button>
          <button class="journal-index-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all whitespace-nowrap" data-index="Scopus Q1">Scopus Q1</button>
          <button class="journal-index-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all whitespace-nowrap" data-index="Scopus Q2">Scopus Q2</button>
          <button class="journal-index-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all whitespace-nowrap" data-index="SINTA 2">SINTA 2 Terakreditasi</button>
          <button class="journal-index-chip px-3 py-1 rounded-full font-medium bg-slate-100 text-slate-700 hover:bg-slate-200 transition-all whitespace-nowrap" data-index="SINTA 3">SINTA 3</button>
        </div>
      </div>

      <!-- Journal Cards Container -->
      <div id="journal-cards-container" class="space-y-4">
        <!-- Rendered dynamically by app.js -->
      </div>
    </div>


    <!-- ========================================================================== -->
    <!-- VIEW 4: PANDUAN P3K & GAWAT DARURAT (TRIAGE SYSTEM) -->
    <!-- ========================================================================== -->
    <div id="view-panduan-p3k-gawat-darurat" class="app-view hidden flex flex-col w-full space-y-6">
      
      <!-- P3K Header -->
      <div class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-2">
        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-xs font-bold">
          <span class="material-symbols-outlined text-sm">e911_emergency</span> Protokol Gawat Darurat & Keselamatan Fisik
        </div>
        <h2 class="text-xl font-bold text-slate-900">Panduan Praktis Pertolongan Pertama (P3K) & Triase</h2>
        <p class="text-xs text-slate-600 max-w-3xl leading-relaxed">
          Tindakan pertolongan pertama pada menit-menit krusial sebelum bantuan medis profesional tiba. Bersumber resmi dari standar pelayanan kegawatdaruratan Kemenkes RI dan IDI.
        </p>
      </div>

      <!-- Protocol R.I.C.E. Deep Dive Visualizer -->
      <section class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm">
            R
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900">Protokol Penanganan Terkilir & Keseleo: R.I.C.E.</h3>
            <span class="text-xs text-slate-500">Sumber: Ditjen Pelayanan Kesehatan Kemenkes RI (2024)</span>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-sm font-bold text-teal-700">1. Rest (Istirahat)</span>
            <p class="text-xs text-slate-600 leading-relaxed">Istirahatkan bagian tubuh yang cedera seketika. Jangan dipaksakan berjalan atau menumpu beban tubuh.</p>
          </div>
          <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-sm font-bold text-teal-700">2. Ice (Kompres Dingin)</span>
            <p class="text-xs text-slate-600 leading-relaxed">Kompres dingin es dibalut handuk selama 15-20 menit setiap 2-3 jam pada 48 jam pertama untuk vasokonstriksi pembuluh darah.</p>
          </div>
          <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-sm font-bold text-teal-700">3. Compression (Balut Tekan)</span>
            <p class="text-xs text-slate-600 leading-relaxed">Balut tekan sendi dengan perban elastis dari arah jari ke pangkal untuk membatasi penumpukan edema cairan radang.</p>
          </div>
          <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 space-y-1">
            <span class="text-sm font-bold text-teal-700">4. Elevation (Tinggikan)</span>
            <p class="text-xs text-slate-600 leading-relaxed">Tinggikan posisi bagian cedera lebih tinggi dari jantung saat berbaring untuk memperlancar aliran balik cairan.</p>
          </div>
        </div>

        <div class="p-3.5 rounded-lg bg-red-50 border border-red-200 text-xs text-red-900 flex items-center gap-2">
          <span class="material-symbols-outlined text-sm text-red-700 shrink-0">block</span>
          <span><strong>HINDARI H.A.R.M.</strong> selama 48 jam pertama: <em>Heat</em> (panas), <em>Alcohol</em>, <em>Running</em> (olahraga), dan <em>Massage</em> (pijat urut paksa) karena memperparah perdarahan jaringan sendi.</span>
        </div>
      </section>

      <!-- Checklist Kotak P3K Standar -->
      <section class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm space-y-3">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <span class="material-symbols-outlined text-teal-700 text-lg">medical_information</span>
          <span>Checklist Isi Kotak P3K Standar Rumah Tangga (Kemenkes)</span>
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs text-slate-700">
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Kassa steril berpori
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Perban elastis (crepe)
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Plester cepat tahan air
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Antiseptik povidone-iodine
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Cairan NaCl 0.9% irigasi
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Oralit sachet osmolaritas
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Termometer digital ketiak
          </div>
          <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-700 text-sm">check</span> Gunting perban & pinset
          </div>
        </div>
      </section>
    </div>


    <!-- ========================================================================== -->
    <!-- VIEW 5: PANEL VERIFIKASI KURATOR & ADMIN -->
    <!-- ========================================================================== -->
    <div id="view-panel-verifikasi-kurator" class="app-view hidden flex flex-col w-full space-y-6">
      
      <!-- Curator Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 rounded-xl bg-white border border-slate-200 shadow-sm">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 text-xs font-bold">
            <span class="material-symbols-outlined text-sm">admin_panel_settings</span> Modul Tata Kelola & Audit Ilmiah
          </div>
          <h2 class="text-xl font-bold text-slate-900">Panel Verifikasi Kurator Medis</h2>
          <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
            Area administrasi khusus kurator klinis untuk mengelola korpus dokumen Kemenkes, sinkronisasi vector database, aturan kata kunci darurat, dan audit trail model.
          </p>
        </div>

        <button id="reindex-btn" class="px-4 py-2 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer" type="button">
          <span class="material-symbols-outlined text-sm">sync</span>
          <span>Re-index Vector Database</span>
        </button>
      </div>

      <!-- Quick Stats Counters -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="p-4 rounded-xl bg-white border border-slate-200 text-center space-y-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dokumen Sumber</span>
          <div id="stat-docs" class="text-xl font-bold text-teal-700">16</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 text-center space-y-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Vector Chunks</span>
          <div id="stat-chunks" class="text-xl font-bold text-sky-700">67</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 text-center space-y-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jurnal Terindeks</span>
          <div id="stat-journals" class="text-xl font-bold text-teal-700">6</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 text-center space-y-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Konsultasi</span>
          <div id="stat-queries" class="text-xl font-bold text-slate-900">0</div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 text-center space-y-1">
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Triase 119 Terpicu</span>
          <div id="stat-emergency" class="text-xl font-bold text-red-600">0</div>
        </div>
      </div>

      <!-- Subtabs Navigation -->
      <div class="border-b border-slate-200 flex items-center gap-6 overflow-x-auto text-xs font-semibold">
        <button class="curator-tab-btn active-tab py-2.5 border-b-2 border-teal-700 text-teal-700 font-bold whitespace-nowrap transition-colors cursor-pointer" data-tab="documents">
          1. Basis Dokumen Sumber
        </button>
        <button class="curator-tab-btn py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 whitespace-nowrap transition-colors cursor-pointer" data-tab="emergency">
          2. Aturan Kata Kunci Darurat
        </button>
        <button class="curator-tab-btn py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 whitespace-nowrap transition-colors cursor-pointer" data-tab="logs">
          3. Audit Log Anonim (UU PDP)
        </button>
        <button class="curator-tab-btn py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 whitespace-nowrap transition-colors cursor-pointer" data-tab="evaluator">
          4. Evaluator Benchmark 90 Soal
        </button>
      </div>

      <!-- TAB VIEW 1: DOKUMEN SUMBER & UPLOAD -->
      <div id="tab-documents" class="curator-tab-view space-y-5">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
          <!-- Form Tambah Dokumen Baru -->
          <div class="lg:col-span-5 p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-3">
            <h4 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
              <span class="material-symbols-outlined text-teal-700 text-base">upload_file</span>
              <span>Tambah Dokumen Sumber Baru</span>
            </h4>
            <p class="text-xs text-slate-500 leading-relaxed">
              Dokumen akan otomatis dipecah menjadi chunks 300-500 token dengan overlap untuk retrieval RAG.
            </p>

            <form id="add-document-form" class="space-y-2.5 pt-1 text-xs">
              <div>
                <label class="block font-medium text-slate-800 mb-1">Judul Dokumen Resmi</label>
                <input id="doc-title" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-teal-700" placeholder="Misal: Protokol Penanganan Luka Bakar Ringan..."/>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block font-medium text-slate-800 mb-1">Penerbit</label>
                  <input id="doc-publisher" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none" value="Kementerian Kesehatan RI"/>
                </div>
                <div>
                  <label class="block font-medium text-slate-800 mb-1">Tahun Terbit</label>
                  <input id="doc-year" type="number" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none" value="2024"/>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <div>
                  <label class="block font-medium text-slate-800 mb-1">Lapisan Sumber</label>
                  <select id="doc-layer" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none">
                    <option value="Lapisan 1 (Kemenkes)">Lapisan 1 (Kemenkes)</option>
                    <option value="Lapisan 2 (Jurnal)">Lapisan 2 (Jurnal Ilmiah)</option>
                  </select>
                </div>
                <div>
                  <label class="block font-medium text-slate-800 mb-1">Topik Kategori</label>
                  <select id="doc-topic" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none">
                    <option value="Cedera Fisik & P3K">Cedera Fisik & P3K</option>
                    <option value="Penyakit Ringan">Penyakit Ringan</option>
                    <option value="Edukasi Kronis">Edukasi Kronis</option>
                    <option value="Gizi & Nutrisi">Gizi & Nutrisi</option>
                  </select>
                </div>
              </div>

              <div>
                <label class="block font-medium text-slate-800 mb-1">Lisensi Dokumen</label>
                <select id="doc-license" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none">
                  <option value="Hak Cipta Kemenkes RI">Hak Cipta Kemenkes RI / Edukasi Publik</option>
                  <option value="CC BY 4.0 Open Access">CC BY 4.0 Open Access</option>
                  <option value="Permenkes Terbuka RI">Regulasi Terbuka Pemerintah</option>
                </select>
              </div>

              <div>
                <label class="block font-medium text-slate-800 mb-1">URL Sumber Resmi</label>
                <input id="doc-url" type="url" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none" placeholder="https://kemkes.go.id/..."/>
              </div>

              <div>
                <label class="block font-medium text-slate-800 mb-1">Isi Konten Teks Dokumen</label>
                <textarea id="doc-content" rows="4" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none resize-none" placeholder="Masukkan ringkasan atau teks resmi dokumen medis..."></textarea>
              </div>

              <button type="submit" class="w-full py-2.5 px-4 bg-teal-700 hover:bg-teal-800 text-white rounded-lg text-xs font-semibold transition-colors shadow-sm cursor-pointer">
                Simpan & Otomatis Index Vector
              </button>
            </form>
          </div>

          <!-- List Dokumen Terdaftar -->
          <div class="lg:col-span-7 p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-3">
            <h4 class="text-sm font-bold text-slate-900">Daftar Dokumen Resmi Terverifikasi</h4>
            <div id="curator-documents-list" class="space-y-2 max-h-[580px] overflow-y-auto custom-scrollbar pr-1">
              <!-- Populated by app.js -->
            </div>
          </div>
        </div>
      </div>

      <!-- TAB VIEW 2: ATURAN KATA KUNCI DARURAT -->
      <div id="tab-emergency" class="curator-tab-view hidden space-y-4">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
          <div>
            <h4 class="text-sm font-bold text-slate-900">Pengaturan Kata Kunci Gawat Darurat (Red Flags)</h4>
            <p class="text-xs text-slate-500">Kelola daftar kata kunci pemicu triase darurat tanpa perlu mengubah kode sumber (PRD FR-20).</p>
          </div>

          <!-- Add rule inline -->
          <form id="add-rule-form" class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-center gap-2">
            <input id="rule-keyword" required placeholder="Kata kunci baru (misal: racun serangga)..." class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none"/>
            <input id="rule-action" required placeholder="Tindakan / Arahkan ke IGD 119..." class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none" value="Arahkan ke IGD / 119"/>
            <button type="submit" class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded text-xs font-semibold transition-colors shadow-sm shrink-0 cursor-pointer">
              Tambah Rule
            </button>
          </form>

          <!-- Table of active emergency keywords -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="border-b border-slate-200 font-semibold text-slate-700 bg-slate-50">
                  <th class="py-2.5 px-3">Kata Kunci (Regex Token)</th>
                  <th class="py-2.5 px-3">Tingkat Keparahan</th>
                  <th class="py-2.5 px-3">Tindakan Protokol</th>
                </tr>
              </thead>
              <tbody id="emergency-rules-list" class="divide-y divide-slate-100">
                <!-- Populated by app.js -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB VIEW 3: AUDIT LOG ANONIM -->
      <div id="tab-logs" class="curator-tab-view hidden space-y-4">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <h4 class="text-sm font-bold text-slate-900">Audit Log Pertanyaan Anonim (UU PDP No. 27/2022)</h4>
              <p class="text-xs text-slate-500">Memantau pertanyaan yang memicu fallback atau deteksi red flag guna penyempurnaan kurasi basis pengetahuan.</p>
            </div>
            
            <select id="audit-log-filter" class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-300 text-xs font-medium" onchange="loadAuditLogs()">
              <option value="all">Semua Log</option>
              <option value="fallback">Hanya Pemicu Fallback</option>
              <option value="emergency">Hanya Gawat Darurat (Red Flags)</option>
              <option value="forbidden">Hanya Dosis/Diagnosis Terlarang</option>
            </select>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
              <thead>
                <tr class="border-b border-slate-200 font-semibold text-slate-700 bg-slate-50">
                  <th class="py-2.5 px-3">Waktu</th>
                  <th class="py-2.5 px-3">Pertanyaan Anonim</th>
                  <th class="py-2.5 px-3">Kategori Status</th>
                  <th class="py-2.5 px-3">Skor RAG</th>
                  <th class="py-2.5 px-3">Pratinjau Respons</th>
                </tr>
              </thead>
              <tbody id="audit-logs-list" class="divide-y divide-slate-100">
                <!-- Populated by app.js -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- TAB VIEW 4: EVALUATOR BENCHMARK 90 SOAL -->
      <div id="tab-evaluator" class="curator-tab-view hidden space-y-4">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <h4 class="text-sm font-bold text-slate-900">Benchmarking Otomatis Evaluasi Chatbot (Dataset 90 Soal)</h4>
              <p class="text-xs text-slate-500 max-w-2xl leading-relaxed">
                Menguji set evaluasi 90 pertanyaan: 46 ada jawaban, 11 tidak ada di data, 14 butuh diagnosis/dosis, 12 darurat, 7 di luar topik sesuai PRD Bagian 11.
              </p>
            </div>
            
            <button id="run-eval-btn" class="px-4 py-2.5 rounded-lg bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold transition-colors flex items-center gap-1.5 shadow-sm shrink-0 cursor-pointer" type="button">
              <span class="material-symbols-outlined text-sm">play_arrow</span>
              <span>Jalankan Benchmark 90 Soal</span>
            </button>
          </div>

          <div id="eval-report-container">
            <div class="p-8 text-center rounded-xl bg-slate-50 border border-dashed border-slate-300 space-y-2">
              <span class="material-symbols-outlined text-3xl text-teal-700">assessment</span>
              <h5 class="text-sm font-bold text-slate-900">Evaluator Siap Digunakan</h5>
              <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                Klik tombol "Jalankan Benchmark 90 Soal" di atas untuk menguji ketepatan guardrails darurat, penolakan dosis, dan grounding sitasi secara real-time.
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>

  </main>


  <!-- ============================================================================== -->
  <!-- FLOATING PERSISTENT CHATBOT WIDGET -->
  <!-- ============================================================================== -->
  <div class="fixed bottom-6 right-6 z-40">
    <!-- Trigger Button -->
    <button id="floating-chat-toggle" class="w-12 h-12 rounded-full bg-teal-700 hover:bg-teal-800 text-white shadow-lg flex items-center justify-center transition-transform hover:scale-105 cursor-pointer" title="Buka Konsultasi Cepat AI">
      <span class="material-symbols-outlined text-xl">chat</span>
    </button>

    <!-- Floating Chat Drawer / Popover -->
    <div id="floating-chat-drawer" class="hidden absolute bottom-16 right-0 w-[340px] sm:w-[380px] rounded-xl bg-white shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50">
      <div class="px-4 py-3 bg-teal-700 text-white flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-base">smart_toy</span>
          <div>
            <h4 class="text-xs font-bold leading-none">MedikaRAG Mini</h4>
            <span class="text-[10px] text-teal-100 font-medium">RAG Grounded Kemenkes</span>
          </div>
        </div>
        <button id="close-floating-chat" class="w-6 h-6 rounded-full bg-teal-800 hover:bg-teal-900 flex items-center justify-center transition-colors cursor-pointer">
          <span class="material-symbols-outlined text-xs">close</span>
        </button>
      </div>

      <div id="floating-chat-messages" class="h-72 p-3 overflow-y-auto space-y-2 custom-scrollbar flex flex-col bg-slate-50 text-xs">
        <div class="p-2.5 rounded-lg bg-white border border-slate-200 text-slate-800 self-start max-w-[90%] shadow-2xs">
          Halo! Ada keluhan gejala ringan atau pertolongan pertama P3K yang ingin Anda tanyakan?
        </div>
      </div>

      <div class="p-2 bg-white border-t border-slate-200 flex items-center gap-1.5">
        <input id="floating-chat-input" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded text-xs focus:outline-none" placeholder="Tanya gejala..."/>
        <button id="send-floating-btn" class="p-1.5 rounded bg-teal-700 hover:bg-teal-800 text-white flex items-center justify-center cursor-pointer">
          <span class="material-symbols-outlined text-xs">send</span>
        </button>
      </div>
    </div>
  </div>


  <!-- ============================================================================== -->
  <!-- MODALS -->
  <!-- ============================================================================== -->
  <!-- 1. Disease Detail Modal -->
  <div id="disease-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div id="disease-modal-content" class="bg-white rounded-xl max-w-3xl w-full shadow-2xl border border-slate-200 overflow-hidden">
      <!-- Populated dynamically -->
    </div>
  </div>

  <!-- 2. Citation Detail Modal -->
  <div id="citation-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div id="citation-modal-body" class="bg-white rounded-xl max-w-xl w-full shadow-2xl border border-slate-200 overflow-hidden">
      <!-- Populated dynamically -->
    </div>
  </div>

  <!-- 3. Journal Detail Modal -->
  <div id="journal-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div id="journal-modal-body" class="bg-white rounded-xl max-w-2xl w-full shadow-2xl border border-slate-200 overflow-hidden">
      <!-- Populated dynamically -->
    </div>
  </div>


  <!-- ============================================================================== -->
  <!-- FOOTER (OFFICIAL HEALTHCARE & LEGAL DISCLAIMER) -->
  <!-- ============================================================================== -->
  <footer class="w-full bg-white border-t border-slate-200 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8">
      
      <!-- Top Disclaimer Box -->
      <div class="p-4 rounded-xl bg-slate-50 mb-6 flex flex-col md:flex-row items-start md:items-center gap-3 border border-slate-200">
        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-base">verified_user</span>
        </div>
        <div class="flex-1">
          <h4 class="text-xs font-bold text-slate-900 mb-0.5">Disclaimer Medis & Batasan Tanggung Jawab Hukum</h4>
          <p class="text-xs text-slate-600 leading-relaxed">
            MedikaRAG menyajikan edukasi promotif-preventif mandiri dan konsultasi berbasis <em>retrieval-augmented generation</em> (RAG) dari dokumen resmi Kementerian Kesehatan Republik Indonesia dan jurnal ilmiah biomedis terakreditasi. Platform ini <strong>tidak melayani diagnosis klinis definitif</strong>, peresepan obat keras, maupun pengganti interaksi dokter-pasien langsung sesuai amanat UU No. 17 Tahun 2023 tentang Kesehatan.
          </p>
        </div>
      </div>
      
      <!-- Footer Columns -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="space-y-2">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-teal-700 flex items-center justify-center text-white">
              <span class="material-symbols-outlined text-xs">medical_services</span>
            </div>
            <span class="text-sm font-bold text-slate-900">MedikaRAG</span>
          </div>
          <p class="text-xs text-slate-500 leading-relaxed">
            Sistem komputasi retrieval terkurasi dokter spesialis, menghubungkan literatur biomedis resmi dengan kebutuhan literasi kesehatan masyarakat.
          </p>
        </div>
        
        <div class="space-y-1.5">
          <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Hotline Kedaruratan</h5>
          <ul class="text-xs text-slate-600 space-y-1">
            <li><strong class="text-red-700">119</strong> - Ambulans SPGDT Kemenkes RI</li>
            <li><strong class="text-red-700">112</strong> - Layanan Tanggap Darurat Nasional</li>
            <li><strong class="text-slate-800">110</strong> - Layanan Presisi Kepolisian RI</li>
            <li><strong class="text-teal-700">1500-567</strong> - Contact Center Halo Kemenkes</li>
          </ul>
        </div>
        
        <div class="space-y-1.5">
          <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Standar & Indeksasi</h5>
          <ul class="text-xs text-slate-600 space-y-1">
            <li>Pedoman Nasional Pelayanan Kedokteran (PNPK)</li>
            <li>Konsensus Ikatan Dokter Indonesia (IDI)</li>
            <li>Jurnal Kedokteran Terakreditasi SINTA 2</li>
            <li>Repositori Scopus Q1/Q2 Biomedical</li>
          </ul>
        </div>
        
        <div class="space-y-1.5">
          <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Keamanan & Tata Kelola</h5>
          <p class="text-xs text-slate-500 leading-relaxed">
            Sistem beroperasi tanpa pengumpulan identitas personal (zero PII storage) mematuhi mandat UU Pelindungan Data Pribadi (UU PDP No. 27/2022).
          </p>
        </div>
      </div>
      
      <!-- Bottom Copyright & Policies -->
      <div class="pt-4 flex flex-col md:flex-row items-center justify-between gap-3 text-center md:text-left border-t border-slate-200 text-xs text-slate-500">
        <p>
          © 2025 MedikaRAG Indonesia. Terintegrasi Pedoman Resmi Kemenkes RI.
        </p>
        <div class="flex items-center gap-4">
          <span class="hover:text-slate-900 transition-colors cursor-pointer">Kebijakan Privasi</span>
          <span class="hover:text-slate-900 transition-colors cursor-pointer">Ketentuan Layanan RAG</span>
          <span class="hover:text-slate-900 transition-colors cursor-pointer">Standar Etik AI Medis</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- Application Logic -->
  <script src="{{ asset('static/js/app.js') }}"></script>
</body>
</html>
