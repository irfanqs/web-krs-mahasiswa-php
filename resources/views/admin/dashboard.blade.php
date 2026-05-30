@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
@php $currentPage = 'dashboard'; @endphp

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        position: relative;
        overflow: hidden;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 150px;
        height: 150px;
        background: rgba(238,242,255,0.5);
        border-radius: 50%;
        transform: translate(30%, 30%);
        z-index: 0;
    }
    .stat-card-content {
        position: relative;
        z-index: 1;
    }
    .stat-title {
        font-size: 11px;
        font-weight: 700;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 36px;
        font-weight: 800;
        color: #1B3679;
        margin-bottom: 12px;
        line-height: 1;
    }
    .stat-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 700;
    }
    .badge-green { background: #D1FAE5; color: #065F46; }
    .badge-blue { background: #DBEAFE; color: #1E40AF; }
    .badge-orange { background: #FFEDD5; color: #C2410C; }
    
    .stat-card-navy {
        background: #1B3679;
        color: white;
    }
    .stat-card-navy::after {
        display: none;
    }
    .stat-card-navy .stat-title { color: rgba(255,255,255,0.8); }
    .stat-card-navy .stat-value { color: white; }
    
    .main-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }
    
    .section-title {
        font-size: 12px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
    }
    
    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .qa-card {
        background: #F9FAFB;
        border-radius: 16px;
        padding: 32px 20px;
        text-align: center;
        text-decoration: none;
        color: #1B3679;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }
    .qa-card:hover {
        background: white;
        border-color: #E5E7EB;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    .qa-icon {
        width: 48px;
        height: 48px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 20px;
        color: #111827;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    
    .activity-log {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .activity-title {
        font-size: 16px;
        font-weight: 700;
        color: #1B3679;
    }
    .view-all {
        font-size: 11px;
        font-weight: 800;
        color: #1B3679;
        text-transform: uppercase;
        text-decoration: none;
        letter-spacing: 0.5px;
    }
    .log-item {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
    }
    .log-item:last-child { margin-bottom: 0; }
    .log-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .log-icon-blue { background: #EEF2FF; color: #3B82F6; }
    .log-icon-orange { background: #FFF7ED; color: #F97316; }
    .log-icon-green { background: #ECFDF5; color: #10B981; }
    .log-content { flex: 1; }
    .log-title { font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 4px; }
    .log-meta { font-size: 12px; color: #9CA3AF; }
    
    .side-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        margin-bottom: 20px;
    }
    .side-card-navy {
        background: #2E4B93;
        color: white;
    }
    .semester-title { font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
    .semester-value { font-size: 24px; font-weight: 800; color: white; margin-bottom: 4px; }
    .semester-period { font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .semester-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 12px; }
    .semester-row-label { color: rgba(255,255,255,0.8); }
    .semester-row-value { font-weight: 700; color: white; }
    
    .dist-row { margin-bottom: 16px; }
    .dist-header { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; margin-bottom: 8px; color: #111827; }
    .dist-bar-bg { height: 6px; background: #F3F4F6; border-radius: 99px; overflow: hidden; }
    .dist-bar-fill { height: 100%; border-radius: 99px; }
    .dist-blue { background: #1B3679; }
    .dist-indigo { background: #4F46E5; }
    .dist-orange { background: #F97316; }
    
    .status-header { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 800; color: #4B5563; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }
    .status-dot { width: 8px; height: 8px; background: #10B981; border-radius: 50%; }
    .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .status-box { background: #F9FAFB; padding: 16px; border-radius: 12px; text-align: center; }
    .status-box-label { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .status-box-value { font-size: 20px; font-weight: 800; color: #1B3679; }
</style>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="stat-card-content">
            <div class="stat-title">TOTAL MAHASISWA</div>
            <div class="stat-value">{{ number_format($totalMahasiswa ?? 4821) }}</div>
            <div class="stat-badge badge-green">+12% dari tahun lalu</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-content">
            <div class="stat-title">TOTAL DOSEN</div>
            <div class="stat-value">{{ number_format($totalDosen ?? 156) }}</div>
            <div class="stat-badge badge-blue">Stabil</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-content">
            <div class="stat-title">TOTAL MATKUL</div>
            <div class="stat-value">{{ number_format($totalMatkul ?? 342) }}</div>
            <div class="stat-badge badge-orange">+4 Baru</div>
        </div>
    </div>
    <div class="stat-card stat-card-navy">
        <div class="stat-card-content">
            <div class="stat-title">VALIDASI KRS</div>
            @php $krsPercent = isset($totalKrs) && isset($totalKrsTarget) && $totalKrsTarget > 0 ? min(100, round(($totalKrs / $totalKrsTarget) * 100)) : 85; @endphp
            <div class="stat-value" style="font-size: 32px; margin-bottom: 16px;">{{ $krsPercent }}%</div>
            <div style="height: 6px; background: rgba(255,255,255,0.2); border-radius: 99px; margin-bottom: 8px; overflow: hidden;">
                <div style="height: 100%; width: {{ $krsPercent }}%; background: white; border-radius: 99px;"></div>
            </div>
            <div style="font-size: 11px; color: rgba(255,255,255,0.7);">Progress for {{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester) . ' ' . $semesterAktif->tahun_ajaran : 'Genap 2025/2026' }}</div>
        </div>
    </div>
</div>

<div class="main-layout">
    <div class="left-col">
        <div class="section-title">MANAJEMEN AKSES CEPAT</div>
        <div class="quick-access-grid">
            <a href="{{ route('admin.mahasiswa.index') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-people"></i></div>
                Manajemen<br>Mahasiswa
            </a>
            <a href="{{ route('admin.dosen.index') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-mortarboard"></i></div>
                Manajemen<br>Dosen
            </a>
            <a href="{{ route('admin.matkul.index') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-book-half"></i></div>
                Manajemen<br>Mata Kuliah
            </a>
        </div>
        
        <div class="activity-log">
            <div class="activity-header">
                <div class="activity-title">Log Aktivitas Terbaru</div>
                <a href="#" class="view-all">LIHAT SEMUA LOG</a>
            </div>
            
            <div class="log-item">
                <div class="log-icon log-icon-blue"><i class="bi bi-box-arrow-in-right"></i></div>
                <div class="log-content">
                    <div class="log-title">Login berhasil: Administrator Utama</div>
                    <div class="log-meta">Hari ini pukul 09:42 • IP: 192.168.1.42</div>
                </div>
            </div>

            <div class="log-item">
                <div class="log-icon log-icon-orange"><i class="bi bi-arrow-clockwise"></i></div>
                <div class="log-content">
                    <div class="log-title">Pembaruan Data Mata Kuliah: "Algoritma Pemrograman II"</div>
                    <div class="log-meta">Hari ini pukul 08:15 • Diubah oleh Dr. Hendra</div>
                </div>
            </div>

            <div class="log-item">
                <div class="log-icon log-icon-green"><i class="bi bi-person-plus"></i></div>
                <div class="log-content">
                    <div class="log-title">Impor Massal: 42 Mahasiswa Baru (Informatika)</div>
                    <div class="log-meta">Kemarin pukul 23:30 • Otomasi Sistem</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="right-col">
        <div class="side-card side-card-navy">
            <div class="semester-title">SEMESTER AKADEMIK</div>
            <div class="semester-value">{{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester) . ' ' . $semesterAktif->tahun_ajaran : 'Genap 2025/2026' }}</div>
            <div class="semester-period">Periode Aktif: Feb - Agu 2026</div>

            <div class="semester-row">
                <span class="semester-row-label">Persiapan UAS</span>
                <span class="semester-row-value">12 Hari Tersisa</span>
            </div>
            <div class="semester-row" style="margin-bottom:0;">
                <span class="semester-row-label">Batas Yudisium</span>
                <span class="semester-row-value">15 Agu 2025</span>
            </div>
        </div>
        
        <div class="side-card">
            <div class="section-title">DISTRIBUSI FAKULTAS</div>

            <div class="dist-row">
                <div class="dist-header">
                    <span>Teknik</span>
                    <span>42%</span>
                </div>
                <div class="dist-bar-bg">
                    <div class="dist-bar-fill dist-blue" style="width: 42%"></div>
                </div>
            </div>

            <div class="dist-row">
                <div class="dist-header">
                    <span>Ekonomi</span>
                    <span>28%</span>
                </div>
                <div class="dist-bar-bg">
                    <div class="dist-bar-fill dist-indigo" style="width: 28%"></div>
                </div>
            </div>

            <div class="dist-row" style="margin-bottom:0;">
                <div class="dist-header">
                    <span>Humaniora</span>
                    <span>15%</span>
                </div>
                <div class="dist-bar-bg">
                    <div class="dist-bar-fill dist-orange" style="width: 15%"></div>
                </div>
            </div>
        </div>
        
        <div class="side-card">
            <div class="status-header">
                <div class="status-dot"></div>
                STATUS SISTEM: OPTIMAL
            </div>
            <div class="status-grid">
                <div class="status-box">
                    <div class="status-box-label">BEBAN SERVER</div>
                    <div class="status-box-value">12%</div>
                </div>
                <div class="status-box">
                    <div class="status-box-label">DATABASE</div>
                    <div class="status-box-value">OK</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
