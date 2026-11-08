<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disease;
use App\Models\Journal;
use App\Models\Document;
use App\Models\Chunk;
use App\Models\EmergencyRule;
use App\Models\AppConfig;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clear existing data
        Chunk::truncate();
        Document::truncate();
        Journal::truncate();
        Disease::truncate();
        EmergencyRule::truncate();
        AppConfig::truncate();

        // 2. Load Diseases
        $diseases = [
            [
                'slug' => 'keseleo-terkilir-sprain',
                'title' => 'Keseleo & Terkilir (Sprain & Strain)',
                'category' => 'p3k',
                'source_label' => 'Ditjen Keslan Kemenkes RI 2024',
                'badge_protocol' => 'Protokol R.I.C.E.',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAKVkEv1QLDyf_KOaSsZgI_sAepb4P0Fw93GyPiZmhPHohQWHYFADTWiHii4djgjODUNdNaK0-0bsGRqxvxHoVcg6l1LMEjaza1VMhjcsJetzzAnIONcLhGsX7KA0CMYfzmHdtfozdhbHi66kxIBQRxyq1hs-qFR3F17y-tod203YGlwS9HuLBoaV6aXCjnJZcO6PMMjChNnuS-ObTpAgRgZgBXgHiQfjZq_dNef50',
                'summary' => 'Tindakan segera penanganan ligamen atau otot teregang. Hindari pijat paksa (H.A.R.M) pada 48 jam pertama untuk mencegah robekan mikroskopik jaringan lebih lanjut.',
                'definition' => 'Keseleo (sprain) adalah cedera regangan atau robekan pada ligamen (jaringan pengikat antar tulang), sedangkan terkilir (strain) adalah cedera pada otot atau tendon yang menghubungkan otot ke tulang. Sering terjadi pada pergelangan kaki, lutut, dan pergelangan tangan akibat salah langkah atau benturan olahraga.',
                'symptoms' => 'Nyeri mendadak pada sendi yang cedera, pembengkakan lokal, memar kemerahan atau kebiruan, rasa hangat pada area cedera, serta keterbatasan gerak sendi saat menumpu beban.',
                'causes' => 'Gerakan memutar yang tiba-tiba, terpeleset pada permukaan tidak rata, mendarat tidak sempurna saat melompat, atau peregangan otot melebihi batas elastisitas normal.',
                'self_care_steps' => "1. REST (Istirahatkan): Segera hentikan aktivitas fisik dan jangan menumpukan berat badan pada kaki/sendi yang cedera.\n2. ICE (Kompres Dingin): Kompres dengan es batu yang dibungkus kain bersih selama 15-20 menit setiap 2-3 jam sekali selama 48 jam pertama.\n3. COMPRESSION (Balut Tekan): Balut sendi dengan perban elastis (crepe bandage) dari arah ujung jari ke pangkal.\n4. ELEVATION (Tinggikan): Posisikan sendi yang cedera lebih tinggi dari posisi jantung saat berbaring.\n5. HINDARI H.A.R.M. selama 48 jam pertama: Heat (panas), Alcohol, Running (olahraga), dan Massage (pijat urut paksa).",
                'red_flags' => 'Sendi tampak berubah bentuk (deformitas nyata), tulang mencuat, ketidakmampuan menahan beban sama sekali (curiga fraktur), kesemutan/mati rasa pada ujung jari.',
                'when_to_doctor' => 'Segera ke dokter atau IGD jika sendi tidak dapat digerakkan sama sekali, terdengar bunyi pop keras saat cedera, atau pembengkakan tidak berkurang setelah 72 jam.',
                'prevention' => 'Lakukan pemanasan dinamis 10-15 menit sebelum olahraga, gunakan alas kaki yang stabil dan sesuai ukuran.',
                'references_text' => "1. Ditjen Pelayanan Kesehatan Lanjutan Kemenkes RI (2024) - Keseleo Ingat RICE.\n2. British Journal of Sports Medicine (BJSM 2024) - Ankle Sprains.",
                'last_reviewed' => '15 Oktober 2025'
            ],
            [
                'slug' => 'batuk-pilek-ispa-ringan',
                'title' => 'Batuk Pilek & ISPA Non-Komplikasi',
                'category' => 'ringan',
                'source_label' => 'Mediakom Kemenkes RI #159',
                'badge_protocol' => 'Manajemen Self-Care 3-5 Hari',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB-4-QAuXYZnbijLd7flaLz32nTgy1S0uKN8ji6-mWebXP1EJvL3JKNuM8HW_ztl-9t6wfkDWvwcbhlXoJi7X53HFFHiT3LskSIgdt_HAUlkDMg1PNvJwDj_zd06r6AgGI7gpyYrwa1xLFoVScdBDTZqOWhpatm_EotatiyEfqWnk0-tiHu4lRjHdvbAdC2KYP1FojcoFJ0qIvbXfjeu13TWE5I6DlDiETtjsYut1w',
                'summary' => 'Sebagian besar infeksi saluran napas atas bersifat virus (self-limiting). Prioritaskan hidrasi cukup, madu untuk batuk anak >1 tahun, dan istirahat optimal.',
                'definition' => 'Infeksi Saluran Pernapasan Akut (ISPA) ringan non-komplikasi meliputi salesma (common cold), faringitis ringan, dan rinitis akibat infeksi virus (rhinovirus, adenovirus). Sembuh sendiri dalam rentang 7-10 hari.',
                'symptoms' => 'Hidung tersumbat, bersin-bersin, lendir ingus bening, tenggorokan terasa gatal/perih, batuk kering atau berdahak ringan, dan demam subfebris (<38.5°C).',
                'causes' => 'Penularan droplet melalui udara atau kontak tangan dengan permukaan yang terkontaminasi virus pernapasan saat daya tahan tubuh menurun.',
                'self_care_steps' => "1. Hidrasi Optimal: Minum air putih hangat 2 hingga 2.5 liter per hari untuk mengencerkan dahak.\n2. Terapi Alami Madu: 1-2 sendok teh madu murni (hanya untuk anak usia >1 tahun dan dewasa) sebelum tidur.\n3. Cuci Hidung dengan Larutan Salin: Bilas rongga hidung memakai larutan steril NaCl 0.9%.\n4. Istirahat Cukup 7-8 jam per hari.\n5. JANGAN MENGONSUMSI ANTIBIOTIK TANPA RESEP DOKTER: Infeksi virus tidak dapat dibunuh oleh antibiotik.",
                'red_flags' => 'Napas cepat (>40x/menit pada anak, >24x/menit pada dewasa), tarikan dinding dada ke dalam, bibir/kuku membiru, dahak berdarah banyak, atau demam tinggi >39°C bertahan >3 hari.',
                'when_to_doctor' => 'Kunjungi Puskesmas atau dokter jika batuk pilek berlanjut lebih dari 10 hari tanpa perbaikan, nyeri telinga hebat, atau timbul sesak napas.',
                'prevention' => 'Cuci tangan dengan sabun 20 detik, etika batuk tutup lengan baju dalam, gunakan masker saat bergejala.',
                'references_text' => "1. Majalah Mediakom Kemenkes RI Edisi 159 (2023) - Perawatan ISPA.\n2. Acta Medica Indonesiana (2023).",
                'last_reviewed' => '20 September 2025'
            ],
            [
                'slug' => 'demam-akut-anak-dewasa',
                'title' => 'Demam Akut pada Anak & Dewasa',
                'category' => 'ringan',
                'source_label' => 'Standar Kemenkes RI 2024',
                'badge_protocol' => 'Pedoman Termoregulasi',
                'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAajQgtQVxiHEg3Jl1WINLMrrrLr6rJouKOYPSVJEowCTHPQRcxsUHknK98d4zuhuNC5sLV2sgm7L8FWbTuXtmxJ1kticymMX2owl4nvpWr5uPa8JImcSuSAIJYVhS4EldirMH570aGQzZuJUYSxwaQXWf_B7zZppI-C0NXP9JZUvwEov_7w4gsOvQesYvTwoHnO1e8wfnebbag-kuRCffIwAc9Y1WkqmtQYUvw5xA',
                'summary' => 'Panduan observasi suhu berkala, kompres air hangat suam-suam kuku (bukan air dingin atau alkohol), serta indikator klinis dehidrasi yang mewajibkan ke faskes.',
                'definition' => 'Demam adalah kenaikan suhu tubuh inti di atas nilai normal (suhu ketiak ≥37.5°C atau suhu rektal ≥38.0°C). Merupakan respon fisiologis alami sistem imun melawan infeksi mikroorganisme.',
                'symptoms' => 'Tubuh teraba panas, menggigil saat suhu mulai naik, berkeringat saat suhu turun, wajah kemerahan, sakit kepala ringan, nafsu makan berkurang.',
                'causes' => 'Infeksi virus (salesma, influenza, demam berdarah awal), infeksi bakteri, atau reaksi pasca imunisasi.',
                'self_care_steps' => "1. Pengukuran Berkala: Gunakan termometer digital ketiak tiap 4 jam.\n2. Kompres Hangat Suam Kuku (Tepid Sponging): Kompres ketiak dan lipat paha dengan handuk basah hangat (30-32°C). DILARANG menggunakan air es atau alkohol.\n3. Pakaian Tipis: Kenakan katun tipis yang menyerap keringat. Jangan membungkus selimut tebal bertumpuk.\n4. Cukupi Cairan: Minum air putih hangat, kaldu, atau oralit bertahap.\n5. Paracetamol bebas dapat diberikan bila suhu >38.5°C dengan petunjuk kemasan/apoteker.",
                'red_flags' => 'Demam pada bayi usia <3 bulan (langsung ke IGD), kejang demam (kelojotan/mata terbalik ke atas), kaku kuduk, muntah menyembur, bintik merah petekie tidak memudar saat ditekan.',
                'when_to_doctor' => 'Bawa ke faskes jika demam bertahan >3 hari, suhu ≥40°C, atau ada tanda dehidrasi (BAK <4 kali sehari, urin pekat, air mata kering).',
                'prevention' => 'Imunisasi tepat waktu, kebersihan diri, dan isolasi mandiri dari individu sakit aktif.',
                'references_text' => "1. Jurnal Kedokteran Indonesia (JKI FKUI 2023) - Hidrasi Oral Demam Tropis.\n2. Pedoman Termoregulasi Anak Kemenkes RI (2024).",
                'last_reviewed' => '05 Oktober 2025'
            ],
            [
                'slug' => 'diare-akut-tanpa-dehidrasi',
                'title' => 'Diare Akut Tanpa Dehidrasi Berat',
                'category' => 'ringan',
                'source_label' => 'Protokol LINTAS Diare Kemenkes',
                'badge_protocol' => '5 Pilar LINTAS Diare',
                'image_url' => '',
                'summary' => 'Cegah komplikasi kehilangan cairan tubuh dengan larutan oralit standar osmolaritas rendah. Larangan antibiotik tanpa resep dokter untuk mencegah resistensi.',
                'definition' => 'Diare akut adalah buang air besar (BAB) lembek atau cair dengan frekuensi lebih dari 3 kali dalam 24 jam, berlangsung kurang dari 14 hari.',
                'symptoms' => 'BAB cair, kram mulas perut, mual ringan, kembung, rasa lemas, dan rasa haus meningkat.',
                'causes' => 'Infeksi virus (rotavirus, norovirus), infeksi bakteri makanan (E. coli, Salmonella), intoleransi laktosa, atau keracunan makanan ringan.',
                'self_care_steps' => "1. Pilar 1 - Berikan Oralit Segera: 1 gelas oralit standar osmolaritas rendah tiap kali selesai BAB cair.\n2. Pilar 2 - Tablet Zinc 10-14 Hari: Membantu regenerasi mukosa usus dan mencegah diare kambuh.\n3. Pilar 3 - Teruskan ASI & Makanan Bergizi: Berikan makanan lunak porsi kecil tapi sering (bubur, pisang, sup).\n4. Pilar 4 - Antibiotik Selektif: JANGAN membeli antibiotik atau obat pemampat diare tanpa resep dokter.\n5. Pilar 5 - Edukasi Higiene: Cuci tangan sabun sebelum makan dan setelah buang air.",
                'red_flags' => 'Darah dalam tinja (disentri), muntah terus menerus tidak bisa minum, rasa haus sangat ekstrem, mata sangat cekung, turgor kulit kembali sangat lambat (>2 detik).',
                'when_to_doctor' => 'Bawa ke faskes bila diare tidak membaik dalam 2 hari, BAB cair >6 kali sehari, demam tinggi, atau anak lemas tidur terus.',
                'prevention' => 'Konsumsi air minum matang mendidih, cuci bersih bahan pangan, dan sanitasi jamban sehat.',
                'references_text' => "1. Subdit Diare Ditjen P2P Kemenkes RI (2023) - Manajemen LINTAS Diare.\n2. WHO Guidelines for Diarrhea Management.",
                'last_reviewed' => '12 Oktober 2025'
            ],
            [
                'slug' => 'luka-lecet-dan-terbuka-ringan',
                'title' => 'Luka Lecet & Luka Terbuka Ringan',
                'category' => 'p3k',
                'source_label' => 'IDI & Kemenkes P3K',
                'badge_protocol' => 'Irigasi Air Mengalir Bersih',
                'image_url' => '',
                'summary' => 'Prosedur pembersihan debu/kotoran memakai air mengalir hangat atau larutan NaCl 0.9%, antiseptik povidone-iodine topikal, dan penutupan kassa steril kedap udara.',
                'definition' => 'Luka lecet (abrasi) adalah cedera pada lapisan permukaan epidermis kulit terluar, sedangkan luka terbuka ringan adalah robekan dangkal yang tidak mengenai pembuluh darah besar atau otot.',
                'symptoms' => 'Rasa perih menyengat, keluarnya cairan bening kekuningan (serum) atau rembesan darah merah segar, kulit sekitar kemerahan.',
                'causes' => 'Gesekan permukaan kasar (aspal, lantai saat terjatuh) atau tergores benda tumpul/tajam ringan.',
                'self_care_steps' => "1. Cuci Tangan Higienis pakai sabun.\n2. Hentikan Perdarahan: Tekan lembut memakai kassa steril selama 3-5 menit.\n3. Irigasi Air Mengalir: Guyur luka dengan air mengalir bersih atau cairan NaCl 0.9% selama 5-10 menit untuk mengangkat debu/pasir. Jangan menggosok kasar.\n4. Antiseptik Topikal: Oleskan povidone-iodine atau petroleum jelly tipis.\n5. Tutup dengan Balutan Steril: Plester cepat atau kassa steril berpori. Ganti minimal 1x sehari.",
                'red_flags' => 'Darah menyembur atau mengalir deras tidak berhenti setelah ditekan 10 menit, luka dalam (>1 cm) menganga terlihat lemak/otot, gigitan hewan, terkena karat besi (bahaya Tetanus).',
                'when_to_doctor' => 'Periksakan bila luka memerlukan jahitan, ada tanda infeksi dalam 24-48 jam (makin merah, hangat berdenyut, bernanah), atau belum vaksin tetanus dalam 5-10 tahun.',
                'prevention' => 'Gunakan pelindung siku dan lutut saat olahraga, gunakan sarung tangan berkebun, simpan kotak P3K siap pakai.',
                'references_text' => "1. Pedoman Pertolongan Pertama Gawat Darurat IDI (2023).\n2. Standar Pelayanan Luka Primer Kemenkes RI.",
                'last_reviewed' => '18 Oktober 2025'
            ],
            [
                'slug' => 'waspada-demam-berdarah-dengue-dbd',
                'title' => 'Pencegahan & Waspada Fase DBD',
                'category' => 'kronis',
                'source_label' => 'Subdit Arbovirus Kemenkes',
                'badge_protocol' => 'Fase Kritis Pelana Kuda',
                'image_url' => '',
                'summary' => 'Memahami grafik penurunan suhu palsu pada Demam Berdarah Dengue. Waspadai tanda kebocoran plasma seperti ujung jari dingin dan nyeri perut hebat.',
                'definition' => 'Demam Berdarah Dengue (DBD) adalah infeksi arbovirus yang ditularkan nyamuk Aedes aegypti betina, dengan perjalanan klinis khas siklus pelana kuda.',
                'symptoms' => 'Demam tinggi mendadak (39-40°C) selama 2-7 hari, nyeri di belakang bola mata, nyeri sendi dan otot hebat, bintik merah petekie, lemas.',
                'causes' => 'Infeksi virus Dengue (DENV-1 s/d DENV-4) yang merusak sel endotel pembuluh darah dan trombosit.',
                'self_care_steps' => "1. Kenali Fase Kritis (Hari ke-3 sampai ke-5): Suhu turun mendadak BUKAN sembuh, melainkan risiko kebocoran plasma dan syok tertinggi.\n2. Terapi Hidrasi Agresif: Minum cairan elektrolit (oralit, jus buah, air kelapa, kaldu) minimal 2-3 liter per hari.\n3. Tirah baring total.\n4. Antipiretik HANYA Paracetamol: HINDARI aspirin, ibuprofen, atau asam mefenamat karena memperparah perdarahan.\n5. Cek laboratorium trombosit & hematokrit tiap 24 jam mulai hari ke-3.",
                'red_flags' => 'Tanda Bahaya Kebocoran Plasma: Nyeri perut hebat terus menerus, muntah persisten, mimisan berulang, gusi berdarah, serta telapak tangan dan kaki teraba dingin pucat lembap (Dengue Shock Syndrome).',
                'when_to_doctor' => 'Bawa SEGERA ke IGD saat memasuki hari ke-3 jika ada salah satu tanda bahaya di atas.',
                'prevention' => 'PSN 3M Plus: Menguras wadah air, Menutup rapat penampungan, Mendaur ulang barang bekas, plus kelambu dan larvasida abate.',
                'references_text' => "1. Pedoman Pencegahan dan Pengendalian DBD Kemenkes RI (2017).\n2. Pedoman Tatalaksana Dengue PAPDI & IDAI (2023).",
                'last_reviewed' => '22 Oktober 2025'
            ],
            [
                'slug' => 'hipertensi-edukasi-pencegahan',
                'title' => 'Hipertensi & Gaya Hidup Sehat',
                'category' => 'kronis',
                'source_label' => 'Direktorat P2PTM Kemenkes RI',
                'badge_protocol' => 'Protokol CERDIK & Diet DASH',
                'image_url' => '',
                'summary' => 'Edukasi mandiri pengelolaan tekanan darah tinggi, pembatasan asupan natrium harian (maksimal 1 sendok teh garam), dan pengenalan krisis hipertensi.',
                'definition' => 'Hipertensi adalah kondisi tekanan darah sistolik ≥140 mmHg dan/atau diastolik ≥90 mmHg pada pengukuran berulang di faskes. Sering tanpa gejala awal (The Silent Killer).',
                'symptoms' => 'Sering asimtomatik; dapat memicu tengkuk kaku, sakit kepala belakang saat bangun pagi, mudah lelah, jantung berdebar.',
                'causes' => 'Faktor genetik, konsumsi garam natrium berlebih, obesitas, kurang gerak, stres kronis, merokok, penuaan pembuluh darah.',
                'self_care_steps' => "1. Batasi Garam (Natrium): Maksimal 1 sendok teh (5 gram atau 2000 mg natrium) per orang per hari.\n2. Diet DASH: Perbanyak sayuran hijau, buah tinggi kalium (pisang, jeruk), biji-bijian utuh, dan ikan.\n3. Aktivitas Fisik: Olahraga aerobik sedang 30 menit/hari, minimal 150 menit per minggu.\n4. Manajemen Stres & Tidur 7-8 jam per malam.\n5. Pantau tensi digital berkala di rumah saat kondisi tenang.",
                'red_flags' => 'Krisis Hipertensi: Tensi >180/120 mmHg disertai nyeri dada menusuk, sesak napas berat, sakit kepala hebat mendadak, kelemahan separuh tubuh, atau penurunan kesadaran (Darurat IGD).',
                'when_to_doctor' => 'Konsultasikan ke dokter Puskesmas untuk evaluasi dan obat antihipertensi jangka panjang. Jangan menghentikan obat resep sendiri.',
                'prevention' => 'Prinsip CERDIK: Cek kesehatan berkala, Enyahkan rokok, Rajin aktivitas, Diet sehat seimbang, Istirahat cukup, Kelola stres.',
                'references_text' => "1. Direktorat P2PTM Kemenkes RI - Pedoman Tata Laksana Hipertensi.\n2. InaSH Guidelines (2023).",
                'last_reviewed' => '14 Oktober 2025'
            ],
            [
                'slug' => 'diabetes-melitus-edukasi-hipoglikemia',
                'title' => 'Diabetes Melitus Tipe 2 & Waspada Hipoglikemia',
                'category' => 'kronis',
                'source_label' => 'Pedoman Pengendalian DM Kemenkes',
                'badge_protocol' => 'Prinsip 3J & Rule of 15',
                'image_url' => '',
                'summary' => 'Pengaturan pola makan 3J (Jadwal, Jumlah, Jenis), perawatan kaki diabetik mandiri, dan penanganan darurat gejala gula darah drop (hipoglikemia).',
                'definition' => 'Diabetes Melitus Tipe 2 adalah gangguan metabolik kronis ditandai hiperglikemia akibat defisiensi insulin relatif dan resistensi insulin sel tubuh.',
                'symptoms' => 'Trias klasik: Sering kencing malam hari (poliuria), sering haus berlebih (polidipsia), sering lapar (polifagia), berat badan turun tanpa sebab jelas.',
                'causes' => 'Gaya hidup sedentari, pola makan tinggi gula sederhana dan lemak jenuh, riwayat genetik, obesitas sentral.',
                'self_care_steps' => "1. Terapkan Prinsip 3J: Tepat Jadwal, Jumlah porsi, dan Jenis karbohidrat kompleks berserat.\n2. Kenali Tanda Hipoglikemia: Gula darah <70 mg/dL (gemetar, keringat dingin membasahi pakaian, jantung berdebar, pusing lemas).\n3. Aturan 15 Menit: Segera minum 1 gelas air gula (2 sendok makan gula pasir atau teh manis kemasan). Istirahat 15 menit, lalu cek kembali gula darah.\n4. Perawatan Kaki Harian: Periksa telapak kaki dari lecet, cuci kaki dan keringkan sela jari, pakai alas kaki empuk.\n5. Cek gula darah puasa dan 2 jam pasca makan terjadwal.",
                'red_flags' => 'Penurunan kesadaran koma diabetik, napas berbau buah manis (Ketoasidosis), luka bernanah berbau busuk pada kaki yang meluas (gangren).',
                'when_to_doctor' => 'Kunjungi dokter bila gula darah puasa >130 mg/dL terus menerus, sering hipoglikemia, atau ada luka baru di kaki tidak sembuh dalam 3 hari.',
                'prevention' => 'Kurangi minuman manis berpemanis, jalan 10.000 langkah sehari, jaga berat badan ideal.',
                'references_text' => "1. Pedoman Pengendalian DM Tipe 2 Kemenkes RI (2022).\n2. Konsensus PERKENI (2023).",
                'last_reviewed' => '10 Oktober 2025'
            ],
            [
                'slug' => 'sakit-kepala-tegang-tension-headache',
                'title' => 'Sakit Kepala Tegang & Migren Ringan',
                'category' => 'ringan',
                'source_label' => 'Konsensus Nasional PERDOSNI',
                'badge_protocol' => 'Manajemen Relaksasi & Hidrasi',
                'image_url' => '',
                'summary' => 'Perbedaan sakit kepala tegang akibat stres/otot leher dengan migren, tata cara kompres, peregangan leher, hidrasi air putih, serta penanda bahaya nyeri kepala akut.',
                'definition' => 'Tension-Type Headache terasa seperti diikat pita kencang di sekeliling kepala. Migren adalah sakit kepala berdenyut berulang yang umumnya terjadi di satu sisi kepala.',
                'symptoms' => 'Nyeri tumpul menekan di dahi atau tengkuk leher, otot bahu kaku kencang. Pada migren: sensasi berdenyut, sensitif cahaya terang atau suara keras.',
                'causes' => 'Paparan layar gawai terlalu lama, postur membungkuk, kurang tidur, dehidrasi, stres emosional, telat makan.',
                'self_care_steps' => "1. Istirahat di Ruangan Redup & Tenang selama 20-30 menit.\n2. Hidrasi Segera: Minum 1-2 gelas air putih suhu ruang.\n3. Pijatan & Peregangan Ringan pada leher dan otot tengkuk.\n4. Kompres Dingin di dahi untuk migren atau kompres hangat di leher untuk otot tegang.\n5. Paracetamol bebas dapat diminum sesuai kemasan. Jangan minum analgesik lebih dari 3 hari berturut-turut untuk cegah sakit kepala rebound.",
                'red_flags' => 'Nyeri kepala sangat hebat mendadak seperti petir (thunderclap headache), sakit kepala disertai demam tinggi dan kaku kuduk, kelemahan separuh wajah/tangan pelo (stroke), atau pasca benturan keras kepala.',
                'when_to_doctor' => 'Periksakan ke dokter jika sakit kepala bertambah berat >3 hari atau frekuensi >2 kali per minggu.',
                'prevention' => 'Aturan 20-20-20 saat menatap layar, postur ergonomis, tidur cukup teratur.',
                'references_text' => "1. Konsensus Nasional Nyeri Kepala PERDOSNI (2023).\n2. Pedoman Praktik Klinis Dokter FKTP Kemenkes RI.",
                'last_reviewed' => '08 Oktober 2025'
            ],
            [
                'slug' => 'luka-bakar-ringan-derajat-1',
                'title' => 'Luka Bakar Ringan (Derajat 1)',
                'category' => 'p3k',
                'source_label' => 'PNPK Trauma Kemenkes & IDI',
                'badge_protocol' => 'Dinginkan Air Mengalir 20 Menit',
                'image_url' => '',
                'summary' => 'Tindakan pendinginan segera dengan air mengalir suhu ruang 15-20 menit. Larangan keras menggunakan pasta gigi, mentega, kecap, atau es batu.',
                'definition' => 'Luka bakar derajat satu (superfisial) hanya merusak epidermis kulit terluar, ditandai kulit kemerahan tanpa lepuhan cairan yang pecah (cipratan minyak goreng, air panas sesaat, sengatan matahari).',
                'symptoms' => 'Kulit merah kering, nyeri perih menyengat, sedikit bengkak, sensitif terhadap sentuhan.',
                'causes' => 'Kontak singkat benda panas, uap air mendidih, cipratan minyak, radiasi matahari berlebih.',
                'self_care_steps' => "1. GUYUR AIR MENGALIR SUHU RUANG: Alirkan air kran bersih suhu ruang (15-25°C) selama 15-20 menit terus menerus.\n2. LEPASKAN AKSESORIS: Lepaskan cincin atau jam tangan sebelum area membengkak.\n3. DILARANG OLESKAN PASTA GIGI, MENTEGA, KECAP: Memerangkap panas dan mengundang infeksi kuman.\n4. DILARANG GUNAKAN ES BATU: Memicu radang beku (frostbite) dan merusak jaringan mikroskopik.\n5. Oleskan petroleum jelly atau gel lidah buaya murni tipis, lalu tutup kassa steril longgar.",
                'red_flags' => 'Luka bakar di wajah, leher, sendi utama, kelamin; luka bakar luas melebihi telapak tangan; kulit putih pucat/hangus mati rasa (derajat 3); atau sengatan listrik tegangan tinggi/kimia pekat.',
                'when_to_doctor' => 'Bawa segera ke IGD jika melepuh gelembung cairan luas (derajat 2 dalam) atau korban adalah bayi/lansia.',
                'prevention' => 'Jauhkan gagang wajan dari tepi kompor, simpan korek api di tempat tinggi tertutup.',
                'references_text' => "1. PNPK Trauma Luka Bakar Kemenkes RI.\n2. ISBI Practice Guidelines.",
                'last_reviewed' => '16 Oktober 2025'
            ],
            [
                'slug' => 'mimisan-epistaksis-anterior',
                'title' => 'Mimisan (Epistaksis Anterior Ringan)',
                'category' => 'p3k',
                'source_label' => 'Panduan THT Kemenkes RI',
                'badge_protocol' => 'Condong ke Depan & Tekan Cuping',
                'image_url' => '',
                'summary' => 'Teknik penanganan mimisan yang benar dengan mencondongkan tubuh ke depan (bukan menengadah) dan menekan cuping hidung 10-15 menit.',
                'definition' => 'Epistaksis anterior adalah perdarahan rongga hidung dari Pleksus Kiesselbach di bagian depan septum hidung, umumnya tidak membahayakan.',
                'symptoms' => 'Darah merah segar mengalir keluar dari salah satu lubang hidung, rasa cairan asin di belakang tenggorokan.',
                'causes' => 'Mengorek hidung terlalu keras, udara sangat kering panas, bersin berlebihan saat alergi/flu, benturan ringan hidung.',
                'self_care_steps' => "1. Duduk Tegak & Condongkan Tubuh ke DEPAN: Jangan pernah menengadah ke belakang karena darah bisa tertelan memicu muntah atau menyumbat jalan napas.\n2. Tekan Cuping Hidung Erat: Jepit bagian lunak hidung dengan ibu jari dan telunjuk selama 10-15 menit tanpa dilepas-lepas.\n3. Bernapas melalui mulut secara tenang.\n4. Kompres Es di batang tulang hidung untuk vasokonstriksi.\n5. Jangan mengorek hidung atau menghembuskan ingus keras selama minimal 12 jam setelah darah berhenti.",
                'red_flags' => 'Darah mengalir terus menerus >20 menit tanpa henti, darah deras mengalir ke tenggorokan (epistaksis posterior), pasien tampak pucat berkeringat dingin pusing lemas, atau akibat benturan keras kepala.',
                'when_to_doctor' => 'Segera bawa ke IGD bila darah tidak berhenti dalam 20 menit atau pasien mengonsumsi obat pengencer darah.',
                'prevention' => 'Oleskan sedikit petroleum jelly di tepi hidung saat udara kering, potong kuku anak berkala.',
                'references_text' => "1. Panduan Tatalaksana Epistaksis Perhati-KL & Kemenkes RI (2023).\n2. First Aid Red Cross.",
                'last_reviewed' => '07 Oktober 2025'
            ],
            [
                'slug' => 'dispepsia-maag-asam-lambung',
                'title' => 'Dispepsia & Maag Ringan',
                'category' => 'ringan',
                'source_label' => 'Pedoman Tata Laksana Dispepsia Kemenkes',
                'badge_protocol' => 'Small Frequent Meals & Hindari Pemicu',
                'image_url' => '',
                'summary' => 'Pengaturan porsi makan kecil tapi sering, pemilahan makanan aman asam lambung, posisi duduk pasca makan, dan anjuran antasida bebas rasional.',
                'definition' => 'Dispepsia adalah kumpulan gejala nyeri atau tidak nyaman pada ulu hati, kembung begah cepat kenyang, atau rasa panas terbakar di dada akibat iritasi asam lambung.',
                'symptoms' => 'Nyeri perih di daerah ulu hati (epigastrium), perut kembung gas, sering bersendawa, mual ringan, rasa panas refluks ke kerongkongan.',
                'causes' => 'Pola makan tidak teratur, makanan terlalu pedas/asam/santan pekat, kopi berkafein, obat pereda nyeri saat perut kosong, stres pikiran.',
                'self_care_steps' => "1. Porsi Kecil tapi Sering (4-5 kali sehari) agar lambung tidak kosong terlalu lama.\n2. Jangan Berbaring Pasca Makan: Beri jeda minimal 2-3 jam sebelum tidur untuk cegah aliran balik asam lambung.\n3. Batasi Makanan Pemicu: Cabai pedas, asam cuka, kopi, alkohol, gorengan berlemak tinggi.\n4. Antasida Bebas kunyah dapat dikonsumsi sementara sesuai petunjuk apoteker.\n5. Kelola stres dengan relaksasi napas dalam.",
                'red_flags' => 'Alarm Symptoms: Muntah berwarna merah darah atau cokelat kehitaman seperti bubuk kopi, BAB hitam lengket aspal (melena), berat badan turun drastis, sulit menelan makanan (disfagia).',
                'when_to_doctor' => 'Segera ke dokter bila timbul alarm symptoms di atas, nyeri ulu hati tembus punggung, atau keluhan tidak membaik setelah 1 minggu.',
                'prevention' => 'Makan teratur tepat waktu, kunyah makanan perlahan, hindari jamu pegal linu tanpa izin BPOM.',
                'references_text' => "1. Konsensus Penatalaksanaan Dispepsia PGI & Kemenkes RI (2024).\n2. British Society of Gastroenterology.",
                'last_reviewed' => '19 Oktober 2025'
            ],
            [
                'slug' => 'patah-tulang-fraktur-imobilisasi',
                'title' => 'Dugaan Patah Tulang & Imobilisasi',
                'category' => 'p3k',
                'source_label' => 'Ditjen Keslan Kemenkes RI & PNPK Trauma',
                'badge_protocol' => 'Prinsip Pembidaian 2 Sendi',
                'image_url' => '',
                'summary' => 'Protokol keselamatan fraktur: larangan keras memijat atau meluruskan tulang bengkok, teknik pembidaian darurat, dan rujukan segera ke IGD.',
                'definition' => 'Fraktur adalah diskontinuitas jaringan tulang akibat trauma benturan langsung atau beban berlebih. Meliputi fraktur tertutup (kulit utuh) atau fraktur terbuka (tulang menonjol).',
                'symptoms' => 'Nyeri hebat menusuk saat disentuh atau digerakkan, bengkak cepat, deformitas bengkok tidak wajar, krepitasi gemeretak tulang, hilangnya fungsi gerak.',
                'causes' => 'Kecelakaan lalu lintas, jatuh dari ketinggian, benturan olahraga kontak keras, osteoporosis lansia.',
                'self_care_steps' => "1. DILARANG MEMIJAT ATAU MENGURUT: Dapat menggeser patahan tulang dan merobek pembuluh darah besar serta saraf.\n2. JANGAN MEMAKSA MELURUSKAN TULANG yang bengkok.\n3. Pembidaian (Imobilisasi) Darurat: Pasang papan datar kaku yang dilapisi kain di sepanjang tulang patah, mencakup DUA SENDI (satu sendi di atas dan satu di bawah patahan). Ikat perban tanpa terlalu kencang.\n4. Kompres es di sekitar luka tertutup untuk kurangi bengkak.\n5. Segera bawa ke IGD Rumah Sakit dengan posisi berbaring tenang.",
                'red_flags' => 'FRAKTUR TERBUKA (tulang menonjol keluar menembus kulit), perdarahan menyembur deras, jari tangan/kaki pucat kebiruan dingin mati rasa, atau kecurigaan cedera leher/tulang belakang.',
                'when_to_doctor' => 'SEMUA DUGAAN FRAKTUR WAJIB SEGERA DIBAWA KE IGD RUMAH SAKIT untuk rontgen X-Ray dan ortopedi.',
                'prevention' => 'Gunakan helm standar SNI saat berkendara, pastikan tangga rumah berpenerangan baik, cukupi kalsium dan vitamin D.',
                'references_text' => "1. Ditjen Pelayanan Kesehatan Lanjutan Kemenkes RI (2024) - P3K Fraktur.\n2. PNPK Trauma Kepmenkes 132/2017.",
                'last_reviewed' => '21 Oktober 2025'
            ],
            [
                'slug' => 'cedera-kepala-ringan-triase-igd',
                'title' => 'Cedera Kepala & Triase Waspada Gegar Otak',
                'category' => 'p3k',
                'source_label' => 'PNPK Cedera Otak Traumatik Kemenkes',
                'badge_protocol' => 'Observasi 24-48 Jam & Red Flags IGD',
                'image_url' => '',
                'summary' => 'Protokol triase ketat benturan kepala: observasi perubahan kesadaran, kompres benjolan hematoma, dan rambu gawat darurat yang mewajibkan ke IGD 119.',
                'definition' => 'Trauma kapitis mencakup benturan mekanik pada kulit kepala, tengkorak, atau jaringan otak, dari memar benjol ringan hingga hematoma intrakranial fatal.',
                'symptoms' => 'Benjolan bengkak nyeri pada kulit kepala, pusing berputar sesaat pasca benturan, lecet permukaan kulit.',
                'causes' => 'Jatuh terpeleset, kepala terbentur pintu/lemari, kecelakaan kendaraan, benturan bola.',
                'self_care_steps' => "1. Kompres Dingin pada Benjolan selama 15 menit dengan es dibalut kain.\n2. Tirah Baring & Hindari Layar ponsel/televisi selama 24 jam pertama.\n3. Observasi Ketat 24-48 Jam: Pasien TIDAK BOLEH ditinggal sendirian. Bangunkan tiap 2-3 jam saat tidur malam untuk pastikan respon sadar penuh.\n4. Analgesik Ringan: Hanya paracetamol bila nyeri kepala ringan. JANGAN memberi obat tidur, aspirin, atau ibuprofen yang tutupi kesadaran atau picu perdarahan.\n5. Dampingi dan pantau ada tidaknya perubahan bicara atau perilaku.",
                'red_flags' => 'TANDA GAWAT DARURAT CEDERA KEPALA (LANGSUNG IGD 119): Riwayat pingsan hilang kesadaran (walau sebentar), muntah menyembur (proyektil) >1 kali, sakit kepala makin hebat tak tertahankan, bicara melantur pelo atau amnesia, pupil mata tidak simetris, keluar darah/cairan bening dari hidung/telinga, kejang.',
                'when_to_doctor' => 'SEGERA HUBUNGI AMBULANS 119 ATAU KE IGD terdekat bila ada SALAH SATU tanda bahaya di atas.',
                'prevention' => 'Kenakan helm standar saat berkendara, pasang pengaman ranjang balita, pegangan tangan kamar mandi lansia.',
                'references_text' => "1. PNPK Cedera Otak Traumatik Kepmenkes 1600/2022.\n2. Permenkes 47/2018 Kegawatdaruratan.",
                'last_reviewed' => '23 Oktober 2025'
            ],
            [
                'slug' => 'pedoman-gizi-seimbang-hidrasi',
                'title' => 'Pedoman Gizi Seimbang & Hidrasi Optimal',
                'category' => 'gizi',
                'source_label' => 'Permenkes No. 41 Tahun 2014',
                'badge_protocol' => 'Isi Piringku & Batasan GGL',
                'image_url' => '',
                'summary' => 'Penerapan panduan piring makan bergizi seimbang Kemenkes RI, aturan pembatasan Gula Garam Lemak (GGL), dan kebutuhan hidrasi 8 gelas air putih per hari.',
                'definition' => 'Gizi seimbang adalah susunan pangan sehari-hari yang mengandung zat gizi dalam jenis dan jumlah sesuai kebutuhan tubuh dengan prinsip keanekaragaman, kebersihan, dan berat badan normal.',
                'symptoms' => 'Dampak gizi tidak seimbang: Badan lemas, konsentrasi menurun, sembelit, sering sakit infeksi, obesitas atau malnutrisi.',
                'causes' => 'Konsumsi makanan olahan cepat saji tinggi kalori kosong, kurang serat sayur buah, jarang minum air putih.',
                'self_care_steps' => "1. Porsi 'Isi Piringku': 1/2 piring berisi sayur (2/3) dan buah (1/3). 1/2 piring lainnya berisi makanan pokok karbohidrat (2/3) dan lauk pauk protein (1/3).\n2. Batasi GGL Harian: Gula maks 4 sendok makan (50g), Garam maks 1 sendok teh (5g atau 2000mg natrium), dan Lemak/Minyak maks 5 sendok makan (67g) per orang per hari.\n3. Penuhi Hidrasi: Minum minimal 8 gelas (setara 2 liter) air putih bersih per hari.\n4. Biasakan sarapan sehat bergizi sebelum jam 9 pagi.\n5. Cuci tangan sabun sebelum memegang makanan.",
                'red_flags' => 'Dehidrasi berat (mata sangat cekung, tidak kencing >8 jam, linglung bingung lemas), atau berat badan turun sangat drastis tanpa diet.',
                'when_to_doctor' => 'Konsultasikan dengan Ahli Gizi / dokter spesialis gizi klinis untuk terapi diet medis penyakit metabolik (diabetes, hipertensi, ginjal).',
                'prevention' => 'Baca label informasi nilai gizi pada kemasan produk, perbanyak makanan segar alami.',
                'references_text' => "1. Permenkes No. 41 Tahun 2014 tentang Pedoman Gizi Seimbang.\n2. Jurnal Gizi Klinik Indonesia (SINTA 2, 2024).",
                'last_reviewed' => '25 Oktober 2025'
            ]
        ];

        foreach ($diseases as $d) {
            Disease::create($d);
        }

        // 3. Load Journals
        $journals = [
            [
                'title' => 'Evidence-Based Conservative Management of Acute Lateral Ankle Sprains: Systematic Review',
                'authors' => 'Bleakley, C. M., Taylor, J. B., Dischiavi, S. L., Doherty, C., Delahunt, E.',
                'year' => 2024,
                'journal_name' => 'British Journal of Sports Medicine (BJSM)',
                'index_rank' => 'Scopus Q1',
                'doi_url' => 'https://doi.org/10.1136/bjsports-2023-107',
                'topic' => 'Cedera Fisik & P3K',
                'license' => 'CC BY 4.0',
                'verified_date' => '18 Oktober 2025',
                'abstract' => 'This systematic review and meta-analysis evaluates the clinical efficacy of early conservative protocols (R.I.C.E. vs P.O.L.I.C.E.) for grade I-II lateral ankle sprains. Early ice application combined with elastic compressive bandaging reduces pain scores and edema in the first 48 hours. Aggressive deep tissue massage is strongly discouraged.',
                'chunks_count' => 14
            ],
            [
                'title' => 'Rasionalitas Penggunaan Antipiretik dan Hidrasi Oral pada Pasien Demam Komunitas Tropis',
                'authors' => 'dr. Maya Indraswari, Sp.A, Prof. Dr. Budiarto, Sp.PD, K-PTI',
                'year' => 2023,
                'journal_name' => 'Jurnal Kedokteran Indonesia (JKI - FKUI)',
                'index_rank' => 'SINTA 2 Terakreditasi',
                'doi_url' => 'https://doi.org/10.23886/ejki.11.234',
                'topic' => 'Penyakit Ringan',
                'license' => 'CC BY 4.0',
                'verified_date' => '12 Oktober 2025',
                'abstract' => 'Uji klinis acak terkontrol pada 180 pasien demam akut di faskes primer. Kompres hangat suam-suam kuku (tepid sponging) terbukti menurunkan suhu inti bertahap tanpa memicu vasokonstriksi kutan. Hidrasi oral adekuat terbukti mencegah dehidrasi ringan pada 92% subjek.',
                'chunks_count' => 9
            ],
            [
                'title' => 'Safety Guardrails in Healthcare Retrieval-Augmented Generation: Triage Sensitivity Benchmarks',
                'authors' => 'Kurniawan, A., Chen, H., Wijaya, S., Thorne, J.',
                'year' => 2024,
                'journal_name' => 'BMC Medical Informatics and Decision Making',
                'index_rank' => 'Scopus Q2',
                'doi_url' => 'https://doi.org/10.1186/s12911-024-02451',
                'topic' => 'AI Medis & Tata Kelola',
                'license' => 'CC BY 4.0',
                'verified_date' => '05 Oktober 2025',
                'abstract' => 'Benchmark for healthcare conversational AI deployed with retrieval-augmented generation. By implementing dual-layer triage checks, the system achieved 100% emergency recall on life-threatening scenarios while eliminating unauthorized pharmacological dosage hallucinations.',
                'chunks_count' => 22
            ],
            [
                'title' => 'Community Acquired Respiratory Tract Infections: Evidence for Self-Care and Timely Referral in Primary Care',
                'authors' => 'dr. Hartono, Sp.P(K), dr. Sarah Melinda, M.Kes',
                'year' => 2023,
                'journal_name' => 'Acta Medica Indonesiana',
                'index_rank' => 'Scopus Q2 / SINTA 1',
                'doi_url' => 'https://doi.org/10.5555/ami.2023.412',
                'topic' => 'Penyakit Ringan',
                'license' => 'CC BY 4.0',
                'verified_date' => '01 Oktober 2025',
                'abstract' => 'Studi kohort pada 340 pasien ISPA non-komplikasi faskes primer. Edukasi hidrasi cairan hangat dan madu terbukti meredakan frekuensi batuk malam hari bermakna (p < 0.01) setara obat OTC. Tidak ditemukan manfaat dari antibiotik empiris pada kasus virus.',
                'chunks_count' => 11
            ],
            [
                'title' => 'Pola Hidrasi dan Kepatuhan Pola Makan Gizi Seimbang Berbasis Permenkes 41/2014 pada Usia Produktif',
                'authors' => 'Prof. Dr. Nurul Hidayati, M.Sc, dr. Dian Lestari, Sp.GK',
                'year' => 2024,
                'journal_name' => 'Jurnal Gizi Klinik Indonesia',
                'index_rank' => 'SINTA 2 Terakreditasi',
                'doi_url' => 'https://doi.org/10.22146/jgki.89201',
                'topic' => 'Gizi & Nutrisi',
                'license' => 'CC BY 4.0',
                'verified_date' => '14 Oktober 2025',
                'abstract' => 'Analisis kepatuhan pilar Isi Piringku terhadap tekanan darah usia produktif. Subjek yang memenuhi hidrasi cairan 2 liter per hari dan membatasi asupan natrium di bawah 2000 mg menunjukkan prevalensi hipertensi 43% lebih rendah dibandingkan kontrol.',
                'chunks_count' => 8
            ],
            [
                'title' => 'Efektivitas Teknik R.I.C.E. Dibandingkan Pemijatan Tradisional pada Penanganan Cedera Ligamen Olahraga Amatir',
                'authors' => 'dr. Bambang Prasetyo, Sp.KO, Ns. Retno Wulandari, M.Kep',
                'year' => 2023,
                'journal_name' => 'Indonesian Journal of Emergency Nursing and Care (IJENC)',
                'index_rank' => 'SINTA 3 Terakreditasi',
                'doi_url' => 'https://doi.org/10.33084/ijenc.2023.78',
                'topic' => 'Cedera Fisik & P3K',
                'license' => 'CC BY-NC 4.0',
                'verified_date' => '19 Oktober 2025',
                'abstract' => 'Studi komparatif 76 kasus cedera pergelangan kaki. Kelompok pijat urut tradisional dalam 24 jam pertama mengalami peningkatan volume hematoma 68% lebih besar dan waktu pemulihan dua kali lebih lama akibat robekan mikroskopik jaringan diperparah manipulasi paksa.',
                'chunks_count' => 10
            ]
        ];

        foreach ($journals as $j) {
            Journal::create($j);
        }

        // 4. Load Source Documents & Build Chunks
        $documents = [
            [
                'title' => 'Pedoman Pertolongan Pertama Saat Kaki Terkilir dan Keseleo',
                'publisher' => 'Ditjen Kesehatan Lanjutan, Kementerian Kesehatan RI',
                'year' => 2024,
                'url' => 'https://keslan.kemkes.go.id/view_artikel/4202/pertolongan-pertama-saat-kaki-terkilir',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-15',
                'status' => 'Terverifikasi',
                'content' => 'Keseleo pergelangan kaki terjadi saat ligamen penstabil sendi meregang melampaui batas normalnya akibat salah pijakan. Protokol utama adalah R.I.C.E.: Rest (istirahatkan segera kaki yang cedera), Ice (kompres es batu yang dibalut kain selama 15-20 menit), Compression (balut tekan menggunakan perban elastis), dan Elevation (posisikan kaki lebih tinggi dari jantung saat berbaring). Dilarang keras melakukan pemijatan atau urut pada 48 jam pertama (prinsip H.A.R.M: Heat, Alcohol, Running, Massage) karena pijatan memicu robekan serat ligamen lebih luas dan meningkatkan perdarahan internal. Bawa ke faskes bila kaki tidak bisa menumpu sama sekali atau sendi berubah bentuk.'
            ],
            [
                'title' => 'Keseleo Ingat R.I.C.E: Panduan Penanganan Mandiri Cedera Sendi',
                'publisher' => 'Ditjen Kesehatan Lanjutan, Kementerian Kesehatan RI',
                'year' => 2024,
                'url' => 'https://keslan.kemkes.go.id/view_artikel/2488/keseleo-ingat-rice',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-15',
                'status' => 'Terverifikasi',
                'content' => 'Empat langkah RICE merupakan standar emas penanganan awal cedera tumpul sendi. Rest mencegah kerusakan bertambah berat. Ice menyempitkan pembuluh darah kapiler yang bocor sehingga bengkak berkurang. Compression memberikan tekanan lembut agar cairan radang tidak menumpuk di rongga sendi. Elevation memanfaatkan gravitasi untuk mengalirkan balik darah kotor dan getah bening menuju jantung. Bila nyeri hebat, obat pereda nyeri bebas paracetamol dapat dikonsumsi dengan anjuran apoteker. Jangan minum obat keras tanpa resep.'
            ],
            [
                'title' => 'Pertolongan Pertama pada Fraktur atau Dugaan Patah Tulang',
                'publisher' => 'Ditjen Kesehatan Lanjutan, Kementerian Kesehatan RI',
                'year' => 2024,
                'url' => 'https://keslan.kemkes.go.id/view_artikel/3757/pertolongan-pertama-pada-fraktur-atau-patah-tulang',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-18',
                'status' => 'Terverifikasi',
                'content' => 'Dugaan patah tulang ditandai dengan nyeri sangat tajam saat disentuh, bengkak cepat, perubahan bentuk (deformitas), dan krepitasi gemeretak saat digerakkan. Tindakan utama adalah imobilisasi total dengan pembidaian (splinting). Gunakan papan datar yang dilapisi kain lembut mencakup dua sendi (sendi di atas dan di bawah patahan). Dilarang keras mencoba meluruskan atau menarik tulang yang bengkok. Pada patah tulang terbuka di mana tulang menonjol keluar kulit, jangan tekan tulang masuk kembali. Tutup dengan kassa steril longgar dan segera hubungi ambulans 119 atau bawa ke IGD rumah sakit.'
            ],
            [
                'title' => 'Kenali, Tangani dengan Tepat, dan Cegah Cedera Olahraga Sejak Dini',
                'publisher' => 'Ditjen Kesehatan Lanjutan, Kementerian Kesehatan RI',
                'year' => 2024,
                'url' => 'https://keslan.kemkes.go.id/view_artikel/4413/kenali-tangani-dengan-tepat-dan-cegah-sejak-dini-cidera-olahraga',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-18',
                'status' => 'Terverifikasi',
                'content' => 'Cedera olahraga meliputi memar (kontusio), terkilir otot (strain), dan robekan ligamen (sprain). Pencegahan terbaik adalah melakukan pemanasan dinamis 10-15 menit dan peregangan statis setelah berolahraga. Saat terjadi memar berdarah di bawah kulit, hindari kompres air panas pada 48 jam pertama. Gunakan es untuk vasokonstriksi. Bila memar bertambah besar secara cepat atau disertai baal/kesemutan pada jari tangan atau kaki, periksakan ke dokter.'
            ],
            [
                'title' => 'Mediakom Edisi 159: Infeksi Saluran Pernapasan Akut (ISPA) dan Perawatan Mandiri',
                'publisher' => 'Biro Komunikasi dan Pelayanan Publik Kemenkes RI',
                'year' => 2023,
                'url' => 'https://kemkes.go.id/app_asset/file_content_download/171090410465fa5328212b43.33390064.pdf',
                'topic' => 'Penyakit Ringan',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-10',
                'status' => 'Terverifikasi',
                'content' => 'ISPA non-komplikasi sebagian besar disebabkan oleh infeksi virus yang bersifat sembuh sendiri (self-limiting). Perawatan mandiri di rumah meliputi hidrasi cairan hangat 2-2.5 liter per hari, istirahat cukup, dan pemberian madu murni untuk meredakan batuk pada anak berusia di atas 1 tahun. Penggunaan antibiotik dilarang keras tanpa resep dokter karena tidak efektif melawan virus dan memicu resistensi antimikroba. Tanda bahaya ISPA yang harus segera ke faskes: napas cepat (>40x/menit pada balita), tarikan dinding dada ke dalam, bibir membiru, atau dahak bercampur darah.'
            ],
            [
                'title' => 'Pedoman Penanganan Termoregulasi dan Demam Akut Komunitas',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia',
                'year' => 2024,
                'url' => 'https://kemkes.go.id/unduhan/demam-termoregulasi-2024.pdf',
                'topic' => 'Penyakit Ringan',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-12',
                'status' => 'Terverifikasi',
                'content' => 'Demam diukur bila suhu ketiak mencapai 37.5°C atau lebih. Langkah penanganan mandiri adalah memberikan kompres air hangat suam-suam kuku (tepid sponging) di area lipatan tubuh seperti ketiak dan leher. Jangan menggunakan air es, alkohol, atau membalut dengan selimut tebal bertumpuk. Cukupi asupan cairan oralit atau air mineral. Paracetamol bebas dapat digunakan bila suhu >38.5°C dengan membaca petunjuk kemasan. Segera ke dokter bila ada tanda bahaya: kejang demam, kaku kuduk, muntah menyembur, bintik merah petekie, atau demam >3 hari.'
            ],
            [
                'title' => 'Pedoman Tatalaksana 5 Pilar LINTAS Diare',
                'publisher' => 'Ditjen P2P, Kementerian Kesehatan Republik Indonesia',
                'year' => 2023,
                'url' => 'https://kemkes.go.id/unduhan/lintas-diare-kemenkes-2023.pdf',
                'topic' => 'Penyakit Ringan',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-14',
                'status' => 'Terverifikasi',
                'content' => '5 Pilar LINTAS Diare Kemenkes RI: 1. Berikan oralit osmolaritas rendah segera setiap kali buang air besar cair; 2. Berikan tablet Zinc selama 10-14 hari berturut-turut untuk memulihkan dinding mukosa usus; 3. Teruskan pemberian ASI dan makanan bernutrisi porsi kecil tapi sering; 4. Berikan antibiotik HANYA atas indikasi ketat dari dokter (misal disentri atau kolera); 5. Berikan edukasi higiene sanitasi dan cuci tangan pakai sabun. Tanda bahaya dehidrasi berat: mata cekung, tidak kencing >6 jam, lemas tidak sadar, atau tinja berdarah.'
            ],
            [
                'title' => 'Pedoman Perawatan Luka Terbuka Ringan dan Pencegahan Tetanus',
                'publisher' => 'Ikatan Dokter Indonesia & Kemenkes RI',
                'year' => 2023,
                'url' => 'https://kemkes.go.id/unduhan/p3k-perawatan-luka.pdf',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-16',
                'status' => 'Terverifikasi',
                'content' => 'Luka lecet dan luka robek dangkal harus segera diirigasi dengan air mengalir bersih atau larutan NaCl 0.9% selama 5-10 menit untuk menghilangkan kotoran dan partikel pasir. Hentikan perdarahan dengan penekanan langsung memakai kassa steril. Oleskan antiseptik povidone-iodine tipis dan tutup dengan balutan kassa steril berpori. Bila luka terkena logam berkarat atau kotoran tanah, periksa riwayat imunisasi tetanus ke faskes untuk suntikan ATS/toxoid. Tanda infeksi: bengkak merah hangat berdenyut dan bernanah.'
            ],
            [
                'title' => 'Pedoman Pencegahan dan Pengendalian Demam Berdarah Dengue (DBD)',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia',
                'year' => 2017,
                'url' => 'https://repository.kemkes.go.id/book/1526',
                'topic' => 'Edukasi Kronis',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-10',
                'status' => 'Terverifikasi',
                'content' => 'DBD memiliki perjalanan khas fase demam tinggi hari 1-3 lalu memasuki fase kritis hari 3-5 saat suhu mendadak turun. Fase kritis adalah fase kebocoran plasma yang rawan syok. Pasien membutuhkan cairan elektrolit oral terus-menerus. Hanya gunakan paracetamol untuk pereda demam; larangan keras menggunakan aspirin atau ibuprofen karena memperparah perdarahan. Tanda bahaya DBD: nyeri perut hebat, muntah terus-menerus, mimisan, gusi berdarah, atau ujung jari dingin dan lembap (syok).'
            ],
            [
                'title' => 'Peraturan Menteri Kesehatan No. 41 Tahun 2014 tentang Pedoman Gizi Seimbang',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia',
                'year' => 2014,
                'url' => 'https://regulasi.bkpk.kemkes.go.id/detail/73b1fc06-f1ef-4c19-b0cd-b8d6072ec7a1/unduh',
                'topic' => 'Gizi & Nutrisi',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Regulasi Terbuka Kemenkes RI',
                'verified_date' => '2025-10-12',
                'status' => 'Terverifikasi',
                'content' => 'Prinsip gizi seimbang tertuang dalam konsep Isi Piringku: separuh piring terdiri dari sayur dan buah, separuh lainnya terdiri dari makanan pokok karbohidrat dan lauk pauk protein. Batasi asupan Gula Garam Lemak (GGL): gula maks 4 sendok makan, garam maks 1 sendok teh (5 gram), dan minyak goreng maks 5 sendok makan per orang per hari. Penuhi kebutuhan hidrasi minimal 2 liter atau 8 gelas air putih per hari untuk memelihara fungsi ginjal dan metabolisme sel.'
            ],
            [
                'title' => 'Pedoman Pengendalian Hipertensi dan Penyakit Kardiovaskular',
                'publisher' => 'Direktorat Pencegahan dan Pengendalian PTM, Kemenkes RI',
                'year' => 2023,
                'url' => 'https://p2ptm.kemkes.go.id/dokumen-ptm/pedoman-hipertensi-2023.pdf',
                'topic' => 'Edukasi Kronis',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-15',
                'status' => 'Terverifikasi',
                'content' => 'Hipertensi didefinisikan sebagai tekanan darah ≥140/90 mmHg. Penanganan mandiri berfokus pada modifikasi gaya hidup: diet rendah garam (maks 1 sendok teh per hari), olahraga aerobik 150 menit per minggu, hindari rokok dan alkohol, serta kelola stres. Krisis hipertensi ditandai tensi >180/120 mmHg disertai sakit kepala parah, penglihatan kabur, atau nyeri dada. Pasien wajib mengonsumsi obat antihipertensi dari dokter secara patuh tanpa menghentikannya sendiri.'
            ],
            [
                'title' => 'Pedoman Pengendalian Diabetes Melitus Tipe 2 Mandiri',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia & PERKENI',
                'year' => 2022,
                'url' => 'https://kemkes.go.id/unduhan/pedoman-diabetes-melitus-2022.pdf',
                'topic' => 'Edukasi Kronis',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Hak Cipta Kemenkes RI / Edukasi Publik',
                'verified_date' => '2025-10-16',
                'status' => 'Terverifikasi',
                'content' => 'Pengelolaan mandiri diabetes berpedoman pada prinsip 3J: Tepat Jadwal, Jumlah, dan Jenis asupan makanan berserat. Waspadai komplikasi darurat hipoglikemia (gula darah <70 mg/dL): gemetar, keringat dingin, jantung berdebar, pusing linglung. Bila mengalami hipoglikemia, segera minum 1 gelas air teh manis atau 2 sendok gula pasir, istirahat 15 menit, lalu cek faskes. Periksa kondisi telapak kaki setiap hari dari kemungkinan lecet atau luka diabetik.'
            ],
            [
                'title' => 'PNPK Tata Laksana Cedera Otak Traumatik (Kepmenkes 1600/2022)',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia',
                'year' => 2022,
                'url' => 'https://keslan.kemkes.go.id/unduhan/fileunduhan_1681539971_246974.pdf',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Kepmenkes Terbuka RI',
                'verified_date' => '2025-10-18',
                'status' => 'Terverifikasi',
                'content' => 'Triase cedera kepala: Benturan kepala ringan dengan benjolan dapat dikompres es dan diobservasi selama 24 jam. Indikator rujukan darurat IGD Rumah Sakit (Red Flags): riwayat pingsan atau hilang kesadaran, muntah proyektil menyembur >1 kali, sakit kepala progresif memburuk, keluarnya cairan bening cerebrospinal atau darah dari telinga/hidung, pupil tidak sama besar, atau bicara pelo dan mengantuk berat yang sulit dibangunkan.'
            ],
            [
                'title' => 'Permenkes No. 47 Tahun 2018 tentang Pelayanan Kegawatdaruratan',
                'publisher' => 'Kementerian Kesehatan Republik Indonesia',
                'year' => 2018,
                'url' => 'https://kemkes.go.id/app_asset/file_content_download/17012291786566b27adad479.88983894.pdf',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 1 (Kemenkes)',
                'license' => 'Regulasi Terbuka Kemenkes RI',
                'verified_date' => '2025-10-20',
                'status' => 'Terverifikasi',
                'content' => 'Kriteria gawat darurat medis yang memerlukan pertolongan segera tanpa hambatan administrasi meliputi: gangguan jalan napas berat, nyeri dada akut curiga infark miokard, henti jantung atau napas, kejang berulang, penurunan kesadaran tiba-tiba, perdarahan hebat, dan cedera multipel trauma mayor. Masyarakat diarahkan menghubungi Sistem Penanggulangan Gawat Darurat Terpadu (SPGDT) nomor 119 atau nomor darurat terpadu 112.'
            ],
            [
                'title' => 'Evidence-Based Conservative Management of Acute Lateral Ankle Sprains',
                'publisher' => 'British Journal of Sports Medicine (BJSM)',
                'year' => 2024,
                'url' => 'https://doi.org/10.1136/bjsports-2023-107',
                'topic' => 'Cedera Fisik & P3K',
                'layer' => 'Lapisan 2 (Jurnal)',
                'license' => 'CC BY 4.0 Open Access',
                'verified_date' => '2025-10-18',
                'status' => 'Terverifikasi',
                'content' => 'Systematic review BJSM 2024: Protokol R.I.C.E. pada cedera ligamen akut terbukti secara ilmiah menurunkan skor nyeri visual analog dan mempercepat reduksi edema lokal pada 48 jam pertama. Evaluasi menunjukkan bahwa manipulasi pijat urut paksa selama fase akut meningkatkan risiko mikrotrauma pembuluh kapiler dan memperlama durasi instabilitas mekanis sendi pergelangan kaki.'
            ],
            [
                'title' => 'Rasionalitas Penggunaan Antipiretik dan Hidrasi Oral pada Pasien Demam Komunitas',
                'publisher' => 'Jurnal Kedokteran Indonesia (JKI - FKUI)',
                'year' => 2023,
                'url' => 'https://doi.org/10.23886/ejki.11.234',
                'topic' => 'Penyakit Ringan',
                'layer' => 'Lapisan 2 (Jurnal)',
                'license' => 'CC BY 4.0 Open Access',
                'verified_date' => '2025-10-12',
                'status' => 'Terverifikasi',
                'content' => 'Uji klinis JKI 2023: Kompres air hangat suam-suam kuku (tepid sponging) memfasilitasi pelepasan panas secara konduksi dan evaporasi tanpa memicu refleks vasokonstriksi kutan yang sering terjadi pada kompres es dingin. Pemberian cairan elektrolit oral secara bertahap efektif mempertahankan volume sirkulasi dan mencegah kebutuhan rehidrasi parenteral pada 92% kasus demam akut tanpa komplikasi.'
            ]
        ];

        foreach ($documents as $docData) {
            $doc = Document::create($docData);

            // Chunk content into ~70 words with overlap
            $words = explode(' ', $docData['content']);
            $chunkSize = 70;
            $overlap = 15;
            $start = 0;
            $totalWords = count($words);

            while ($start < $totalWords) {
                $end = min($start + $chunkSize, $totalWords);
                $chunkWords = array_slice($words, $start, $end - $start);
                $chunkText = implode(' ', $chunkWords);

                Chunk::create([
                    'doc_id' => $doc->id,
                    'judul_dokumen' => $doc->title,
                    'sumber' => $doc->publisher,
                    'url' => $doc->url,
                    'topik' => $doc->topic,
                    'lapisan' => $doc->layer,
                    'lisensi' => $doc->license,
                    'tahun' => $doc->year,
                    'text' => $chunkText,
                ]);

                if ($end >= $totalWords) {
                    break;
                }
                $start += ($chunkSize - $overlap);
            }
        }

        // 5. Index Disease Chunks for comprehensive recall
        foreach ($diseases as $d) {
            Chunk::create([
                'doc_id' => null,
                'judul_dokumen' => $d['title'],
                'sumber' => $d['source_label'],
                'url' => '#/penyakit/' . $d['slug'],
                'topik' => $d['category'],
                'lapisan' => 'Lapisan 1 (Kemenkes)',
                'lisensi' => 'Edukasi MedikaRAG Terverifikasi',
                'tahun' => 2024,
                'text' => "Topik: {$d['title']}. Definisi: {$d['definition']} Gejala klinis: {$d['symptoms']} Penyebab: {$d['causes']}",
            ]);

            Chunk::create([
                'doc_id' => null,
                'judul_dokumen' => $d['title'],
                'sumber' => $d['source_label'],
                'url' => '#/penyakit/' . $d['slug'],
                'topik' => $d['category'],
                'lapisan' => 'Lapisan 1 (Kemenkes)',
                'lisensi' => 'Edukasi MedikaRAG Terverifikasi',
                'tahun' => 2024,
                'text' => "Topik: {$d['title']}. Protokol Penanganan Mandiri di Rumah: {$d['self_care_steps']}",
            ]);

            Chunk::create([
                'doc_id' => null,
                'judul_dokumen' => $d['title'],
                'sumber' => $d['source_label'],
                'url' => '#/penyakit/' . $d['slug'],
                'topik' => $d['category'],
                'lapisan' => 'Lapisan 1 (Kemenkes)',
                'lisensi' => 'Edukasi MedikaRAG Terverifikasi',
                'tahun' => 2024,
                'text' => "Topik: {$d['title']}. Tanda Bahaya (Red Flags): {$d['red_flags']} Kapan Harus ke Dokter / Faskes / IGD: {$d['when_to_doctor']} Pencegahan: {$d['prevention']}",
            ]);
        }

        // 6. Emergency Rules
        $emergencyRules = [
            ['keyword' => 'nyeri dada', 'severity' => 'CRITICAL', 'action' => 'Arahkan ke IGD / Panggil Ambulans 119'],
            ['keyword' => 'sesak napas berat', 'severity' => 'CRITICAL', 'action' => 'Arahkan ke IGD / Panggil Ambulans 119'],
            ['keyword' => 'kejang', 'severity' => 'CRITICAL', 'action' => 'Arahkan ke IGD / Panggil Ambulans 119'],
            ['keyword' => 'pingsan', 'severity' => 'CRITICAL', 'action' => 'Arahkan ke IGD / Posisi Mantap'],
            ['keyword' => 'stroke', 'severity' => 'CRITICAL', 'action' => 'Protokol FAST / IGD 119'],
            ['keyword' => 'wajah mencong', 'severity' => 'CRITICAL', 'action' => 'Protokol FAST / IGD 119'],
            ['keyword' => 'bicara pelo', 'severity' => 'CRITICAL', 'action' => 'Protokol FAST / IGD 119'],
            ['keyword' => 'perdarahan hebat', 'severity' => 'CRITICAL', 'action' => 'Penekanan Langsung / IGD 119'],
            ['keyword' => 'muntah proyektil', 'severity' => 'CRITICAL', 'action' => 'Cedera Kepala Akut / IGD 119'],
            ['keyword' => 'tulang menonjol', 'severity' => 'CRITICAL', 'action' => 'Fraktur Terbuka / IGD 119'],
            ['keyword' => 'keracunan', 'severity' => 'CRITICAL', 'action' => 'Dekontaminasi / IGD 119'],
            ['keyword' => 'pendarahan hamil', 'severity' => 'CRITICAL', 'action' => 'Gawat Darurat Kebidanan / IGD 119'],
            ['keyword' => 'bunuh diri', 'severity' => 'CRITICAL', 'action' => 'Hotline Krisis / IGD 119'],
        ];

        foreach ($emergencyRules as $r) {
            EmergencyRule::updateOrCreate(['keyword' => $r['keyword']], $r);
        }

        // 7. App Configs
        AppConfig::updateOrCreate(['key' => 'fallback_message'], [
            'value' => 'Maaf, informasi terverifikasi untuk pertanyaan spesifik tersebut belum tersedia di basis pengetahuan resmi Kemenkes RI dan jurnal ilmiah terindeks kami. Silakan konsultasikan langsung dengan dokter di fasilitas pelayanan kesehatan terdekat.'
        ]);
        AppConfig::updateOrCreate(['key' => 'min_similarity_threshold'], ['value' => '0.20']);
        AppConfig::updateOrCreate(['key' => 'rag_version'], ['value' => '3.2.0-laravel']);
    }
}
