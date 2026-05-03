@extends('layouts.app')
@section('title', 'Course Enrollment')

@push('styles')
<style>
    .krs-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 100px; /* space for sticky footer */
    }

    /* Header & Top Stats */
    .header-area {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 24px;
        margin-bottom: 32px;
    }
    .header-text { max-width: 600px; }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1B3679;
        margin: 0 0 12px 0;
        letter-spacing: -0.5px;
    }
    .page-subtitle {
        font-size: 15px;
        color: #6B7280;
        line-height: 1.6;
        margin: 0;
    }

    .top-stats {
        display: flex;
        gap: 16px;
    }
    .ts-card {
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        min-width: 130px;
    }
    .ts-title {
        font-size: 10px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    .ts-value {
        font-size: 32px;
        font-weight: 800;
        color: #1B3679;
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 6px;
    }
    .ts-value span { font-size: 13px; font-weight: 600; color: #9CA3AF; }

    /* Layout: Sidebar + Main */
    .main-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 32px;
    }

    /* Sidebar */
    .sidebar-card {
        background: linear-gradient(135deg, #1B3679 0%, #11235A 100%);
        border-radius: 20px;
        padding: 32px 24px;
        color: white;
        box-shadow: 0 10px 30px rgba(27,54,121,0.15);
    }
    .sc-title {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.7);
        margin-bottom: 16px;
    }
    .sc-phase {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 24px;
    }
    .sc-progress {
        height: 6px;
        background: rgba(255,255,255,0.2);
        border-radius: 99px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .sc-bar {
        width: 60%;
        height: 100%;
        background: white;
        border-radius: 99px;
    }
    .sc-footer {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
    }

    /* Main Grid */
    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        color: #6B7280;
        font-size: 14px;
        font-weight: 600;
    }
    .toolbar-icons { display: flex; gap: 8px; }
    .icon-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #1B3679; color: white; cursor: pointer; }
    .icon-btn.inactive { background: white; color: #9CA3AF; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 24px;
    }

    /* Course Card */
    .course-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border-left: 4px solid transparent;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
    }
    .course-card.is-selected {
        border-left-color: #1B3679;
    }

    .cc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .cc-code {
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .selected-pill { display: none; }
    .is-selected .selected-pill {
        display: inline-block;
        background: #EEF2FF;
        color: #1B3679;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .cc-title {
        font-size: 18px;
        font-weight: 800;
        color: #1B3679;
        margin: 0 0 16px 0;
        line-height: 1.3;
    }

    .cc-meta {
        display: flex;
        gap: 24px;
        font-size: 13px;
        font-weight: 600;
        color: #4B5563;
        margin-bottom: 16px;
    }
    .cc-meta i { color: #9CA3AF; margin-right: 4px; font-size: 15px; }

    .cc-time {
        font-size: 13px;
        color: #6B7280;
        font-weight: 500;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cc-time i { color: #9CA3AF; font-size: 15px; }

    .cc-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #F3F4F6;
    }
    .cc-lecturer {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cc-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #F3F4F6;
        color: #4B5563;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        overflow: hidden;
    }
    .cc-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .cc-lecturer span {
        font-size: 12px;
        font-weight: 700;
        color: #111827;
    }

    /* Card Action Buttons */
    .btn-action {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-drop { display: none; color: #DC2626; }
    .btn-drop:hover { color: #B91C1C; }
    .btn-select {
        display: inline-flex;
        background: #1B3679;
        color: white;
        padding: 8px 16px;
        border-radius: 99px;
    }
    .btn-select:hover { background: #11235A; transform: translateY(-1px); }
    .btn-full { color: #9CA3AF; background: #F3F4F6; padding: 8px 16px; border-radius: 99px; cursor: not-allowed; }

    .is-selected .btn-drop { display: inline-flex; }
    .is-selected .btn-select { display: none; }

    /* Sticky Bottom Footer */
    .sticky-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 20px 48px;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        display: flex;
        justify-content: flex-end;
        z-index: 100;
        border-top: 1px solid #F3F4F6;
    }
    .btn-submit {
        background: #1B3679;
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(27,54,121,0.2);
        transition: all 0.2s;
    }
    .btn-submit:hover:not(:disabled) { background: #0B1E4F; transform: translateY(-2px); }
    .btn-submit:disabled { background: #9CA3AF; cursor: not-allowed; box-shadow: none; }

    @media(max-width: 1024px) {
        .main-layout { grid-template-columns: 1fr; }
        .sidebar-card { display: flex; align-items: center; justify-content: space-between; padding: 24px; }
        .sc-progress, .sc-footer { display: none; }
        .sc-phase { margin-bottom: 0; }
        .header-area { flex-direction: column; }
        .top-stats { width: 100%; justify-content: space-between; }
        .sticky-footer { padding: 16px 24px; }
    }
</style>
@endpush

@section('content')
@php $currentPage = 'krs'; @endphp

<div class="krs-container">
    <div class="header-area">
        <div class="header-text">
            <h1 class="page-title">Course Enrollment</h1>
            <p class="page-subtitle">Curate your academic journey for the upcoming Odd Semester. Please ensure your credit balance remains within the permitted limit.</p>
        </div>
        
        <div class="top-stats">
            <div class="ts-card">
                <div class="ts-title">MAX CREDITS</div>
                <div class="ts-value">{{ $maxSks }} <span>SKS</span></div>
            </div>
            <div class="ts-card">
                <div class="ts-title">SELECTED</div>
                <div class="ts-value"><span id="selected-sks-counter" style="color:#1B3679;font-size:32px;font-weight:800;margin:0;">{{ $currentSks }}</span> <span style="margin-left:6px;">SKS</span></div>
            </div>
            <div class="ts-card">
                <div class="ts-title">BALANCE</div>
                <div class="ts-value"><span id="balance-sks-counter" style="color:#1B3679;font-size:32px;font-weight:800;margin:0;">{{ $maxSks - $currentSks }}</span> <span style="margin-left:6px;">SKS</span></div>
            </div>
        </div>
    </div>

    @if($isKrsLocked)
    <div style="background:#ECFDF5; border:1px solid #A7F3D0; padding:16px 24px; border-radius:12px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
        <div style="color:#065F46; font-weight:600;"><i class="bi bi-check-circle-fill" style="margin-right:8px;"></i> KRS Anda telah disetujui secara permanen.</div>
        <div style="display:flex; gap:12px;">
            <a href="{{ route('mahasiswa.krs') }}?edit=1" style="color:#059669; font-weight:700; text-decoration:none; font-size:14px;"><i class="bi bi-pencil"></i> Revisi KRS</a>
            <a href="#" onclick="window.print()" style="color:#059669; font-weight:700; text-decoration:none; font-size:14px;"><i class="bi bi-printer"></i> Cetak PDF</a>
        </div>
    </div>
    @endif

    <div class="main-layout">
        <div class="sidebar-col">
            <div class="sidebar-card">
                <div class="sc-title">ENROLLMENT STATUS</div>
                <div class="sc-phase">Phase 1: Open</div>
                <div class="sc-progress"><div class="sc-bar"></div></div>
                <div class="sc-footer">Ends in 2 days (Oct 15, 2023)</div>
            </div>
            <!-- Extra sidebar elements omitted to match backend logic functionality -->
        </div>

        <div class="main-col">
            <div class="toolbar">
                <div class="toolbar-text">Showing <strong>{{ count($availableCourses) }}</strong> courses available</div>
                <div class="toolbar-icons">
                    <div class="icon-btn"><i class="bi bi-grid-fill"></i></div>
                    <div class="icon-btn inactive"><i class="bi bi-list-ul"></i></div>
                </div>
            </div>

            <div class="courses-grid">
                @foreach($availableCourses as $course)
                    @php 
                        $isSelected = isset($selectedJadwal[$course->id_jadwal]); 
                        $isFull = $course->sks_terdaftar >= $course->kuota;
                        
                        // Generates Avatar Initials
                        $initials = strtoupper(substr($course->nama_dosen, 0, 1));
                        $parts = explode(' ', $course->nama_dosen);
                        if(count($parts) > 1 && strlen($parts[1]) > 0) {
                            $second = substr($parts[1], 0, 1);
                            if(ctype_alpha($second)) $initials .= strtoupper($second);
                        }
                    @endphp
                    <div class="course-card {{ $isSelected ? 'is-selected' : '' }}" data-sks="{{ $course->sks }}">
                        <!-- Hidden checkbox for logic tracking -->
                        <input type="checkbox" class="course-checkbox" style="display:none;" value="{{ $course->id_jadwal }}" {{ $isSelected ? 'checked' : '' }}>
                        
                        <div class="cc-header">
                            <span class="cc-code">{{ $course->kode_matkul }}</span>
                            <span class="cc-pill selected-pill">SELECTED</span>
                        </div>
                        
                        <h3 class="cc-title">{{ $course->nama_matkul }}</h3>
                        
                        <div class="cc-meta">
                            <span><i class="bi bi-journal-text"></i> {{ $course->sks }} Credits</span>
                            <span><i class="bi bi-people"></i> {{ $course->sks_terdaftar }}/{{ $course->kuota }} Slots</span>
                        </div>
                        
                        <div class="cc-time">
                            <i class="bi bi-clock"></i> {{ substr($course->hari, 0, 3) }}, {{ substr($course->jam_mulai,0,5) }} - {{ substr($course->jam_selesai,0,5) }} &bull; {{ $course->ruang }}
                        </div>
                        
                        <div class="cc-footer">
                            <div class="cc-lecturer">
                                <div class="cc-avatar">{{ $initials }}</div>
                                <span>{{ $course->nama_dosen }}</span>
                            </div>
                            
                            @if($isKrsLocked)
                                @if($isSelected)
                                    <span style="color:#059669; font-weight:700; font-size:13px;"><i class="bi bi-check-circle"></i> Enrolled</span>
                                @endif
                            @else
                                @if($isFull && !$isSelected)
                                    <button type="button" class="btn-action btn-full" disabled><i class="bi bi-slash-circle"></i> Class Full</button>
                                @else
                                    <!-- Both buttons rendered, toggled via CSS based on parent .is-selected -->
                                    <button type="button" class="btn-action btn-drop" onclick="toggleCourse(this)">
                                        <i class="bi bi-dash-circle"></i> Drop Course
                                    </button>
                                    <button type="button" class="btn-action btn-select" onclick="toggleCourse(this)">
                                        <i class="bi bi-plus-circle"></i> Select Course
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if(!$isKrsLocked)
<div class="sticky-footer">
    <button class="btn-submit" id="btn-save"><i class="bi bi-cloud-arrow-up"></i> Submit Enrollment</button>
</div>
@endif

@endsection

@push('scripts')
@if(!$isKrsLocked)
<script>
const maxSKS = {{ $maxSks }};
const saveUrl = '{{ route('mahasiswa.krs.save') }}';
const csrfToken = '{{ csrf_token() }}';

function toggleCourse(btn) {
    const card = btn.closest('.course-card');
    const cb = card.querySelector('.course-checkbox');
    const sks = parseInt(card.dataset.sks);
    
    // Calculate current total
    let currentTotal = 0;
    document.querySelectorAll('.course-checkbox:checked').forEach(c => {
        currentTotal += parseInt(c.closest('.course-card').dataset.sks);
    });
    
    if (!cb.checked) {
        // Trying to select
        if (currentTotal + sks > maxSKS) {
            alert('Cannot select course. Credit limit exceeded!');
            return;
        }
        cb.checked = true;
        card.classList.add('is-selected');
    } else {
        // Trying to drop
        cb.checked = false;
        card.classList.remove('is-selected');
    }
    
    updateStats();
}

function updateStats() {
    let currentTotal = 0;
    document.querySelectorAll('.course-checkbox:checked').forEach(c => {
        currentTotal += parseInt(c.closest('.course-card').dataset.sks);
    });
    
    document.getElementById('selected-sks-counter').textContent = currentTotal;
    document.getElementById('balance-sks-counter').textContent = maxSKS - currentTotal;
}

document.getElementById('btn-save').addEventListener('click', function() {
    const selectedJadwal = Array.from(document.querySelectorAll('.course-checkbox:checked')).map(cb => cb.value);
    
    if (selectedJadwal.length === 0) { 
        alert('Please select at least one course.'); 
        return; 
    }
    
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
    
    fetch(saveUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ jadwal_ids: selectedJadwal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) { 
            alert('Enrollment submitted successfully!'); 
            location.reload(); 
        } else { 
            alert('Failed: ' + (data.errors ? data.errors.join(', ') : 'Unknown error')); 
            this.disabled = false; 
            this.innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Submit Enrollment'; 
        }
    })
    .catch(err => { 
        alert('Error: ' + err.message); 
        this.disabled = false; 
        this.innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Submit Enrollment'; 
    });
});

// Initialize
updateStats();
</script>
@endif
@endpush
