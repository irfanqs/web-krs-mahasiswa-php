@extends('layouts.app')
@section('title', 'Input Nilai')

@push('styles')
<style>
    .input-nilai-container {
        max-width: 1400px;
        margin: 0 auto;
        padding-bottom: 120px; /* Space for large sticky footer */
    }

    /* Header Section */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 24px;
        margin-bottom: 32px;
    }
    .header-text { max-width: 600px; }
    .academic-term { font-size: 11px; font-weight: 800; color: #1B3679; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #111827;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    .page-subtitle {
        font-size: 15px;
        color: #4B5563;
        line-height: 1.5;
        margin: 0;
    }

    .class-selector {
        background: white;
        border-radius: 12px;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .cs-label { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; }
    .cs-select {
        border: none;
        background: transparent;
        font-size: 15px;
        font-weight: 800;
        color: #1B3679;
        cursor: pointer;
        outline: none;
        font-family: inherit;
    }

    /* Top Cards Grid */
    .top-cards {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    /* Metrics Card */
    .metrics-card {
        background: white;
        border-radius: 20px;
        padding: 32px 40px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .metric-group { display: flex; gap: 48px; }
    .metric-item { display: flex; flex-direction: column; gap: 8px; }
    .m-label { font-size: 10px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; }
    .m-value { font-size: 36px; font-weight: 800; color: #1B3679; line-height: 1; display: flex; align-items: baseline; gap: 8px; }
    .m-sub { font-size: 14px; font-weight: 600; color: #9CA3AF; }
    
    .divider { width: 1px; height: 60px; background: #F3F4F6; }
    
    .avatar-stack { display: flex; align-items: center; }
    .av { width: 36px; height: 36px; border-radius: 50%; border: 2px solid white; background: #E5E7EB; margin-left: -12px; display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 10px; font-weight: 800; color: #1B3679; }
    .av:first-child { margin-left: 0; }
    .av.more { background: #EEF2FF; }

    /* Deadline Card */
    .deadline-card {
        background: linear-gradient(135deg, #1B3679 0%, #11235A 100%);
        border-radius: 20px;
        padding: 32px;
        color: white;
        box-shadow: 0 10px 30px rgba(27,54,121,0.15);
        position: relative;
    }
    .dl-label { font-size: 11px; font-weight: 800; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
    .dl-value { font-size: 28px; font-weight: 800; margin-bottom: 8px; line-height: 1.2; }
    .dl-sub { font-size: 13px; color: rgba(255,255,255,0.6); }
    .dl-icon { position: absolute; top: 32px; right: 32px; font-size: 24px; color: rgba(255,255,255,0.3); }

    /* Table Container */
    .table-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .table-toolbar {
        padding: 24px 32px;
        border-bottom: 1px solid #F3F4F6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .search-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        padding: 10px 16px;
        border-radius: 12px;
        width: 300px;
    }
    .search-box input { border: none; background: transparent; outline: none; font-size: 14px; width: 100%; color: #111827; }
    .search-box i { color: #9CA3AF; }
    
    .toolbar-actions { display: flex; gap: 24px; }
    .tb-action { font-size: 13px; font-weight: 800; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .tb-action:hover { color: #111827; }

    /* Table Styles */
    table { width: 100%; border-collapse: collapse; }
    th {
        padding: 16px 32px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #F3F4F6;
    }
    td {
        padding: 20px 32px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
    }
    
    .col-nim { font-size: 14px; color: #6B7280; font-weight: 500; }
    .col-name { font-size: 15px; font-weight: 800; color: #111827; margin-bottom: 4px; }
    .col-sub { font-size: 12px; color: #9CA3AF; }
    
    .grade-input {
        width: 64px;
        padding: 10px 8px;
        background: #F3F4F6;
        border: 1px solid transparent;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        text-align: center;
        transition: all 0.2s;
    }
    .grade-input:focus { background: white; border-color: #1B3679; outline: none; box-shadow: 0 0 0 3px rgba(27,54,121,0.1); }
    
    .final-score { font-size: 16px; font-weight: 800; color: #1B3679; }
    
    .badge-huruf {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px; height: 32px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
    }
    .b-A { background: #ECFDF5; color: #059669; }
    .b-B { background: #FEF3C7; color: #B45309; }
    .b-C { background: #F3F4F6; color: #4B5563; }
    .b-D { background: #FEF2F2; color: #DC2626; }
    .b-E { background: #FEE2E2; color: #991B1B; }

    .status-text { font-size: 12px; font-weight: 800; color: #9CA3AF; }
    .status-unsaved { color: #2563EB; display: flex; align-items: center; gap: 6px; }
    .status-unsaved::before { content: ''; width: 6px; height: 6px; background: #2563EB; border-radius: 50%; }

    /* Sticky Footer */
    .footer-wrapper {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: white;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        z-index: 100;
    }
    .footer-content {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 40px;
    }
    .info-box {
        display: flex;
        gap: 16px;
        border-left: 4px solid #2563EB;
        padding: 8px 16px;
        background: #EFF6FF;
        border-radius: 0 12px 12px 0;
        flex: 1;
    }
    .info-box i { font-size: 24px; color: #2563EB; }
    .ib-title { font-size: 14px; font-weight: 800; color: #1E3A8A; margin-bottom: 4px; }
    .ib-desc { font-size: 12px; color: #3B82F6; line-height: 1.5; }
    
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
        white-space: nowrap;
    }
    .btn-submit:hover { background: #11235A; transform: translateY(-2px); }

    @media(max-width: 1024px) {
        .top-cards { grid-template-columns: 1fr; }
        .footer-content { flex-direction: column; align-items: stretch; }
    }
</style>
@endpush

@section('content')
@php $currentPage = 'input_nilai'; @endphp

<div class="input-nilai-container">
    <div class="header-section">
        <div class="header-text">
            <div class="academic-term"><i class="bi bi-mortarboard"></i> ACADEMIC YEAR 2023/2024</div>
            <h1 class="page-title">Input Nilai: {{ $jadwalInfo ? $jadwalInfo->nama_matkul : 'Pilih Kelas' }} {{ $jadwalInfo ? '('.$jadwalInfo->kode_matkul.')' : '' }}</h1>
            <p class="page-subtitle">Manage and evaluate student performance for the current semester. Changes are saved locally until submitted to the registrar.</p>
        </div>
        
        <form method="GET" class="class-selector">
            <span class="cs-label">CLASS SELECTION</span>
            <select name="id_jadwal" onchange="this.form.submit()" class="cs-select">
                @if(!$jadwalInfo) <option value="" selected disabled>-- Pilih Kelas --</option> @endif
                @foreach($jadwalDosen as $j)
                    <option value="{{ $j->id_jadwal }}" {{ $selectedJadwal == $j->id_jadwal ? 'selected' : '' }}>
                        {{ $j->nama_matkul }} ({{ $j->kode_matkul }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($jadwalInfo)
    <div class="top-cards">
        <div class="metrics-card">
            <div class="metric-group">
                <div class="metric-item">
                    <span class="m-label">TOTAL ENROLLED</span>
                    <span class="m-value">{{ $totalEnrolled }}</span>
                </div>
                <div class="divider"></div>
                <div class="metric-item">
                    <span class="m-label">GRADED</span>
                    <span class="m-value">{{ $graded }} <span class="m-sub">/ {{ $totalEnrolled }}</span></span>
                </div>
                <div class="divider"></div>
                <div class="metric-item">
                    <span class="m-label">AVG. SCORE</span>
                    <span class="m-value">{{ number_format($avgScore, 1) }}</span>
                </div>
            </div>
            
            <div class="avatar-stack">
                <!-- Mockup avatars for design fidelity -->
                <div class="av"><i class="bi bi-person-fill"></i></div>
                <div class="av"><i class="bi bi-person-fill"></i></div>
                <div class="av"><i class="bi bi-person-fill"></i></div>
                <div class="av more">+{{ max(0, $totalEnrolled - 3) }}</div>
            </div>
        </div>

        <div class="deadline-card">
            <i class="bi bi-stopwatch dl-icon"></i>
            <div class="dl-label">DEADLINE SUBMISSION</div>
            <div class="dl-value">14 Days Remaining</div>
            <div class="dl-sub">Submit by {{ date('F j, Y', strtotime('+14 days')) }}</div>
        </div>
    </div>
    @endif

    @if($mahasiswaList->isNotEmpty())
    <form id="nilai-form">
    @csrf
    <div class="table-container">
        <div class="table-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Filter by Name or NIM...">
            </div>
            <div class="toolbar-actions">
                <div class="tb-action"><i class="bi bi-download"></i> EXPORT XLS</div>
                <div class="tb-action"><i class="bi bi-upload"></i> IMPORT BULK</div>
            </div>
        </div>
        
        <table id="nilaiTable">
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>NAMA MAHASISWA</th>
                    <th style="text-align:center;">TUGAS (20%)</th>
                    <th style="text-align:center;">UTS (30%)</th>
                    <th style="text-align:center;">UAS (50%)</th>
                    <th style="text-align:center;">NILAI AKHIR</th>
                    <th style="text-align:center;">HURUF</th>
                    <th style="text-align:right;">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswaList as $m)
                @php
                    $locked = $m->status_kunci == 1;
                    $bClass = match(substr($m->nilai_huruf,0,1)) { 'A'=>'b-A','B'=>'b-B','C'=>'b-C','D'=>'b-D','E'=>'b-E',default=>'b-C' };
                @endphp
                <tr data-id-krs="{{ $m->id_krs }}" class="student-row">
                    <td class="col-nim">{{ $m->nim }}</td>
                    <td>
                        <div class="col-name">{{ $m->nama }}</div>
                        <div class="col-sub">Regular Class</div>
                    </td>
                    <td style="text-align:center;">
                        @if($locked)
                            <span class="grade-input" style="display:inline-block;">{{ $m->tugas }}</span>
                        @else
                            <input type="number" class="grade-input tugas-input" value="{{ $m->tugas ?? '' }}" min="0" max="100" step="0.01">
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($locked)
                            <span class="grade-input" style="display:inline-block;">{{ $m->uts }}</span>
                        @else
                            <input type="number" class="grade-input uts-input" value="{{ $m->uts ?? '' }}" min="0" max="100" step="0.01">
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @if($locked)
                            <span class="grade-input" style="display:inline-block;">{{ $m->uas }}</span>
                        @else
                            <input type="number" class="grade-input uas-input" value="{{ $m->uas ?? '' }}" min="0" max="100" step="0.01">
                        @endif
                    </td>
                    <td style="text-align:center;"><span class="final-score nilai-angka">{{ $m->nilai_angka ?? '-' }}</span></td>
                    <td style="text-align:center;">
                        @if($m->nilai_huruf)
                            <span class="badge-huruf nilai-huruf {{ $bClass }}">{{ $m->nilai_huruf }}</span>
                        @else
                            <span class="badge-huruf nilai-huruf" style="background:#F3F4F6;color:#9CA3AF;">-</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if($locked)
                            <span class="status-text" style="color:#6B7280;">Saved</span>
                        @else
                            <span class="status-indicator status-text {{ ($m->tugas!==null && $m->uts!==null && $m->uas!==null) ? 'status-unsaved' : '' }}">
                                {{ ($m->tugas!==null && $m->uts!==null && $m->uas!==null) ? 'Unsaved' : 'Saved' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 24px 32px; font-size: 11px; font-weight: 800; color: #9CA3AF; text-transform: uppercase; letter-spacing: 1px; border-top: 1px solid #F3F4F6;">
            SHOWING {{ count($mahasiswaList) }} STUDENTS
        </div>
    </div>
    </form>
    @elseif($jadwalInfo)
        <div class="table-container" style="padding:40px;text-align:center;color:#6B7280;">Tidak ada mahasiswa terdaftar di kelas ini.</div>
    @else
        <div class="table-container" style="padding:60px;text-align:center;color:#6B7280;display:flex;flex-direction:column;align-items:center;gap:16px;">
            <i class="bi bi-inboxes" style="font-size:48px;color:#D1D5DB;"></i>
            <div>Pilih kelas pada dropdown di atas untuk memulai penilaian.</div>
        </div>
    @endif
</div>

@if($jadwalInfo && $mahasiswaList->isNotEmpty())
<div class="footer-wrapper">
    <div class="footer-content">
        <div class="info-box">
            <i class="bi bi-info-circle"></i>
            <div>
                <div class="ib-title">Pedoman Penilaian</div>
                <div class="ib-desc">Nilai Huruf dihitung otomatis berdasarkan akumulasi bobot. Pastikan seluruh komponen nilai telah terisi sebelum melakukan penyimpanan. Komponen nilai akan dikunci secara otomatis pada akhir periode (Final Submission).</div>
            </div>
        </div>
        
        <button id="btn-save-nilai" class="btn-submit" type="button">
            <i class="bi bi-floppy"></i> Simpan Perubahan
        </button>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Search Filter functionality
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('.student-row');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

function hitungNilaiHuruf(angka) {
    if (angka >= 85) return 'A';
    if (angka >= 70) return 'B+';
    if (angka >= 60) return 'B';
    if (angka >= 55) return 'C+';
    if (angka >= 50) return 'C';
    if (angka >= 40) return 'D';
    return 'E';
}
function badgeClass(huruf) {
    if (!huruf) return '';
    const first = huruf.charAt(0);
    const map = {'A':'b-A','B':'b-B','C':'b-C','D':'b-D','E':'b-E'};
    return map[first] || 'b-C';
}

// Live calculation
document.querySelectorAll('.student-row').forEach(row => {
    const inputs = row.querySelectorAll('.grade-input');
    if (inputs.length < 3 || inputs[0].tagName !== 'INPUT') return;
    
    function recalc() {
        const t = parseFloat(inputs[0].value);
        const u = parseFloat(inputs[1].value);
        const a = parseFloat(inputs[2].value);
        
        const isComplete = !isNaN(t) && !isNaN(u) && !isNaN(a);
        
        if (isComplete) {
            const angka = Math.round((0.2*t + 0.3*u + 0.5*a)*100)/100;
            const huruf = hitungNilaiHuruf(angka);
            row.querySelector('.nilai-angka').textContent = angka.toFixed(2);
            const hurufEl = row.querySelector('.nilai-huruf');
            hurufEl.textContent = huruf;
            hurufEl.className = 'badge-huruf nilai-huruf ' + badgeClass(huruf);
            
            // Mark as unsaved
            const statusEl = row.querySelector('.status-indicator');
            if (statusEl) {
                statusEl.textContent = 'Unsaved';
                statusEl.className = 'status-indicator status-text status-unsaved';
            }
        }
    }
    inputs.forEach(i => i.addEventListener('input', recalc));
});

// Save Data
document.getElementById('btn-save-nilai')?.addEventListener('click', function() {
    const payload = {};
    document.querySelectorAll('.student-row').forEach(row => {
        const idKrs = row.dataset.idKrs;
        const inputs = row.querySelectorAll('input.grade-input');
        if (inputs.length < 3) return;
        
        payload[idKrs] = {
            tugas: inputs[0].value,
            uts: inputs[1].value,
            uas: inputs[2].value,
        };
    });
    
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    
    fetch('{{ route('dosen.input_nilai.save') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ nilai: payload })
    })
    .then(r => r.json())
    .then(d => {
        if (d.ok) { 
            alert('Nilai berhasil disimpan!'); 
            location.reload(); 
        } else { 
            alert('Gagal: ' + (d.errors ? d.errors.join(', ') : 'Error')); 
        }
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-floppy"></i> Simpan Perubahan';
    })
    .catch(e => { 
        alert('Error: ' + e.message); 
        this.disabled = false; 
        this.innerHTML = '<i class="bi bi-floppy"></i> Simpan Perubahan'; 
    });
});
</script>
@endpush
