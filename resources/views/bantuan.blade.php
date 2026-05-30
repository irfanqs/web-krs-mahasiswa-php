@extends('layouts.app')
@section('title', 'Pusat Bantuan')

@push('styles')
<style>
    .bantuan-wrap { max-width: 960px; margin: 0 auto; padding-bottom: 60px; }

    /* Hero */
    .bantuan-hero {
        background: linear-gradient(135deg, #1B3679 0%, #2A4A9E 100%);
        border-radius: 20px; padding: 40px 48px; margin-bottom: 36px;
        display: flex; align-items: center; gap: 32px;
    }
    .bantuan-hero-icon { font-size: 52px; color: #ffffff !important; flex-shrink: 0; }
    .bantuan-hero h1 { font-size: 28px; font-weight: 800; margin: 0 0 8px; color: #ffffff !important; }
    .bantuan-hero p { font-size: 15px; color: rgba(255,255,255,0.88) !important; margin: 0; line-height: 1.6; }
    .bantuan-hero p strong { color: #ffffff !important; }

    /* Search */
    .bantuan-search { position: relative; margin-bottom: 36px; }
    .bantuan-search input {
        width: 100%; padding: 16px 20px 16px 52px; border: 1.5px solid #E5E7EB;
        border-radius: 14px; font-size: 15px; color: #111827; outline: none;
        transition: border-color 0.2s; background: white; box-sizing: border-box;
    }
    .bantuan-search input:focus { border-color: #1B3679; box-shadow: 0 0 0 4px rgba(27,54,121,0.08); }
    .bantuan-search i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #9CA3AF; }

    /* Section */
    .bantuan-section { margin-bottom: 40px; }
    .bantuan-section-title {
        font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        color: #9CA3AF; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .bantuan-section-title::after { content: ''; flex: 1; height: 1px; background: #E5E7EB; }

    /* Guide Cards */
    .guide-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 40px; }
    .guide-card {
        background: white; border-radius: 16px; padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05); cursor: pointer;
        border: 1.5px solid transparent; transition: all 0.2s;
    }
    .guide-card:hover { border-color: #1B3679; box-shadow: 0 4px 16px rgba(27,54,121,0.1); transform: translateY(-2px); }
    .guide-card.active { border-color: #1B3679; background: #EEF2FF; }
    .guide-icon {
        width: 44px; height: 44px; border-radius: 12px; background: #EEF2FF;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #1B3679; margin-bottom: 14px;
    }
    .guide-card h3 { font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 6px; }
    .guide-card p { font-size: 13px; color: #6B7280; margin: 0; line-height: 1.5; }
    .guide-badge { display: inline-block; margin-top: 10px; font-size: 11px; font-weight: 700; background: #DBEAFE; color: #1E40AF; padding: 2px 8px; border-radius: 99px; }

    /* Guide Detail */
    .guide-detail { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 32px; display: none; }
    .guide-detail.show { display: block; }
    .guide-detail h2 { font-size: 20px; font-weight: 800; color: #1B3679; margin: 0 0 6px; }
    .guide-detail .guide-desc { font-size: 14px; color: #6B7280; margin: 0 0 24px; }
    .step-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 14px; }
    .step-item { display: flex; gap: 16px; align-items: flex-start; }
    .step-num {
        width: 32px; height: 32px; border-radius: 50%; background: #1B3679; color: white;
        font-size: 13px; font-weight: 800; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; margin-top: 1px;
    }
    .step-content h4 { font-size: 14px; font-weight: 700; color: #111827; margin: 0 0 4px; }
    .step-content p { font-size: 13px; color: #6B7280; margin: 0; line-height: 1.5; }
    .step-tip {
        display: flex; align-items: flex-start; gap: 10px; background: #FFFBEB;
        border: 1px solid #FDE68A; border-radius: 10px; padding: 12px 14px; margin-top: 20px;
        font-size: 13px; color: #92400E;
    }
    .step-tip i { font-size: 16px; color: #F59E0B; flex-shrink: 0; margin-top: 1px; }

    /* FAQ */
    .faq-list { display: flex; flex-direction: column; gap: 10px; }
    .faq-item { background: white; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.05); overflow: hidden; }
    .faq-q {
        width: 100%; background: none; border: none; text-align: left; padding: 18px 20px;
        font-size: 14px; font-weight: 700; color: #111827; cursor: pointer;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .faq-q:hover { background: #F9FAFB; }
    .faq-q i.chevron { font-size: 16px; color: #9CA3AF; transition: transform 0.25s; flex-shrink: 0; }
    .faq-item.open .faq-q i.chevron { transform: rotate(180deg); }
    .faq-a {
        padding: 0 20px; max-height: 0; overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
        font-size: 13px; color: #4B5563; line-height: 1.7;
    }
    .faq-item.open .faq-a { max-height: 500px; padding: 0 20px 18px; }

    /* Contact */
    .contact-card {
        background: linear-gradient(135deg, #F0F4FF 0%, #E8EEFF 100%);
        border-radius: 16px; padding: 28px 32px; display: flex; align-items: center;
        justify-content: space-between; gap: 24px; border: 1px solid #C7D2FE;
    }
    .contact-card h3 { font-size: 16px; font-weight: 800; color: #1B3679; margin: 0 0 6px; }
    .contact-card p { font-size: 13px; color: #4B5563; margin: 0; }
    .btn-contact {
        background: #1B3679; color: white; border: none; border-radius: 10px;
        padding: 12px 24px; font-size: 13px; font-weight: 700; cursor: pointer;
        white-space: nowrap; display: flex; align-items: center; gap: 8px;
    }
</style>
@endpush

@section('content')
<div class="bantuan-wrap">

    {{-- Hero --}}
    <div class="bantuan-hero">
        <i class="bi bi-headset bantuan-hero-icon"></i>
        <div>
            <h1>Pusat Bantuan SIAKAD Gallery</h1>
            <p>Temukan panduan penggunaan, jawaban pertanyaan umum, dan cara menghubungi tim akademik.<br>
            @if($role === 'mahasiswa') Panduan ini dirancang khusus untuk <strong>Mahasiswa</strong>.
            @elseif($role === 'dosen') Panduan ini dirancang khusus untuk <strong>Dosen</strong>.
            @else Panduan ini dirancang khusus untuk <strong>Administrator</strong>.
            @endif
            </p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bantuan-search">
        <i class="bi bi-search"></i>
        <input type="text" id="search-input" placeholder="Cari panduan atau pertanyaan..." oninput="filterFaq(this.value)">
    </div>

    {{-- ── MAHASISWA ── --}}
    @if($role === 'mahasiswa')

    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-map"></i> Panduan Penggunaan</div>
        <div class="guide-grid">
            <div class="guide-card" onclick="showGuide('krs')">
                <div class="guide-icon"><i class="bi bi-calendar-check"></i></div>
                <h3>Pengisian KRS</h3>
                <p>Cara memilih dan mendaftarkan mata kuliah untuk semester aktif.</p>
                <span class="guide-badge">5 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('khs')">
                <div class="guide-icon"><i class="bi bi-bar-chart-line"></i></div>
                <h3>Melihat Hasil Studi</h3>
                <p>Cara melihat KHS, nilai per semester, dan riwayat akademik.</p>
                <span class="guide-badge">3 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('jadwal')">
                <div class="guide-icon"><i class="bi bi-calendar3"></i></div>
                <h3>Jadwal Kuliah</h3>
                <p>Cara melihat jadwal mingguan dan informasi dosen pengampu.</p>
                <span class="guide-badge">2 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('profil')">
                <div class="guide-icon"><i class="bi bi-person-circle"></i></div>
                <h3>Profil Akademik</h3>
                <p>Cara melihat data diri, performa semester, dan cetak transkrip.</p>
                <span class="guide-badge">2 langkah</span>
            </div>
        </div>

        {{-- Guide: KRS --}}
        <div class="guide-detail" id="guide-krs">
            <h2><i class="bi bi-calendar-check" style="color:#1B3679;margin-right:8px;"></i>Cara Pengisian KRS</h2>
            <p class="guide-desc">Pengisian KRS dilakukan setiap awal semester pada periode yang ditentukan. Berikut langkah-langkahnya:</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Pengisian KRS</h4><p>Klik <strong>Pengisian KRS</strong> di sidebar kiri. Halaman menampilkan semua mata kuliah yang tersedia di semester aktif.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Perhatikan batas SKS Anda</h4><p>Di bagian atas halaman terdapat informasi <strong>IPK</strong> dan <strong>batas maksimal SKS</strong> yang bisa diambil berdasarkan IPK semester lalu.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Pilih mata kuliah</h4><p>Klik kartu mata kuliah yang ingin diambil. Panel pendaftaran akan muncul. Klik tombol <strong>"Daftar"</strong> untuk mendaftarkan diri.</p></div></li>
                <li class="step-item"><div class="step-num">4</div><div class="step-content"><h4>Perhatikan aturan semester</h4><p>Mata kuliah wajib dengan label <strong>🔒 Terkunci</strong> berarti belum bisa diambil karena masih di atas semester Anda saat ini. Mata kuliah pilihan bebas diambil kapan saja.</p></div></li>
                <li class="step-item"><div class="step-num">5</div><div class="step-content"><h4>Batalkan jika perlu</h4><p>Jika ingin membatalkan, klik kembali kartu mata kuliah yang sudah terdaftar lalu klik <strong>"Batalkan"</strong>.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Sistem otomatis mencegah jadwal bentrok dan total SKS melebihi batas. Jika mendapat error saat mendaftar, baca pesan yang muncul untuk mengetahui penyebabnya.</span></div>
        </div>

        {{-- Guide: KHS --}}
        <div class="guide-detail" id="guide-khs">
            <h2><i class="bi bi-bar-chart-line" style="color:#1B3679;margin-right:8px;"></i>Cara Melihat Hasil Studi (KHS)</h2>
            <p class="guide-desc">KHS menampilkan nilai akhir per semester yang sudah dikunci oleh dosen.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Hasil Studi</h4><p>Klik <strong>Hasil Studi</strong> di sidebar. Secara default, sistem menampilkan nilai semester aktif.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Pilih semester</h4><p>Gunakan dropdown <strong>Pilih Semester</strong> di bagian atas untuk melihat nilai semester lainnya.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Lihat riwayat semua semester</h4><p>Gulir ke bawah ke bagian <strong>"Riwayat KHS Seluruh Semester"</strong>. Klik baris semester untuk memperluas detail nilai.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Nilai hanya tampil setelah dosen mengunci (finalisasi) penilaian. Jika nilai belum muncul, hubungi dosen pengampu atau bagian akademik.</span></div>
        </div>

        {{-- Guide: Jadwal --}}
        <div class="guide-detail" id="guide-jadwal">
            <h2><i class="bi bi-calendar3" style="color:#1B3679;margin-right:8px;"></i>Cara Melihat Jadwal Kuliah</h2>
            <p class="guide-desc">Jadwal mingguan menampilkan seluruh mata kuliah yang sudah Anda daftarkan di KRS.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Jadwal</h4><p>Klik <strong>Jadwal</strong> di sidebar. Halaman menampilkan grid jadwal Senin–Sabtu dengan detail ruangan dan dosen.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Cek jadwal hari ini</h4><p>Sidebar kanan menampilkan <strong>Dosen Hari Ini</strong> untuk memudahkan Anda mengetahui jadwal di hari berjalan.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Jadwal hanya akan muncul setelah Anda berhasil mendaftarkan mata kuliah di KRS. Pastikan KRS sudah diisi terlebih dahulu.</span></div>
        </div>

        {{-- Guide: Profil --}}
        <div class="guide-detail" id="guide-profil">
            <h2><i class="bi bi-person-circle" style="color:#1B3679;margin-right:8px;"></i>Cara Melihat Profil Akademik</h2>
            <p class="guide-desc">Halaman profil memuat data diri, status akademik, dan rekap performa per semester.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Profil Akademik</h4><p>Klik <strong>Profil Akademik</strong> di sidebar untuk melihat data lengkap Anda.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Cetak transkrip</h4><p>Klik tombol <strong>"Cetak Transkrip"</strong> di pojok kanan atas untuk mencetak atau menyimpan sebagai PDF melalui dialog print browser.</p></div></li>
            </ol>
        </div>
    </div>

    {{-- FAQ Mahasiswa --}}
    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-chat-left-quote"></i> Pertanyaan Umum (FAQ)</div>
        <div class="faq-list" id="faq-list">
            @php $faqs = [
                ['q'=>'Berapa batas SKS yang bisa saya ambil?','a'=>'Batas SKS ditentukan berdasarkan IPK semester lalu: IPK ≥ 3.50 = 24 SKS, IPK 3.00–3.49 = 22 SKS, IPK 2.50–2.99 = 20 SKS, IPK 2.00–2.49 = 18 SKS, IPK < 2.00 = 15 SKS. Jika belum punya IPK (semester pertama), batas default adalah 24 SKS.'],
                ['q'=>'Mengapa ada mata kuliah yang terkunci (🔒)?','a'=>'Mata kuliah wajib hanya bisa diambil sesuai urutan semester. Jika semester Anda belum mencapai semester mata kuliah tersebut, tombol daftar akan dikunci. Mata kuliah pilihan bebas diambil kapan saja tanpa batasan semester.'],
                ['q'=>'Nilai saya belum muncul di KHS, kenapa?','a'=>'Nilai hanya ditampilkan setelah dosen pengampu melakukan finalisasi (mengunci nilai). Jika semester sudah berakhir namun nilai belum muncul, hubungi dosen atau bagian administrasi akademik.'],
                ['q'=>'Bisakah saya mengubah KRS yang sudah disimpan?','a'=>'Ya. Buka halaman Pengisian KRS, lalu klik mata kuliah yang ingin dibatalkan dan tekan tombol "Batalkan". Kemudian daftarkan ulang mata kuliah yang diinginkan. Perubahan bisa dilakukan selama periode pengisian KRS masih dibuka.'],
                ['q'=>'Mengapa jadwal saya tidak muncul di halaman Jadwal?','a'=>'Jadwal hanya tampil untuk mata kuliah yang sudah berhasil didaftarkan di KRS. Pastikan proses pendaftaran berhasil (tidak ada pesan error) dan coba muat ulang halaman Jadwal.'],
                ['q'=>'Apa arti status akademik di KHS?','a'=>'"AKTIF / MEMUASKAN" berarti IPS ≥ 3.0. "AKTIF" berarti IPS antara 2.0–2.99. "AKTIF / PERLU PERHATIAN" berarti IPS di bawah 2.0 dan disarankan untuk konsultasi dengan pembimbing akademik. "BELUM ADA NILAI" berarti belum ada nilai yang dikunci di semester tersebut.'],
            ]; @endphp
            @foreach($faqs as $f)
            <div class="faq-item" data-q="{{ strtolower($f['q']) }}">
                <button class="faq-q" onclick="toggleFaq(this)">
                    <span>{{ $f['q'] }}</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </button>
                <div class="faq-a">{{ $f['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── DOSEN ── --}}
    @elseif($role === 'dosen')

    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-map"></i> Panduan Penggunaan</div>
        <div class="guide-grid">
            <div class="guide-card" onclick="showGuide('input-nilai')">
                <div class="guide-icon"><i class="bi bi-pencil-square"></i></div>
                <h3>Input Nilai Mahasiswa</h3>
                <p>Cara memasukkan nilai tugas, UTS, UAS, dan mengunci nilai akhir.</p>
                <span class="guide-badge">6 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('daftar-mhs')">
                <div class="guide-icon"><i class="bi bi-people"></i></div>
                <h3>Daftar Mahasiswa Kelas</h3>
                <p>Cara melihat daftar mahasiswa yang terdaftar di setiap kelas.</p>
                <span class="guide-badge">2 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('jadwal-dosen')">
                <div class="guide-icon"><i class="bi bi-calendar3"></i></div>
                <h3>Jadwal Mengajar</h3>
                <p>Cara melihat jadwal mengajar dan informasi kelas yang diampu.</p>
                <span class="guide-badge">2 langkah</span>
            </div>
        </div>

        {{-- Guide: Input Nilai --}}
        <div class="guide-detail" id="guide-input-nilai">
            <h2><i class="bi bi-pencil-square" style="color:#1B3679;margin-right:8px;"></i>Cara Input Nilai Mahasiswa</h2>
            <p class="guide-desc">Input nilai dilakukan per kelas. Nilai akhir dihitung otomatis dengan bobot 20% Tugas, 30% UTS, 50% UAS.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Input Nilai</h4><p>Klik <strong>Input Nilai</strong> di sidebar kiri untuk membuka halaman penilaian.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Pilih kelas</h4><p>Gunakan dropdown <strong>"Pilih Kelas"</strong> di bagian atas untuk memilih mata kuliah dan kelas yang akan dinilai.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Isi nilai di tabel</h4><p>Klik kolom <strong>Tugas</strong>, <strong>UTS</strong>, atau <strong>UAS</strong> pada baris mahasiswa yang bersangkutan. Nilai valid antara 0–100.</p></div></li>
                <li class="step-item"><div class="step-num">4</div><div class="step-content"><h4>Nilai akhir otomatis terhitung</h4><p>Kolom <strong>Nilai Akhir</strong> dan <strong>Huruf</strong> akan otomatis terisi saat Anda mengisi ketiga komponen penilaian.</p></div></li>
                <li class="step-item"><div class="step-num">5</div><div class="step-content"><h4>Klik "Simpan Perubahan"</h4><p>Setelah selesai mengisi semua nilai, klik tombol <strong>"Simpan Perubahan"</strong> di bagian bawah halaman. Notifikasi akan muncul jika berhasil disimpan.</p></div></li>
                <li class="step-item"><div class="step-num">6</div><div class="step-content"><h4>Kunci nilai (finalisasi)</h4><p>Setelah seluruh nilai sudah benar dan final, hubungi admin untuk mengunci nilai. Nilai yang sudah dikunci tidak bisa diubah dan akan tampil di KHS mahasiswa.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Jika kolom nilai berwarna merah, berarti nilai yang dimasukkan di luar rentang 0–100. Perbaiki sebelum menyimpan.</span></div>
        </div>

        {{-- Guide: Daftar Mhs --}}
        <div class="guide-detail" id="guide-daftar-mhs">
            <h2><i class="bi bi-people" style="color:#1B3679;margin-right:8px;"></i>Cara Melihat Daftar Mahasiswa</h2>
            <p class="guide-desc">Lihat siapa saja mahasiswa yang terdaftar di kelas yang Anda ampu.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Daftar Mahasiswa</h4><p>Klik <strong>Daftar Mahasiswa</strong> di sidebar. Daftar semua kelas yang Anda ampu akan ditampilkan.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Pilih kelas</h4><p>Gunakan dropdown untuk memilih kelas tertentu. Tabel akan menampilkan NIM, nama, dan status nilai mahasiswa.</p></div></li>
            </ol>
        </div>

        {{-- Guide: Jadwal Dosen --}}
        <div class="guide-detail" id="guide-jadwal-dosen">
            <h2><i class="bi bi-calendar3" style="color:#1B3679;margin-right:8px;"></i>Cara Melihat Jadwal Mengajar</h2>
            <p class="guide-desc">Halaman jadwal menampilkan seluruh sesi mengajar Anda di semester aktif.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka menu Jadwal</h4><p>Klik <strong>Jadwal</strong> di sidebar untuk melihat jadwal mengajar mingguan Anda.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Cek jadwal hari ini</h4><p>Dashboard juga menampilkan <strong>"Jadwal Mengajar Hari Ini"</strong> langsung di halaman utama untuk akses cepat.</p></div></li>
            </ol>
        </div>
    </div>

    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-chat-left-quote"></i> Pertanyaan Umum (FAQ)</div>
        <div class="faq-list" id="faq-list">
            @php $faqs = [
                ['q'=>'Bagaimana cara menghitung nilai akhir mahasiswa?','a'=>'Nilai akhir dihitung otomatis: Nilai Akhir = (Tugas × 20%) + (UTS × 30%) + (UAS × 50%). Konversi ke huruf: ≥85 → A, ≥70 → B+, ≥60 → B, ≥55 → C+, ≥50 → C, ≥40 → D, <40 → E.'],
                ['q'=>'Apakah nilai bisa diubah setelah disimpan?','a'=>'Ya, nilai bisa diubah selama belum dikunci (status_kunci = 0). Setelah dikunci oleh admin, nilai bersifat final dan tidak bisa diedit lagi.'],
                ['q'=>'Mengapa beberapa baris di tabel nilai tidak bisa diedit?','a'=>'Baris yang tidak bisa diedit berarti nilai mahasiswa tersebut sudah dikunci (finalisasi). Hubungi admin jika ada kesalahan pada nilai yang sudah dikunci.'],
                ['q'=>'Kapan nilai akan muncul di KHS mahasiswa?','a'=>'Nilai akan tampil di KHS mahasiswa setelah admin atau Anda melakukan finalisasi (penguncian nilai). Sebelum dikunci, nilai hanya terlihat di halaman input nilai Anda.'],
                ['q'=>'Bagaimana jika ada mahasiswa yang tidak muncul di daftar?','a'=>'Mahasiswa hanya muncul jika sudah mendaftarkan kelas Anda di KRS mereka. Jika ada mahasiswa yang seharusnya ada tapi tidak muncul, informasikan ke bagian administrasi akademik.'],
            ]; @endphp
            @foreach($faqs as $f)
            <div class="faq-item" data-q="{{ strtolower($f['q']) }}">
                <button class="faq-q" onclick="toggleFaq(this)">
                    <span>{{ $f['q'] }}</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </button>
                <div class="faq-a">{{ $f['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── ADMIN ── --}}
    @else

    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-map"></i> Panduan Penggunaan</div>
        <div class="guide-grid">
            <div class="guide-card" onclick="showGuide('kelola-mhs')">
                <div class="guide-icon"><i class="bi bi-person-badge"></i></div>
                <h3>Manajemen Mahasiswa</h3>
                <p>Cara menambah, mengedit, dan menghapus data mahasiswa.</p>
                <span class="guide-badge">4 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('kelola-dosen')">
                <div class="guide-icon"><i class="bi bi-person-video3"></i></div>
                <h3>Manajemen Dosen</h3>
                <p>Cara mengelola data dosen dan penugasan mata kuliah.</p>
                <span class="guide-badge">3 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('kelola-jadwal')">
                <div class="guide-icon"><i class="bi bi-calendar3"></i></div>
                <h3>Manajemen Jadwal</h3>
                <p>Cara membuat dan mengatur jadwal kuliah per semester.</p>
                <span class="guide-badge">4 langkah</span>
            </div>
            <div class="guide-card" onclick="showGuide('kelola-semester')">
                <div class="guide-icon"><i class="bi bi-calendar2-check"></i></div>
                <h3>Manajemen Semester</h3>
                <p>Cara mengaktifkan semester dan mengatur periode akademik.</p>
                <span class="guide-badge">3 langkah</span>
            </div>
        </div>

        {{-- Guide: Kelola Mhs --}}
        <div class="guide-detail" id="guide-kelola-mhs">
            <h2><i class="bi bi-person-badge" style="color:#1B3679;margin-right:8px;"></i>Cara Manajemen Mahasiswa</h2>
            <p class="guide-desc">Kelola seluruh data mahasiswa aktif, cuti, maupun lulus.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka Manajemen Mahasiswa</h4><p>Klik <strong>Manajemen Mahasiswa</strong> di sidebar. Halaman menampilkan tabel seluruh mahasiswa beserta status dan angkatan.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Tambah mahasiswa baru</h4><p>Klik tombol <strong>"+ Tambah Mahasiswa"</strong> di kanan atas. Isi NIM, nama, email, program studi, angkatan, dan password. Klik Simpan.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Edit data mahasiswa</h4><p>Klik ikon <strong>pensil (edit)</strong> pada baris mahasiswa yang ingin diubah. Perbarui data yang diperlukan lalu simpan.</p></div></li>
                <li class="step-item"><div class="step-num">4</div><div class="step-content"><h4>Hapus mahasiswa</h4><p>Klik ikon <strong>tempat sampah (hapus)</strong> dan konfirmasi penghapusan. Data yang dihapus bersifat permanen.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>NIM bersifat unik dan tidak bisa diubah setelah disimpan. Pastikan NIM benar sebelum menyimpan data mahasiswa baru.</span></div>
        </div>

        {{-- Guide: Kelola Dosen --}}
        <div class="guide-detail" id="guide-kelola-dosen">
            <h2><i class="bi bi-person-video3" style="color:#1B3679;margin-right:8px;"></i>Cara Manajemen Dosen</h2>
            <p class="guide-desc">Kelola data dosen yang mengajar di institusi.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka Manajemen Dosen</h4><p>Klik <strong>Manajemen Dosen</strong> di sidebar untuk melihat daftar semua dosen.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Tambah dosen baru</h4><p>Klik <strong>"+ Tambah Dosen"</strong>. Isi NIDN, nama, email, jurusan, dan password. NIDN akan digunakan sebagai username login dosen.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Edit atau hapus dosen</h4><p>Gunakan tombol edit/hapus di kolom Aksi pada tabel dosen.</p></div></li>
            </ol>
        </div>

        {{-- Guide: Kelola Jadwal --}}
        <div class="guide-detail" id="guide-kelola-jadwal">
            <h2><i class="bi bi-calendar3" style="color:#1B3679;margin-right:8px;"></i>Cara Manajemen Jadwal Kuliah</h2>
            <p class="guide-desc">Buat jadwal kuliah dengan menghubungkan mata kuliah, dosen, ruang, dan waktu.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Pastikan semester aktif sudah diset</h4><p>Jadwal hanya bisa dibuat untuk semester yang sudah ada. Aktifkan semester terlebih dahulu di menu <strong>Manajemen Semester</strong>.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Buka Manajemen Jadwal</h4><p>Klik <strong>Manajemen Jadwal</strong> di sidebar lalu klik <strong>"+ Tambah Jadwal"</strong>.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Isi detail jadwal</h4><p>Pilih <strong>Mata Kuliah</strong>, <strong>Dosen</strong>, <strong>Semester</strong>, <strong>Hari</strong>, <strong>Jam Mulai</strong>, <strong>Jam Selesai</strong>, <strong>Ruang</strong>, dan <strong>Kuota</strong> kelas.</p></div></li>
                <li class="step-item"><div class="step-num">4</div><div class="step-content"><h4>Simpan jadwal</h4><p>Klik Simpan. Jadwal yang baru akan langsung tersedia untuk dipilih mahasiswa di halaman KRS.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Pastikan tidak ada jadwal yang bentrok (dosen, ruangan, atau waktu yang sama). Sistem tidak memvalidasi bentrok secara otomatis di sisi admin.</span></div>
        </div>

        {{-- Guide: Kelola Semester --}}
        <div class="guide-detail" id="guide-kelola-semester">
            <h2><i class="bi bi-calendar2-check" style="color:#1B3679;margin-right:8px;"></i>Cara Manajemen Semester</h2>
            <p class="guide-desc">Atur periode akademik dan pastikan hanya satu semester yang aktif pada satu waktu.</p>
            <ol class="step-list">
                <li class="step-item"><div class="step-num">1</div><div class="step-content"><h4>Buka Manajemen Semester</h4><p>Klik <strong>Manajemen Semester</strong> di sidebar untuk melihat semua semester yang tersedia.</p></div></li>
                <li class="step-item"><div class="step-num">2</div><div class="step-content"><h4>Tambah semester baru</h4><p>Klik <strong>"+ Tambah Semester"</strong>. Isi tahun ajaran (mis. 2025/2026), tingkatan (Ganjil/Genap), dan status.</p></div></li>
                <li class="step-item"><div class="step-num">3</div><div class="step-content"><h4>Aktifkan semester</h4><p>Edit semester yang ingin diaktifkan dan ubah statusnya menjadi <strong>"Aktif"</strong>. Sistem hanya mengizinkan satu semester aktif. Semester sebelumnya otomatis menjadi nonaktif.</p></div></li>
            </ol>
            <div class="step-tip"><i class="bi bi-lightbulb-fill"></i><span>Semester aktif menentukan jadwal yang muncul di KRS mahasiswa dan halaman input nilai dosen. Pastikan semester yang benar sudah diaktifkan sebelum periode pengisian KRS dimulai.</span></div>
        </div>
    </div>

    <div class="bantuan-section">
        <div class="bantuan-section-title"><i class="bi bi-chat-left-quote"></i> Pertanyaan Umum (FAQ)</div>
        <div class="faq-list" id="faq-list">
            @php $faqs = [
                ['q'=>'Bisakah ada lebih dari satu semester aktif?','a'=>'Tidak. Sistem hanya mengizinkan satu semester berstatus aktif pada satu waktu. Saat Anda mengaktifkan semester baru, semester yang sebelumnya aktif otomatis dinonaktifkan.'],
                ['q'=>'Apa yang terjadi jika mahasiswa dihapus?','a'=>'Data mahasiswa beserta KRS dan nilai yang terkait akan ikut terhapus secara permanen. Lakukan penghapusan dengan hati-hati dan pastikan sudah ada backup data.'],
                ['q'=>'Bagaimana cara mengunci nilai mahasiswa?','a'=>'Buka tabel nilai di halaman yang relevan dan ubah status_kunci menjadi 1 (aktif) melalui fitur manajemen data. Setelah dikunci, dosen tidak bisa lagi mengubah nilai tersebut.'],
                ['q'=>'Apakah bisa menambahkan mata kuliah baru di tengah semester?','a'=>'Secara teknis bisa melalui menu Manajemen Mata Kuliah, namun disarankan untuk menambah mata kuliah dan jadwalnya sebelum periode KRS dibuka agar mahasiswa bisa mendaftar dengan benar.'],
                ['q'=>'Bagaimana cara mereset password mahasiswa atau dosen?','a'=>'Buka menu Manajemen Mahasiswa atau Manajemen Dosen, klik Edit pada data yang bersangkutan, lalu isi kolom password baru. Password akan tersimpan dalam bentuk terenkripsi (bcrypt).'],
            ]; @endphp
            @foreach($faqs as $f)
            <div class="faq-item" data-q="{{ strtolower($f['q']) }}">
                <button class="faq-q" onclick="toggleFaq(this)">
                    <span>{{ $f['q'] }}</span>
                    <i class="bi bi-chevron-down chevron"></i>
                </button>
                <div class="faq-a">{{ $f['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    @endif

    {{-- Kontak Bantuan --}}
    <div class="contact-card">
        <div>
            <h3>Masih butuh bantuan?</h3>
            <p>Hubungi tim administrasi akademik jika pertanyaan Anda tidak terjawab di sini.<br>
            Email: <strong>akademik@siakad-gallery.ac.id</strong> &bull; Telepon: <strong>(021) 1234-5678</strong></p>
        </div>
        <button class="btn-contact"><i class="bi bi-envelope"></i> Hubungi Admin</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
    }

    function showGuide(id) {
        document.querySelectorAll('.guide-detail').forEach(d => d.classList.remove('show'));
        document.querySelectorAll('.guide-card').forEach(c => c.classList.remove('active'));
        const detail = document.getElementById('guide-' + id);
        if (detail) {
            detail.classList.add('show');
            detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        event.currentTarget.classList.add('active');
    }

    function filterFaq(q) {
        const term = q.toLowerCase();
        document.querySelectorAll('.faq-item').forEach(item => {
            const text = item.getAttribute('data-q') + ' ' + item.querySelector('.faq-a').textContent.toLowerCase();
            item.style.display = text.includes(term) ? '' : 'none';
        });
    }
</script>
@endpush
