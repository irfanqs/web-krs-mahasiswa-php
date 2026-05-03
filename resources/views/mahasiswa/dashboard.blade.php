@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-container {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    /* Left Column */
    .welcome-card {
        background: linear-gradient(135deg, #0B1E4F 0%, #1A367B 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(11,30,79,0.15);
        position: relative;
        overflow: hidden;
    }
    .welcome-title {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }
    .welcome-subtitle {
        font-size: 15px;
        color: rgba(255,255,255,0.8);
        line-height: 1.5;
        margin-bottom: 32px;
        max-width: 80%;
    }
    .stats-row {
        display: flex;
        gap: 16px;
    }
    .stat-box {
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        padding: 20px 24px;
        flex: 1;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }
    .stat-val {
        font-size: 32px;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: baseline;
        gap: 4px;
        line-height: 1;
    }
    .stat-val span {
        font-size: 14px;
        font-weight: 600;
        color: rgba(255,255,255,0.6);
    }

    .degree-card {
        background: white;
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .degree-info {
        flex: 0 0 30%;
    }
    .degree-title { font-size: 20px; font-weight: 800; color: #0B1E4F; margin-bottom: 4px; }
    .degree-subtitle { font-size: 13px; color: #6B7280; font-weight: 500; }
    .degree-progress-text { margin-top: 16px; display: flex; align-items: baseline; gap: 8px; }
    .degree-pct { font-size: 40px; font-weight: 800; color: #0B1E4F; line-height: 1; }
    .degree-lbl { font-size: 14px; font-weight: 700; color: #9CA3AF; }
    
    .degree-bar-wrapper {
        flex: 1;
        margin-left: 40px;
    }
    .progress-bar-bg {
        height: 12px;
        background: #F3F4F6;
        border-radius: 99px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: #0B1E4F;
        border-radius: 99px;
        width: 82%; /* Mockup value */
    }
    .progress-markers {
        display: flex;
        justify-content: space-between;
    }
    .marker { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: #4B5563; }
    .marker-dot { width: 10px; height: 10px; border-radius: 50%; }
    .marker-dot.done { background: #0B1E4F; }
    .marker-dot.pending { background: #D1D5DB; }

    .announcement-card {
        background: white;
        border-radius: 24px;
        padding: 32px 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .section-title {
        font-size: 20px;
        font-weight: 800;
        color: #0B1E4F;
        margin: 0;
    }
    .view-all {
        font-size: 12px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
    }
    
    .announcement-list {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .announcement-item {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }
    .date-box {
        background: #F8FAFC;
        border-radius: 12px;
        width: 60px;
        height: 60px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid #F1F5F9;
    }
    .date-month { font-size: 11px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; margin-bottom: 2px; }
    .date-day { font-size: 20px; font-weight: 800; color: #0B1E4F; line-height: 1; }
    .ann-content {
        flex: 1;
    }
    .ann-title { font-size: 15px; font-weight: 800; color: #111827; margin-bottom: 6px; }
    .ann-desc { font-size: 13px; color: #6B7280; line-height: 1.4; margin: 0; }
    .ann-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .badge-academic { background: #EEF2FF; color: #4F46E5; }
    .badge-system { background: #FFEDD5; color: #C2410C; }
    .badge-event { background: #FCE7F3; color: #BE185D; }

    /* Right Column */
    .quick-access-header {
        font-size: 12px;
        font-weight: 800;
        color: #0B1E4F;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 16px;
        margin-top: 8px;
    }
    .qa-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }
    .qa-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .qa-card:hover { transform: translateY(-2px); }
    .qa-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #EEF2FF;
        color: #1B3679;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .qa-text { flex: 1; }
    .qa-title { font-size: 14px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .qa-subtitle { font-size: 12px; color: #9CA3AF; }
    .qa-arrow { color: #D1D5DB; font-size: 16px; }
    


    .sessions-card {
        background: white;
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .badge-date {
        background: #EEF2FF;
        color: #1B3679;
        padding: 6px 12px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
    }
    .timeline {
        position: relative;
        margin-top: 16px;
        padding-left: 20px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0;
        top: 20px;
        bottom: 40px;
        width: 2px;
        background: #F3F4F6;
    }
    .session-item {
        position: relative;
        margin-bottom: 32px;
    }
    .session-item:last-child { margin-bottom: 16px; }
    .session-icon {
        position: absolute;
        left: -38px;
        top: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: 4px solid white;
    }
    .session-active .session-icon { background: #0B1E4F; color: white; }
    .session-upcoming .session-icon { background: #F3F4F6; color: #9CA3AF; }
    .session-faded .session-icon { background: #F9FAFB; color: #D1D5DB; }
    
    .session-time { font-size: 11px; font-weight: 800; color: #0B1E4F; letter-spacing: 1px; margin-bottom: 6px; display: block; }
    .session-upcoming .session-time { color: #9CA3AF; }
    .session-faded .session-time { color: #D1D5DB; }
    
    .session-title { font-size: 15px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .session-upcoming .session-title { color: #4B5563; }
    .session-faded .session-title { color: #9CA3AF; }
    
    .session-meta { font-size: 12px; color: #6B7280; }
    .session-faded .session-meta { color: #D1D5DB; }

    .full-schedule-link {
        display: block;
        text-align: center;
        margin-top: 32px;
        font-size: 12px;
        font-weight: 800;
        color: #0B1E4F;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
    }

    @media(max-width: 1024px) {
        .dashboard-container { grid-template-columns: 1fr; }
        .degree-card { flex-direction: column; align-items: flex-start; gap: 24px; }
        .degree-bar-wrapper { margin-left: 0; width: 100%; }
        .stats-row { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')
@php 
    $currentPage = 'dashboard'; 
    $firstName = explode(' ', $mahasiswa->nama)[0] ?? 'Student';
    // Dummy progression mapping if not available in DB
    $sksTarget = 144;
    $progressPct = $sksTarget > 0 ? min(100, round(($sksTempuh / $sksTarget) * 100)) : 82;
@endphp

<div class="dashboard-container">
    
    <!-- LEFT COLUMN -->
    <div class="left-col">
        <!-- Welcome Card -->
        <div class="welcome-card">
            <div class="welcome-title">Welcome back, {{ $firstName }}.</div>
            <div class="welcome-subtitle">Your academic journey is looking exceptional this semester. You're currently in the top 5% of your cohort.</div>
            
            <div class="stats-row">
                <div class="stat-box">
                    <span class="stat-label">GPA (IPK)</span>
                    <div class="stat-val">{{ number_format($ipk, 2) }}</div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">CREDITS EARNED</span>
                    <div class="stat-val">{{ $sksTempuh }} <span>SKS</span></div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">CURRENT SEMESTER</span>
                    <div class="stat-val">0{{ $semesterAktif ? (substr($semesterAktif->tahun_ajaran, -1) == '1' ? '7' : '6') : '7' }}</div>
                </div>
            </div>
        </div>

        <!-- Degree Path -->
        <div class="degree-card">
            <div class="degree-info">
                <div class="degree-title">Degree Path</div>
                <div class="degree-subtitle">{{ $mahasiswa->program_studi }}</div>
                <div class="degree-progress-text">
                    <div class="degree-pct">{{ $progressPct }}%</div>
                    <div class="degree-lbl">Complete</div>
                </div>
            </div>
            <div class="degree-bar-wrapper">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: {{ $progressPct }}%"></div>
                </div>
                <div class="progress-markers">
                    <div class="marker"><div class="marker-dot done"></div> Core Curriculum<br>(Completed)</div>
                    <div class="marker"><div class="marker-dot pending"></div> Thesis Defense<br>(Pending)</div>
                </div>
            </div>
        </div>

        <!-- Pengumuman Kampus -->
        <div class="announcement-card">
            <div class="section-header">
                <h3 class="section-title">Pengumuman Kampus</h3>
                <a href="#" class="view-all">VIEW ALL</a>
            </div>
            <div class="announcement-list">
                @php
                    // Map real pengumuman to the mockup's badge styles or use fallbacks
                    $defaultAnnouncements = [
                        (object)['created_at' => \Carbon\Carbon::parse('2023-10-12'), 'judul' => 'Mid-Semester Examination Schedule for Fall 2023', 'desc' => 'Detailed schedules are now available for...', 'tipe' => 'ACADEMIC'],
                        (object)['created_at' => \Carbon\Carbon::parse('2023-10-09'), 'judul' => 'Digital Library Maintenance - Temporary Downtime', 'desc' => 'Service will be interrupted this weekend fo...', 'tipe' => 'SYSTEM'],
                        (object)['created_at' => \Carbon\Carbon::parse('2023-10-05'), 'judul' => 'New Seminar Series: AI in the Modern Workplace', 'desc' => 'Join us for a three-part guest lecture...', 'tipe' => 'EVENT'],
                    ];
                    $displayPengumuman = $pengumuman->count() > 0 ? $pengumuman->take(3) : $defaultAnnouncements;
                @endphp
                
                @foreach($displayPengumuman as $p)
                    <div class="announcement-item">
                        <div class="date-box">
                            <span class="date-month">{{ $p->created_at ? $p->created_at->format('M') : 'OCT' }}</span>
                            <span class="date-day">{{ $p->created_at ? $p->created_at->format('d') : '12' }}</span>
                        </div>
                        <div class="ann-content">
                            <div class="ann-title">{{ $p->judul }}</div>
                            <div class="ann-desc">{{ $p->desc ?? 'Informasi lebih lanjut dapat dilihat pada portal akademik.' }}</div>
                        </div>
                        <div class="ann-badge badge-{{ strtolower($p->tipe ?? 'academic') }}">{{ $p->tipe ?? 'ACADEMIC' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="right-col">
        <div class="quick-access-header">QUICK ACCESS</div>
        <div class="qa-list">
            <a href="{{ route('mahasiswa.krs') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-clipboard-check"></i></div>
                <div class="qa-text">
                    <div class="qa-title">KRS Enrollment</div>
                    <div class="qa-subtitle">Plan your next semester</div>
                </div>
                <i class="bi bi-chevron-right qa-arrow"></i>
            </a>
            <a href="{{ route('mahasiswa.khs') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="qa-text">
                    <div class="qa-title">KHS Results</div>
                    <div class="qa-subtitle">View detailed grades</div>
                </div>
                <i class="bi bi-chevron-right qa-arrow"></i>
            </a>
            <a href="{{ route('mahasiswa.jadwal') }}" class="qa-card">
                <div class="qa-icon"><i class="bi bi-qr-code-scan"></i></div>
                <div class="qa-text">
                    <div class="qa-title">Attendance List</div>
                    <div class="qa-subtitle">Scan to check-in</div>
                </div>
                <i class="bi bi-chevron-right qa-arrow"></i>
            </a>
        </div>



        <div class="sessions-card">
            <div class="section-header">
                <h3 class="section-title">Today's Sessions</h3>
                <div class="badge-date">{{ now()->format('D, d M') }}</div>
            </div>
            
            <div class="timeline">
                @php
                    $mockSessions = [
                        (object)['jam_mulai'=>'08:00', 'jam_selesai'=>'10:30', 'nama_matkul'=>'Advanced Web Architecture', 'ruang'=>'Hall B - Room 302', 'nama_dosen'=>'Prof. Dr. Satria', 'status'=>'session-active', 'icon'=>'bi-mortarboard'],
                        (object)['jam_mulai'=>'11:00', 'jam_selesai'=>'13:00', 'nama_matkul'=>'Discrete Mathematics', 'ruang'=>'Lab C - Room 101', 'nama_dosen'=>'Dr. Linda W.', 'status'=>'session-upcoming', 'icon'=>'bi-flask'],
                        (object)['jam_mulai'=>'14:00', 'jam_selesai'=>'16:00', 'nama_matkul'=>'Operating Systems II', 'ruang'=>'Virtual Room', 'nama_dosen'=>'Prof. Agus S.', 'status'=>'session-faded', 'icon'=>'bi-laptop'],
                    ];
                    $displaySessions = $jadwalHariIni->count() > 0 ? $jadwalHariIni : $mockSessions;
                @endphp

                @foreach($displaySessions as $index => $s)
                    @php 
                        $statusClass = isset($s->status) ? $s->status : ($index == 0 ? 'session-active' : ($index == 1 ? 'session-upcoming' : 'session-faded')); 
                        $iconClass = isset($s->icon) ? $s->icon : 'bi-journal-bookmark';
                    @endphp
                    <div class="session-item {{ $statusClass }}">
                        <div class="session-icon"><i class="bi {{ $iconClass }}"></i></div>
                        <span class="session-time">{{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}</span>
                        <div class="session-title">{{ $s->nama_matkul }}</div>
                        <div class="session-meta">{{ $s->ruang }} &bull; {{ $s->nama_dosen }}</div>
                    </div>
                @endforeach
            </div>
            
            <a href="{{ route('mahasiswa.jadwal') }}" class="full-schedule-link">FULL SCHEDULE &rarr;</a>
        </div>
    </div>
</div>
@endsection
