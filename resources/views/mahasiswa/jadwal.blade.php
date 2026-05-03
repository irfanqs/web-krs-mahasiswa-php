@extends('layouts.app')
@section('title', 'Weekly Schedule')

@push('styles')
<style>
    .schedule-container {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 32px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #0B1E4F;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }
    .page-subtitle {
        font-size: 15px;
        color: #4B5563;
        font-weight: 500;
        margin: 0;
    }
    .header-actions {
        display: flex;
        gap: 16px;
        align-items: center;
    }
    .semester-select {
        padding: 12px 20px;
        border-radius: 12px;
        border: 1px solid #E5E7EB;
        background: white;
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        outline: none;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1em;
        padding-right: 40px;
    }
    .btn-download {
        background: #1B3679;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(27,54,121,0.2);
        transition: all 0.2s;
    }
    .btn-download:hover { background: #0B1E4F; transform: translateY(-2px); }

    /* Main Schedule Grid */
    .schedule-board {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .days-header {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        border-bottom: 1px solid #F3F4F6;
    }
    .day-col-header {
        text-align: center;
        padding: 20px 10px;
        border-right: 1px solid #F3F4F6;
    }
    .day-col-header:last-child { border-right: none; }
    .day-name { font-size: 12px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .day-date { font-size: 24px; font-weight: 800; color: #1B3679; }

    .schedule-body {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        min-height: 500px;
    }
    .day-col-body {
        border-right: 1px solid #F3F4F6;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .day-col-body:last-child { border-right: none; }

    /* Course Cards */
    .course-card {
        border-radius: 12px;
        padding: 16px;
        position: relative;
    }
    .course-title { font-size: 14px; font-weight: 800; margin-bottom: 8px; line-height: 1.3; }
    .course-meta { font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 4px; opacity: 0.8; margin-bottom: 4px;}

    /* Card Variants mapped to $c->jenis or randomly if none */
    .card-major { background: #EFF6FF; border-left: 4px solid #3B82F6; color: #1E3A8A; }
    .card-elective { background: #EEF2FF; border-left: 4px solid #1B3679; color: #1E3A8A; }
    .card-minor { background: #FFF7ED; border-left: 4px solid #F59E0B; color: #9A3412; }
    .card-research { background: #ECFDF5; border-left: 4px solid #10B981; color: #065F46; }
    .card-default { background: #F3F4F6; border-left: 4px solid #9CA3AF; color: #374151; }

    /* Legend */
    .schedule-legend {
        display: flex;
        align-items: center;
        gap: 24px;
        margin-top: 24px;
        padding: 0 16px;
    }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #6B7280; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; }
    .dot-major { background: #3B82F6; }
    .dot-elective { background: #1B3679; }
    .dot-minor { background: #F59E0B; }
    .dot-research { background: #10B981; }
    .legend-updated { margin-left: auto; font-size: 12px; color: #9CA3AF; font-weight: 500; }

    /* Right Sidebar */
    .credits-card {
        background: linear-gradient(135deg, #0B1E4F 0%, #1A367B 100%);
        border-radius: 24px;
        padding: 32px;
        color: white;
        margin-bottom: 24px;
        box-shadow: 0 10px 30px rgba(11,30,79,0.15);
    }
    .credits-header { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.7); margin-bottom: 8px; }
    .credits-value { font-size: 48px; font-weight: 800; line-height: 1; margin-bottom: 24px; display: flex; align-items: baseline; gap: 8px; }
    .credits-value span { font-size: 16px; font-weight: 600; color: rgba(255,255,255,0.6); }
    .credits-footer { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; }
    .credits-status { color: rgba(255,255,255,0.9); }
    .credits-badge { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 99px; }

    .lecturers-card {
        background: white;
        border-radius: 24px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .lecturers-header { font-size: 13px; font-weight: 800; color: #1B3679; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 24px; }
    .lecturer-item { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
    .lecturer-item:last-child { margin-bottom: 0; }
    .lecturer-avatar { width: 40px; height: 40px; border-radius: 50%; background: #EEF2FF; color: #1B3679; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
    .lecturer-info { flex: 1; }
    .lecturer-name { font-size: 14px; font-weight: 800; color: #111827; margin-bottom: 2px; }
    .lecturer-subj { font-size: 12px; color: #6B7280; font-weight: 500; }
    .lecturer-msg { color: #1B3679; font-size: 18px; cursor: pointer; opacity: 0.8; transition: opacity 0.2s; }
    .lecturer-msg:hover { opacity: 1; }

    @media(max-width: 1024px) {
        .schedule-container { grid-template-columns: 1fr; }
        .schedule-body { grid-template-columns: 1fr; min-height: auto; }
        .days-header { display: none; }
        .day-col-body { border-right: none; border-bottom: 1px solid #F3F4F6; }
        /* Add fake headers for mobile */
        .day-col-body::before { content: attr(data-day); font-weight: 800; color: #1B3679; font-size: 16px; margin-bottom: 8px; }
        .schedule-legend { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')
@php 
    $currentPage = 'jadwal'; 
    $hariListArray = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    // Generate dates starting from this week's Monday
    $startOfWeek = now()->startOfWeek();
    $dates = [];
    foreach(range(0, 4) as $i) {
        $dates[] = $startOfWeek->copy()->addDays($i)->format('d');
    }
@endphp

<div class="header-section">
    <div>
        <div style="font-size: 11px; font-weight: 800; color: #1B3679; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Student Academic Flow</div>
        <h1 class="page-title">Weekly Schedule</h1>
        <p class="page-subtitle">Academic Year {{ $semesterSelected->tahun_ajaran ?? '2023/2024' }} &mdash; {{ ucfirst($semesterSelected->tingkatan_semester ?? 'Ganjil') }} Semester</p>
    </div>
    <div class="header-actions">
        <form method="GET">
            <select name="semester_id" class="semester-select" onchange="this.form.submit()">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id_semester }}" {{ $semesterSelected?->id_semester == $sem->id_semester ? 'selected' : '' }}>
                        {{ ucfirst($sem->tingkatan_semester) }} Semester {{ $sem->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
        </form>
        <button onclick="window.print()" class="btn-download">
            <i class="bi bi-download"></i> Download Schedule
        </button>
    </div>
</div>

<div class="schedule-container">
    <!-- LEFT: Calendar Grid -->
    <div class="left-col">
        <div class="schedule-board">
            <div class="days-header">
                @foreach($hariListArray as $index => $hari)
                    <div class="day-col-header">
                        <div class="day-name">{{ substr($hari, 0, 3) }}</div>
                        <div class="day-date">{{ $dates[$index] }}</div>
                    </div>
                @endforeach
            </div>
            <div class="schedule-body">
                @foreach($hariListArray as $hari)
                    <div class="day-col-body" data-day="{{ $hari }}">
                        @php $courses = collect($jadwalByHari->get($hari, [])); @endphp
                        @forelse($courses as $index => $c)
                            @php
                                // Map jenis to specific card styles based on standard keywords
                                $jenis = strtolower($c->jenis ?? '');
                                if(str_contains($jenis, 'wajib') || str_contains($jenis, 'major')) $cardClass = 'card-major';
                                elseif(str_contains($jenis, 'pilihan') || str_contains($jenis, 'elective')) $cardClass = 'card-elective';
                                elseif(str_contains($jenis, 'minor')) $cardClass = 'card-minor';
                                elseif(str_contains($jenis, 'praktikum') || str_contains($jenis, 'lab')) $cardClass = 'card-research';
                                else {
                                    // Fallback deterministic colors based on index
                                    $classes = ['card-major', 'card-elective', 'card-minor', 'card-research'];
                                    $cardClass = $classes[$index % 4];
                                }
                            @endphp
                            <div class="course-card {{ $cardClass }}">
                                <div class="course-title">{{ $c->nama_matkul }}</div>
                                <div class="course-meta"><i class="bi bi-clock"></i> {{ substr($c->jam_mulai,0,5) }} - {{ substr($c->jam_selesai,0,5) }}</div>
                                <div class="course-meta"><i class="bi bi-geo-alt"></i> {{ $c->ruang }}</div>
                            </div>
                        @empty
                            <!-- Empty slot filler -->
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>

        <div class="schedule-legend">
            <div class="legend-item"><div class="legend-dot dot-major"></div> Major Courses</div>
            <div class="legend-item"><div class="legend-dot dot-elective"></div> Selected Electives</div>
            <div class="legend-item"><div class="legend-dot dot-minor"></div> Minor Courses</div>
            <div class="legend-item"><div class="legend-dot dot-research"></div> Research/Lab</div>
            <div class="legend-updated">* Last updated on {{ now()->format('M d, Y \a\t h:i A') }}</div>
        </div>
    </div>

    <!-- RIGHT: Sidebars -->
    <div class="right-col">
        <div class="credits-card">
            <div class="credits-header">TOTAL CREDITS</div>
            <div class="credits-value">{{ $totalSks }} <span>SKS</span></div>
            <div class="credits-footer">
                <span class="credits-status">Status: Active</span>
                <span class="credits-badge">{{ $totalSks >= 20 ? 'Full Load' : 'Normal Load' }}</span>
            </div>
        </div>

        <div class="lecturers-card">
            <div class="lecturers-header">TODAY'S LECTURERS</div>
            @forelse($dosenHariIni as $d)
                @php
                    // Get initials
                    $initials = strtoupper(substr($d->nama_dosen, 0, 1));
                    $parts = explode(' ', $d->nama_dosen);
                    if(count($parts) > 1 && strlen($parts[1]) > 0) {
                        // try to skip titles like Dr. Prof. etc for the second initial if possible, but keep it simple
                        $second = substr($parts[1], 0, 1);
                        if(ctype_alpha($second)) $initials .= strtoupper($second);
                    }
                @endphp
                <div class="lecturer-item">
                    <div class="lecturer-avatar">{{ $initials }}</div>
                    <div class="lecturer-info">
                        <div class="lecturer-name">{{ $d->nama_dosen }}</div>
                        <div class="lecturer-subj">{{ $d->nama_matkul }}</div>
                    </div>
                    <i class="bi bi-chat-left-text lecturer-msg" title="Message Lecturer"></i>
                </div>
            @empty
                <p style="font-size:13px;color:#9CA3AF;">No classes scheduled for today.</p>
            @endforelse
        </div>
        
        <!-- Next Assignment & Campus Map omitted as requested -->
    </div>
</div>
@endsection
