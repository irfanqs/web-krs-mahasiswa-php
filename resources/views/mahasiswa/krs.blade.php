@extends('layouts.app')
@section('title', 'Course Enrollment')

@push('styles')
<style>
    .krs-wrap { max-width: 1300px; margin: 0 auto; padding-bottom: 40px; }

    /* Header */
    .page-header { margin-bottom: 28px; }
    .page-title { font-size: 30px; font-weight: 800; color: #1B3679; margin: 0 0 6px; }
    .page-sub { font-size: 14px; color: #6B7280; margin: 0; }

    /* Stats Bar */
    .stats-bar {
        display: flex; gap: 12px; margin-bottom: 28px; flex-wrap: wrap;
    }
    .stat-chip {
        background: white; border-radius: 12px; padding: 14px 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04); min-width: 110px;
    }
    .stat-chip-navy { background: #1B3679; color: white; }
    .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9CA3AF; margin-bottom: 4px; }
    .stat-chip-navy .stat-label { color: rgba(255,255,255,0.7); }
    .stat-val { font-size: 26px; font-weight: 800; color: #1B3679; display: flex; align-items: baseline; gap: 4px; }
    .stat-chip-navy .stat-val { color: white; }
    .stat-val small { font-size: 12px; font-weight: 600; color: #9CA3AF; }
    .stat-chip-navy .stat-val small { color: rgba(255,255,255,0.6); }

    /* Layout */
    .krs-layout { display: grid; grid-template-columns: 260px 1fr; gap: 28px; }

    /* Sidebar */
    .sidebar-panel { display: flex; flex-direction: column; gap: 16px; }
    .panel-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .panel-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #9CA3AF; margin-bottom: 14px; }

    /* Search */
    .search-box { position: relative; }
    .search-input {
        width: 100%; padding: 11px 12px; border: 1.5px solid #E5E7EB; border-radius: 10px;
        font-size: 13px; color: #111827; background: white; outline: none; box-sizing: border-box;
        transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #1B3679; }
    .search-input::placeholder { color: #C4C9D4; }
    .btn-search-full {
        width: 100%; margin-top: 10px; padding: 10px; background: #1B3679; color: white;
        border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background 0.2s;
    }
    .btn-search-full:hover { background: #0B1E4F; }

    /* Category list */
    .cat-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px; }
    .cat-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 8px 12px; border-radius: 8px; cursor: pointer; font-size: 13px;
        font-weight: 600; color: #374151; transition: all 0.15s;
    }
    .cat-item:hover, .cat-item.active { background: #EEF2FF; color: #1B3679; }
    .cat-item.active { font-weight: 700; }
    .cat-badge { background: #F3F4F6; color: #6B7280; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 99px; }
    .cat-item.active .cat-badge { background: #DBEAFE; color: #1B3679; }

    /* Enrollment status card */
    .enroll-status-card { background: linear-gradient(135deg, #1B3679, #0B1E4F); color: white; border-radius: 16px; padding: 20px; }
    .es-label { font-size: 10px; font-weight: 700; letter-spacing: 0.8px; color: rgba(255,255,255,0.6); text-transform: uppercase; margin-bottom: 8px; }
    .es-phase { font-size: 18px; font-weight: 800; margin-bottom: 14px; }
    .es-bar { height: 5px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin-bottom: 10px; }
    .es-bar-fill { height: 100%; background: white; border-radius: 99px; width: 60%; }
    .es-footer { font-size: 11px; color: rgba(255,255,255,0.6); }

    /* Main content */
    .main-panel {}
    .toolbar {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; gap: 12px;
    }
    .result-info { font-size: 13px; color: #6B7280; font-weight: 600; }
    .result-info strong { color: #111827; }

    /* Course Cards Grid */
    .courses-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

    /* Course Card */
    .course-card {
        background: white; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .course-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
    .cc-banner {
        height: 120px; display: flex; align-items: center; justify-content: flex-end;
        padding: 12px; position: relative; overflow: hidden;
    }
    .cc-banner::before {
        content: ''; position: absolute; inset: 0;
        background: repeating-linear-gradient(45deg, rgba(255,255,255,0.05) 0, rgba(255,255,255,0.05) 10px, transparent 10px, transparent 20px);
    }
    .banner-blue   { background: linear-gradient(135deg, #1B3679, #2A4A9E); }
    .banner-teal   { background: linear-gradient(135deg, #0D7377, #14A085); }
    .banner-purple { background: linear-gradient(135deg, #6D28D9, #8B5CF6); }
    .banner-rose   { background: linear-gradient(135deg, #BE185D, #EC4899); }
    .banner-amber  { background: linear-gradient(135deg, #B45309, #F59E0B); }
    .banner-green  { background: linear-gradient(135deg, #065F46, #10B981); }

    .enrolled-badge {
        background: rgba(255,255,255,0.25); color: white; font-size: 10px; font-weight: 700;
        padding: 4px 10px; border-radius: 99px; backdrop-filter: blur(4px);
        letter-spacing: 0.5px; display: none; align-items: center; gap: 4px;
    }
    .course-card.is-enrolled .enrolled-badge { display: flex; }

    .cc-body { padding: 16px; }
    .cc-category { font-size: 10px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 6px; }
    .cc-name { font-size: 15px; font-weight: 800; color: #111827; margin: 0 0 12px; line-height: 1.3; }
    .cc-meta { display: flex; gap: 14px; font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 14px; }
    .cc-meta i { color: #9CA3AF; margin-right: 3px; }

    /* Enrollment Panel (shown on click) */
    .cc-enroll-panel { border-top: 1px solid #F3F4F6; padding-top: 14px; display: none; }
    .course-card.is-open .cc-enroll-panel { display: block; }
    .ep-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 4px; }
    .ep-desc { font-size: 12px; color: #9CA3AF; margin-bottom: 12px; }
    .ep-row { display: flex; gap: 8px; }
    .btn-enrol {
        flex: 1; background: #1B3679; color: white; border: none; padding: 10px;
        border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 6px;
        transition: background 0.2s;
    }
    .btn-enrol:hover { background: #0B1E4F; }
    .btn-drop {
        flex: 1; background: #FEF2F2; color: #DC2626; border: 1.5px solid #FECACA;
        padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;
        display: none; align-items: center; justify-content: center; gap: 6px;
        transition: all 0.2s;
    }
    .btn-drop:hover { background: #DC2626; color: white; border-color: #DC2626; }
    .btn-full-disabled {
        flex: 1; background: #F9FAFB; color: #9CA3AF; border: 1.5px solid #E5E7EB;
        padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: not-allowed;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-locked {
        flex: 1; background: #FEF3C7; color: #92400E; border: 1.5px solid #FDE68A;
        padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: not-allowed;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .lock-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;
        padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .course-card.is-enrolled .btn-enrol { display: none; }
    .course-card.is-enrolled .btn-drop { display: flex; }

    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 48px; color: #D1D5DB; display: block; margin-bottom: 16px; }
    .empty-state p { font-size: 15px; font-weight: 600; color: #6B7280; margin: 0 0 6px; }
    .empty-state small { font-size: 13px; color: #9CA3AF; }

    /* Toast */
    .toast-wrap { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
    .toast {
        background: #111827; color: white; padding: 14px 20px; border-radius: 12px;
        font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15); animation: slideUp 0.3s ease;
        max-width: 340px;
    }
    .toast.success { background: #065F46; }
    .toast.error   { background: #991B1B; }
    @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

    @media(max-width: 1024px) { .krs-layout { grid-template-columns: 1fr; } .sidebar-panel { display: none; } }
    @media(max-width: 640px) { .courses-grid { grid-template-columns: 1fr; } .stats-bar { gap: 8px; } }
</style>
@endpush

@section('content')
@php
    $currentPage = 'krs';
    $bannerClasses = ['banner-blue','banner-teal','banner-purple','banner-rose','banner-amber','banner-green'];

    // Group courses by category
    $grouped = [];
    foreach ($availableCourses as $c) {
        $cat = 'Semester ' . $c->semester;
        $grouped[$cat][] = $c;
    }
    ksort($grouped);
@endphp

<div class="krs-wrap">
    {{-- Header --}}
    <div class="page-header">
        <h1 class="page-title">Course Enrollment</h1>
        <p class="page-sub">
            @if($semesterAktif)
                {{ $semesterAktif->tahun_ajaran }} &bull; {{ ucfirst($semesterAktif->tingkatan_semester) }}
            @else
                Pilih dan daftarkan mata kuliah Anda
            @endif
        </p>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-chip stat-chip-navy">
            <div class="stat-label">Maks SKS</div>
            <div class="stat-val" id="stat-max">{{ $maxSks }} <small>SKS</small></div>
        </div>
        <div class="stat-chip">
            <div class="stat-label">Diambil</div>
            <div class="stat-val" id="stat-selected">{{ $currentSks }} <small>SKS</small></div>
        </div>
        <div class="stat-chip">
            <div class="stat-label">Sisa</div>
            <div class="stat-val" id="stat-balance">{{ $maxSks - $currentSks }} <small>SKS</small></div>
        </div>
        <div class="stat-chip">
            <div class="stat-label">Matkul</div>
            <div class="stat-val" id="stat-courses">{{ count(array_filter((array)$selectedJadwal)) }} <small>matkul</small></div>
        </div>
    </div>

    <div class="krs-layout">
        {{-- Sidebar --}}
        <div class="sidebar-panel">
            {{-- Search --}}
            <div class="panel-card">
                <div class="panel-title">Cari Mata Kuliah</div>
                <div class="search-box">
                    <input type="text" id="search-input" class="search-input" placeholder="Kode atau nama matkul..." autocomplete="off">
                </div>
                <button class="btn-search-full" onclick="doSearch()">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>

            {{-- Enrollment Status --}}
            <div class="enroll-status-card">
                <div class="es-label">Status Pendaftaran</div>
                <div class="es-phase">Fase 1: Dibuka</div>
                <div class="es-bar"><div class="es-bar-fill"></div></div>
                <div class="es-footer">
                    @if($semesterAktif)
                        {{ $semesterAktif->tahun_ajaran }} &bull; {{ ucfirst($semesterAktif->tingkatan_semester) }}
                    @else
                        Belum ada semester aktif
                    @endif
                </div>
            </div>

            {{-- Course Categories --}}
            <div class="panel-card">
                <div class="panel-title">Kategori Mata Kuliah</div>
                <ul class="cat-list">
                    <li class="cat-item active" data-cat="all" onclick="filterCategory('all', this)">
                        <span>Semua Matkul</span>
                        <span class="cat-badge">{{ count($availableCourses) }}</span>
                    </li>
                    <li class="cat-item" data-cat="enrolled" onclick="filterCategory('enrolled', this)">
                        <span>Sudah Diambil</span>
                        <span class="cat-badge" id="cat-enrolled-count">{{ count(array_filter((array)$selectedJadwal)) }}</span>
                    </li>
                    @foreach($grouped as $catName => $courses)
                    <li class="cat-item" data-cat="{{ $catName }}" onclick="filterCategory('{{ $catName }}', this)">
                        <span>{{ $catName }}</span>
                        <span class="cat-badge">{{ count($courses) }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="main-panel">
            <div class="toolbar">
                <div class="result-info" id="result-info">
                    Menampilkan <strong>{{ count($availableCourses) }}</strong> mata kuliah tersedia
                </div>
            </div>

            <div class="courses-grid" id="courses-grid">
                @foreach($availableCourses as $i => $course)
                @php
                    $isEnrolled = isset($selectedJadwal[$course->id_jadwal]);
                    $isFull = $course->sks_terdaftar >= $course->kuota && !$isEnrolled;
                    $isLocked = $course->jenis === 'wajib' && $course->semester > $studentSemester;
                    $bannerClass = $bannerClasses[$i % count($bannerClasses)];
                    $catKey = 'Semester ' . $course->semester;
                @endphp
                <div class="course-card {{ $isEnrolled ? 'is-enrolled' : '' }}"
                    data-jadwal="{{ $course->id_jadwal }}"
                    data-sks="{{ $course->sks }}"
                    data-kode="{{ strtolower($course->kode_matkul) }}"
                    data-nama="{{ strtolower($course->nama_matkul) }}"
                    data-cat="{{ $catKey }}"
                    onclick="togglePanel(this)">

                    <div class="cc-banner {{ $bannerClass }}">
                        <div class="enrolled-badge"><i class="bi bi-check-circle-fill"></i> Terdaftar</div>
                    </div>

                    <div class="cc-body">
                        <div class="cc-category">
                            {{ $course->kode_matkul }} &bull; {{ $course->jenis === 'wajib' ? 'Mata Kuliah Wajib' : 'Mata Kuliah Pilihan' }}
                            @if($isLocked)
                                &nbsp;<span class="lock-badge"><i class="bi bi-lock-fill"></i> Sem. {{ $course->semester }}</span>
                            @endif
                        </div>
                        <h3 class="cc-name">{{ $course->nama_matkul }}</h3>
                        <div class="cc-meta">
                            <span><i class="bi bi-journal-text"></i>{{ $course->sks }} SKS</span>
                            <span><i class="bi bi-people"></i>{{ $course->sks_terdaftar }}/{{ $course->kuota }}</span>
                            <span><i class="bi bi-clock"></i>{{ substr($course->hari,0,3) }} {{ substr($course->jam_mulai,0,5) }}</span>
                        </div>

                        {{-- Enrollment Panel --}}
                        <div class="cc-enroll-panel" onclick="event.stopPropagation()">
                            <div class="ep-title">Pendaftaran Mandiri</div>
                            <div class="ep-desc">
                                {{ $course->ruang }} &bull; {{ $course->nama_dosen }}
                                @if($isFull) &bull; <span style="color:#DC2626;">Kelas Penuh</span> @endif
                            </div>
                            <div class="ep-row">
                                @if($isLocked)
                                    <button class="btn-locked" disabled>
                                        <i class="bi bi-lock-fill"></i> Tersedia di Semester {{ $course->semester }}
                                    </button>
                                @elseif($isFull)
                                    <button class="btn-full-disabled" disabled>
                                        <i class="bi bi-slash-circle"></i> Kelas Penuh
                                    </button>
                                @else
                                    <button class="btn-enrol" onclick="enrollCourse({{ $course->id_jadwal }}, this)">
                                        <i class="bi bi-plus-circle"></i> Daftar
                                    </button>
                                    <button class="btn-drop" onclick="dropCourse({{ $course->id_jadwal }}, this)">
                                        <i class="bi bi-dash-circle"></i> Batalkan
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div id="empty-state" class="empty-state" style="display:none;">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada mata kuliah ditemukan</p>
                <small>Coba kata kunci atau kategori lain</small>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="toast-wrap" id="toast-wrap"></div>

@endsection

@push('scripts')
<script>
const ENROLL_URL = '{{ route("mahasiswa.krs.enroll") }}';
const DROP_URL   = '{{ route("mahasiswa.krs.drop") }}';
const CSRF       = '{{ csrf_token() }}';

// ── Toggle enrollment panel ──────────────────────────────────────────────────
function togglePanel(card) {
    const wasOpen = card.classList.contains('is-open');
    document.querySelectorAll('.course-card.is-open').forEach(c => c.classList.remove('is-open'));
    if (!wasOpen) card.classList.add('is-open');
}

// ── Enroll single course ─────────────────────────────────────────────────────
function enrollCourse(idJadwal, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mendaftar...';

    fetch(ENROLL_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ id_jadwal: idJadwal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const card = btn.closest('.course-card');
            card.classList.add('is-enrolled');
            updateStats(data.current_sks, data.max_sks);
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Gagal mendaftar.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-plus-circle"></i> Daftar';
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan jaringan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-circle"></i> Daftar';
    });
}

// ── Drop single course ───────────────────────────────────────────────────────
function dropCourse(idJadwal, btn) {
    if (!confirm('Yakin ingin membatalkan mata kuliah ini dari KRS?')) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Membatalkan...';

    fetch(DROP_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ id_jadwal: idJadwal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            const card = btn.closest('.course-card');
            card.classList.remove('is-enrolled');
            updateStats(data.current_sks, data.max_sks);
            showToast(data.message, 'success');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-dash-circle"></i> Batalkan';
        } else {
            showToast(data.message || 'Gagal membatalkan.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-dash-circle"></i> Batalkan';
        }
    })
    .catch(() => {
        showToast('Terjadi kesalahan jaringan.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-dash-circle"></i> Batalkan';
    });
}

// ── Update SKS counters ──────────────────────────────────────────────────────
function updateStats(currentSks, maxSks) {
    document.getElementById('stat-selected').innerHTML = currentSks + ' <small>SKS</small>';
    document.getElementById('stat-max').innerHTML      = maxSks + ' <small>SKS</small>';
    document.getElementById('stat-balance').innerHTML  = (maxSks - currentSks) + ' <small>SKS</small>';
    const enrolledCount = document.querySelectorAll('.course-card.is-enrolled').length;
    document.getElementById('stat-courses').innerHTML  = enrolledCount + ' <small>matkul</small>';
    document.getElementById('cat-enrolled-count').textContent = enrolledCount;
}

// ── Search ───────────────────────────────────────────────────────────────────
function doSearch() {
    const q = document.getElementById('search-input').value.trim().toLowerCase();
    // Reset category filter
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    document.querySelector('[data-cat="all"]').classList.add('active');

    applyFilter({ search: q });
}

// ── Category Filter ──────────────────────────────────────────────────────────
function filterCategory(cat, el) {
    document.querySelectorAll('.cat-item').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('search-input').value = '';
    applyFilter({ category: cat });
}

// ── Core filter logic ────────────────────────────────────────────────────────
function applyFilter({ search = '', category = 'all' } = {}) {
    const cards = document.querySelectorAll('.course-card');
    let visible = 0;

    cards.forEach(card => {
        let show = false;
        if (category === 'all') {
            show = !search || card.dataset.kode.includes(search) || card.dataset.nama.includes(search);
        } else if (category === 'enrolled') {
            show = card.classList.contains('is-enrolled');
        } else {
            show = card.dataset.cat === category;
        }
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const emptyState = document.getElementById('empty-state');
    const resultInfo = document.getElementById('result-info');
    if (visible === 0) {
        emptyState.style.display = '';
        resultInfo.innerHTML = 'Tidak ada hasil ditemukan';
    } else {
        emptyState.style.display = 'none';
        const label = search ? `"<strong>${search}</strong>"` : (category !== 'all' ? `<strong>${category}</strong>` : 'tersedia');
        resultInfo.innerHTML = `Menampilkan <strong>${visible}</strong> mata kuliah ${label}`;
    }
}

// ── Toast ────────────────────────────────────────────────────────────────────
function showToast(msg, type = '') {
    const wrap = document.getElementById('toast-wrap');
    const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-x-circle-fill' : 'bi-info-circle-fill';
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.innerHTML = `<i class="bi ${icon}"></i>${msg}`;
    wrap.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

// Enter to search
document.getElementById('search-input').addEventListener('keydown', e => { if (e.key === 'Enter') doSearch(); });
</script>
@endpush
