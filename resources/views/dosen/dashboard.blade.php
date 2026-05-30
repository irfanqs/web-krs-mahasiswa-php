@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@push('styles')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 60px;
    }

    /* Top Grid */
    .top-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-bottom: 40px;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border-left: 4px solid #1B3679;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-card.orange-border { border-left-color: #B45309; }
    
    .sc-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .sc-icon { width: 40px; height: 40px; background: #EEF2FF; color: #1B3679; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .stat-card.orange-border .sc-icon { background: #FEF3C7; color: #B45309; }
    .sc-label { font-size: 11px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; }
    
    .sc-value { font-size: 36px; font-weight: 800; color: #1B3679; line-height: 1; margin-bottom: 8px; }
    .sc-subtext { font-size: 14px; color: #4B5563; font-weight: 500; }

    /* Navy Promo Card */
    .promo-card {
        background: linear-gradient(135deg, #1B3679 0%, #11235A 100%);
        border-radius: 16px;
        padding: 24px 32px;
        color: white;
        box-shadow: 0 10px 30px rgba(27,54,121,0.15);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pc-title { font-size: 20px; font-weight: 800; margin-bottom: 12px; }
    .pc-desc { font-size: 14px; color: rgba(255,255,255,0.8); line-height: 1.5; margin-bottom: 20px; }
    .btn-promo {
        background: white;
        color: #1B3679;
        border: none;
        padding: 12px 24px;
        border-radius: 99px;
        font-weight: 800;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        align-self: flex-start;
    }
    .btn-promo:hover { background: #F3F4F6; transform: translateY(-2px); }

    /* Main Content Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .section-title {
        font-size: 20px;
        font-weight: 800;
        color: #1B3679;
    }
    .date-pill {
        background: #F3F4F6;
        color: #6B7280;
        padding: 6px 16px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Jadwal Items */
    .jadwal-list { display: flex; flex-direction: column; gap: 16px; }
    .jadwal-item {
        background: #F8FAFC;
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 24px;
        transition: background 0.2s;
    }
    .jadwal-item:hover { background: #F1F5F9; }
    .ji-time {
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 60px;
    }
    .ji-time-lbl { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .ji-time-val { font-size: 24px; font-weight: 800; color: #1B3679; line-height: 1; }
    
    .ji-info { flex: 1; }
    .ji-title { font-size: 16px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .ji-room { font-size: 13px; color: #6B7280; display: flex; align-items: center; gap: 6px; }
    
    .ji-sks {
        background: #EEF2FF;
        color: #1B3679;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
    }
    .ji-action {
        width: 40px;
        height: 40px;
        background: #1B3679;
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        text-decoration: none;
        transition: transform 0.2s;
    }
    .ji-action:hover { transform: translateY(-2px); }
    .ji-action.online { background: #6B7280; }

    /* Mata Kuliah Aktif Container */
    .matkul-container {
        background: white;
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 24px;
    }
    .matkul-list { display: flex; flex-direction: column; gap: 32px; }
    .mk-item { display: flex; flex-direction: column; gap: 12px; }
    .mk-header { display: flex; justify-content: space-between; align-items: center; }
    .mk-title { font-size: 15px; font-weight: 800; color: #111827; }
    .mk-students { background: #EEF2FF; color: #1B3679; padding: 4px 12px; border-radius: 99px; font-size: 11px; font-weight: 800; }
    
    .mk-progress { height: 8px; background: #F3F4F6; border-radius: 99px; overflow: hidden; }
    .mk-bar { height: 100%; background: #1B3679; border-radius: 99px; }
    
    .mk-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 4px; }
    .btn-mk {
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-outline { border: 1px solid #D1D5DB; color: #1B3679; background: white; }
    .btn-outline:hover { background: #F9FAFB; border-color: #9CA3AF; }
    .btn-solid { background: #1B3679; color: white; border: none; }
    .btn-solid:hover { background: #11235A; }

    /* Campus Info Card */
    .campus-card {
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        height: 200px;
        background: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80') center/cover;
    }
    .cc-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(to top, rgba(11,30,79,0.9) 0%, transparent 100%);
        padding: 40px 24px 24px;
        color: white;
    }
    .cc-lbl { font-size: 10px; font-weight: 800; letter-spacing: 1px; margin-bottom: 4px; text-transform: uppercase; color: rgba(255,255,255,0.8); }
    .cc-title { font-size: 16px; font-weight: 800; }

    @media(max-width: 1200px) {
        .top-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media(max-width: 900px) {
        .main-grid { grid-template-columns: 1fr; }
        .top-stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
@php $currentPage = 'dashboard'; @endphp

<div class="dashboard-container">
    <div class="top-stats-grid">
        <div class="stat-card">
            <div class="sc-header">
                <div class="sc-icon"><i class="bi bi-book"></i></div>
                <div class="sc-label">MATKUL</div>
            </div>
            <div class="sc-value">{{ $jumlahMatkul }}</div>
            <div class="sc-subtext">Mata Kuliah Diampu</div>
        </div>

        <div class="stat-card">
            <div class="sc-header">
                <div class="sc-icon"><i class="bi bi-people"></i></div>
                <div class="sc-label">MAHASISWA</div>
            </div>
            <div class="sc-value">{{ $jumlahMahasiswa }}</div>
            <div class="sc-subtext">Total Mahasiswa</div>
        </div>

        <div class="stat-card orange-border">
            <div class="sc-header">
                <div class="sc-icon"><i class="bi bi-journal-check"></i></div>
                <div class="sc-label">PENILAIAN</div>
            </div>
            <div class="sc-value">{{ $jumlahGrading }}</div>
            <div class="sc-subtext">Input Nilai Berjalan</div>
        </div>

        <div class="promo-card">
            <div>
                <div class="pc-title">Periode Penilaian Aktif</div>
                <div class="pc-desc">Semester Genap 2025/2026 berakhir dalam 5 hari. Pastikan semua nilai akhir telah ditinjau.</div>
            </div>
            <a href="{{ route('dosen.input_nilai') }}" class="btn-promo">
                <i class="bi bi-pencil-square"></i> Input Nilai Sekarang
            </a>
        </div>
    </div>

    <div class="main-grid">
        <!-- Left Col -->
        <div>
            <div class="section-header">
                <div class="section-title">Jadwal Mengajar Hari Ini</div>
                <div class="date-pill">{{ strtoupper(date('D, d M')) }}</div>
            </div>
            
            <div class="jadwal-list">
                @forelse($jadwalHariIni as $index => $j)
                    <div class="jadwal-item">
                        <div class="ji-time">
                            <span class="ji-time-lbl">START</span>
                            <span class="ji-time-val">{{ substr($j->jam_mulai,0,5) }}</span>
                        </div>
                        <div class="ji-info">
                            <div class="ji-title">{{ $j->nama_matkul }}</div>
                            <div class="ji-room">
                                @if(strtolower($j->ruang) == 'online')
                                    <i class="bi bi-camera-video"></i> Pertemuan Online
                                @else
                                    <i class="bi bi-geo-alt"></i> Ruang {{ $j->ruang }}
                                @endif
                            </div>
                        </div>
                        <div class="ji-sks">{{ $j->sks ?? '3' }} SKS</div>
                        <a href="{{ route('dosen.daftar_mahasiswa') }}?id_jadwal={{ $j->id_jadwal }}" class="ji-action {{ strtolower($j->ruang) == 'online' ? 'online' : '' }}" title="Daftar Mahasiswa">
                            @if(strtolower($j->ruang) == 'online')
                                <i class="bi bi-link-45deg"></i>
                            @else
                                <i class="bi bi-qr-code-scan"></i>
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="jadwal-item" style="justify-content:center;">
                        <span style="color:#6B7280;font-weight:600;">Tidak ada jadwal hari ini.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Col -->
        <div>
            <div class="matkul-container">
                <div class="section-title" style="margin-bottom:24px;">Mata Kuliah Aktif</div>
                
                <div class="matkul-list">
                    @forelse($matkulAktif as $m)
                        <div class="mk-item">
                            <div class="mk-header">
                                <div class="mk-title">{{ $m->nama_matkul }}</div>
                                <div class="mk-students">{{ $m->enrolled }}/{{ $m->kuota }} Mahasiswa</div>
                            </div>
                            <div class="mk-progress">
                                <div class="mk-bar" style="width:{{ $m->kuota > 0 ? ($m->enrolled/$m->kuota)*100 : 0 }}%"></div>
                            </div>
                            <div class="mk-actions">
                                <a href="{{ route('dosen.daftar_mahasiswa') }}?id_jadwal={{ $m->id_jadwal }}" class="btn-mk btn-outline">Daftar Mahasiswa</a>
                                <a href="{{ route('dosen.input_nilai') }}?id_jadwal={{ $m->id_jadwal }}" class="btn-mk btn-solid">Input Nilai</a>
                            </div>
                        </div>
                    @empty
                        <div style="color:#6B7280;font-weight:600;text-align:center;padding:24px 0;">Tidak ada mata kuliah aktif.</div>
                    @endforelse
                </div>
            </div>

            <div class="campus-card">
                <div class="cc-overlay">
                    <div class="cc-lbl">INFO KAMPUS</div>
                    <div class="cc-title">Gedung Teknik - Renovasi Lantai 4</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
