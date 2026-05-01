@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
.page-title { font-size:28px; font-weight:700; color:#0B1E4F; margin-bottom:4px; }
.page-subtitle { color:#6B7489; font-size:14px; }
.grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
.grid-2 { display:grid; grid-template-columns:2fr 1fr; gap:24px; margin-bottom:24px; }
.card { background:white; border-radius:16px; padding:20px 24px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.card-navy { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; }
.card-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6B7489; margin-bottom:8px; }
.card-navy .card-title { color:rgba(255,255,255,.7); }
.card-value { font-size:28px; font-weight:700; color:#0B1E4F; }
.card-navy .card-value { color:white; }
.card-sub { font-size:13px; color:#6B7489; margin-top:4px; }
.card-navy .card-sub { color:rgba(255,255,255,.6); }
.hero-card { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; border-radius:16px; padding:28px 32px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; }
.hero-info h2 { font-size:22px; font-weight:700; margin:0 0 4px 0; color:white; }
.hero-info p { font-size:14px; opacity:.8; margin:0 0 20px 0; color:white; }
.hero-stats { display:flex; gap:32px; }
.hero-stat-label { font-size:10px; font-weight:700; opacity:.7; text-transform:uppercase; letter-spacing:1px; }
.hero-stat-value { font-size:24px; font-weight:700; }
.hero-actions { display:flex; gap:12px; flex-wrap:wrap; }
.btn-outline-white { padding:10px 20px; border:2px solid rgba(255,255,255,.4); color:white; background:transparent; border-radius:10px; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:8px; transition:all .2s; }
.btn-outline-white:hover { background:rgba(255,255,255,.1); border-color:rgba(255,255,255,.7); }
.section-title { font-size:16px; font-weight:700; color:#0B1E4F; margin-bottom:16px; }
.announcement-item { padding:12px 0; border-bottom:1px solid #E4E7EE; }
.announcement-item:last-child { border-bottom:none; }
.badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; }
.badge-academic { background:#E3F2FD; color:#1565C0; }
.badge-system { background:#F3E5F5; color:#7B1FA2; }
.badge-event { background:#E8F5E9; color:#2E7D32; }
.session-item { display:flex; align-items:center; gap:16px; padding:12px; background:#F5F6FA; border-radius:12px; margin-bottom:8px; }
.session-time { font-size:12px; font-weight:700; color:#6B7489; white-space:nowrap; }
.session-name { font-size:14px; font-weight:600; color:#0B1E4F; }
.session-room { font-size:12px; color:#6B7489; }
@media(max-width:900px){.grid-2{grid-template-columns:1fr;}.grid-3{grid-template-columns:1fr 1fr;}}
</style>
@endpush

@section('content')
@php $currentPage = 'dashboard'; @endphp

<div class="hero-card">
    <div class="hero-info">
        <h2>Welcome back, {{ $mahasiswa->nama }}!</h2>
        <p>{{ $mahasiswa->program_studi }} &bull; {{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester).' '.$semesterAktif->tahun_ajaran : 'No Active Semester' }}</p>
        <div class="hero-stats">
            <div>
                <div class="hero-stat-label">GPA (IPK)</div>
                <div class="hero-stat-value">{{ number_format($ipk, 2) }}</div>
            </div>
            <div>
                <div class="hero-stat-label">Credits Earned</div>
                <div class="hero-stat-value">{{ $sksTempuh }}</div>
            </div>
        </div>
    </div>
    <div class="hero-actions">
        <a href="{{ route('mahasiswa.krs') }}" class="btn-outline-white"><i class="bi bi-calendar-check"></i> KRS Enrollment</a>
        <a href="{{ route('mahasiswa.khs') }}" class="btn-outline-white"><i class="bi bi-star"></i> KHS Results</a>
    </div>
</div>

<div class="grid-2">
    <div>
        <div class="section-title">Pengumuman Kampus</div>
        <div class="card">
            @forelse($pengumuman as $p)
                <div class="announcement-item">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span class="badge badge-{{ strtolower($p->tipe) }}">{{ $p->tipe }}</span>
                        <small style="color:#6B7489;">{{ $p->created_at ? $p->created_at->format('d M Y') : '' }}</small>
                    </div>
                    <div style="font-size:14px;font-weight:600;color:#0B1E4F;">{{ $p->judul }}</div>
                </div>
            @empty
                <p style="color:#6B7489;font-size:14px;">Belum ada pengumuman.</p>
            @endforelse
        </div>
    </div>
    <div>
        <div class="section-title">Today's Sessions</div>
        @forelse($jadwalHariIni as $j)
            <div class="session-item">
                <div>
                    <div class="session-name">{{ $j->nama_matkul }}</div>
                    <div class="session-room"><i class="bi bi-geo-alt"></i> {{ $j->ruang }} &bull; {{ substr($j->jam_mulai,0,5) }}-{{ substr($j->jam_selesai,0,5) }}</div>
                    <div class="session-room"><i class="bi bi-person"></i> {{ $j->nama_dosen }}</div>
                </div>
            </div>
        @empty
            <div class="card"><p style="color:#6B7489;font-size:14px;">Tidak ada jadwal hari ini.</p></div>
        @endforelse
    </div>
</div>
@endsection
