@extends('layouts.app')
@section('title', 'Pengisian KRS')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
<style>
.page-title { font-size:28px; font-weight:700; color:var(--text-900,#0B1E4F); margin-bottom:4px; }
.page-subtitle { color:var(--text-500,#6B7489); font-size:14px; }
.alert-validation { padding:16px; border-radius:12px; margin-bottom:24px; background:#FFF4DC; border-left:4px solid #F4B43C; color:#856404; font-size:14px; }
.alert-locked { background:#E8F5E9; border-left:4px solid #4CAF50; color:#2E7D32; }
.grid-4 { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
.card { background:white; border-radius:16px; padding:20px 24px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.card-navy { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; }
.card-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6B7489; margin-bottom:8px; }
.card-navy .card-title { color:rgba(255,255,255,.7); }
.card-value { font-size:28px; font-weight:700; color:#0B1E4F; }
.card-navy .card-value { color:white; }
.distribution-bar { display:flex; gap:2px; height:8px; border-radius:4px; overflow:hidden; margin-bottom:8px; }
.table-wrapper { background:white; border-radius:16px; box-shadow:0 2px 10px rgba(11,30,79,.06); overflow:hidden; margin-bottom:80px; }
.table { width:100%; border-collapse:collapse; font-size:14px; }
.table thead { background:#F5F6FA; }
.table th { padding:12px 16px; text-align:left; font-weight:600; color:#0B1E4F; border-bottom:2px solid #E4E7EE; }
.table td { padding:12px 16px; border-bottom:1px solid #E4E7EE; }
.table tr:hover { background:#F5F6FA; }
.table-checkbox { width:18px; height:18px; accent-color:#0B1E4F; cursor:pointer; }
.course-row.selected { border-left:4px solid #0B1E4F; background:rgba(42,74,158,.02); }
.course-name { font-weight:600; color:#0B1E4F; }
.badge-wajib { background:#E3F2FD; color:#1976D2; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; margin-left:6px; }
.badge-pilihan { background:#F3E5F5; color:#7B1FA2; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; margin-left:6px; }
.schedule-info { font-size:12px; color:#6B7489; display:flex; gap:12px; flex-wrap:wrap; }
.schedule-item { display:flex; align-items:center; gap:4px; }
.sticky-footer { position:sticky; bottom:0; left:0; width:100%; background:white; border-top:1px solid #E4E7EE; padding:16px 48px; box-shadow:0 -2px 10px rgba(11,30,79,.06); display:flex; justify-content:space-between; align-items:center; gap:24px; z-index:50; }
.footer-progress { display:flex; align-items:center; gap:16px; flex:1; flex-wrap:wrap; }
.progress-text { font-size:13px; color:#2C3A59; font-weight:600; }
.footer-actions { display:flex; gap:12px; }
.btn { padding:10px 16px; border:none; border-radius:10px; font-weight:600; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:8px; font-size:14px; }
.btn-secondary { background:#E4E7EE; color:#0B1E4F; }
.btn-secondary:hover { background:#D0D3DC; }
.btn-primary { background:#0B1E4F; color:white; }
.btn-primary:hover { background:#1C3578; transform:translateY(-2px); }
.btn-primary:disabled { background:#ccc; cursor:not-allowed; transform:none; }
.text-danger { color:#E04F5F; }
@media(max-width:1280px){.grid-4{grid-template-columns:repeat(2,1fr);}}
@media(max-width:768px){.sticky-footer{flex-direction:column;align-items:stretch;padding:16px 24px;}.grid-4{grid-template-columns:1fr;}}
@media print{.sidebar,.topbar,.sticky-footer,.alert-validation,.btn{display:none!important;}.page-layout{margin-left:0!important;}.page-content{padding:0!important;}}
</style>
@endpush

@section('content')
@php $currentPage = 'krs'; @endphp

<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <div>
        <h1 class="page-title">Pengisian KRS</h1>
        <p class="page-subtitle">
            Semester {{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester).' '.$semesterAktif->tahun_ajaran : '-' }}
        </p>
    </div>
    @if($isKrsLocked)
    <div style="display:flex;gap:12px;">
        <a href="{{ route('mahasiswa.krs') }}?edit=1" class="btn btn-secondary"><i class="bi bi-pencil"></i> Revisi KRS</a>
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak KRS</button>
    </div>
    @endif
</div>

<div class="alert-validation {{ $isKrsLocked ? 'alert-locked' : '' }}">
    @if($isKrsLocked)
        <i class="bi bi-check-circle-fill" style="margin-right:8px;"></i>
        <strong>KRS Disetujui:</strong> Anda telah mengambil <strong>{{ $currentSks }} SKS</strong> pada semester ini.
    @else
        <i class="bi bi-info-circle" style="margin-right:8px;"></i>
        <strong>Sistem Validasi:</strong> Batas maksimal SKS berdasarkan IPK semester lalu: <strong>{{ $maxSks }} SKS</strong>.
    @endif
</div>

<div class="grid-4">
    <div class="card card-navy">
        <div class="card-title">SKS Dipilih</div>
        <div class="card-value"><span id="sks-counter">{{ $currentSks }}</span> <span style="font-size:16px;font-weight:normal;opacity:.7;">/ {{ $maxSks }}</span></div>
        <div style="font-size:13px;opacity:.6;margin-top:4px;">Selection progress</div>
    </div>
    <div class="card card-navy">
        <div class="card-title">Total Kursus Dipilih</div>
        <div class="card-value" id="course-counter">{{ count(array_filter((array)$selectedJadwal)) }}</div>
        <div style="font-size:13px;opacity:.6;margin-top:4px;">Selected courses</div>
    </div>
    <div class="card">
        <div class="card-title">Wajib vs Pilihan</div>
        <div class="distribution-bar">
            <div style="background:#0B1E4F;width:{{ $currentSks > 0 ? ($mandatorySks/$currentSks)*100 : 0 }}%;height:100%;border-radius:4px;"></div>
            <div style="background:#F4B43C;width:{{ $currentSks > 0 ? ($electiveSks/$currentSks)*100 : 0 }}%;height:100%;border-radius:4px;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:13px;color:#6B7489;">
            <span>Wajib: {{ $mandatorySks }} SKS</span>
            <span>Pilihan: {{ $electiveSks }} SKS</span>
        </div>
    </div>
    <div class="card">
        <div class="card-title">Kursus Tersedia</div>
        <div class="card-value">{{ $availableCourses->count() }}</div>
        <div style="font-size:13px;color:#6B7489;margin-top:4px;">Total courses offered</div>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th style="width:40px;"></th>
                <th>No</th>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th style="text-align:center;">SKS</th>
                <th>Jadwal & Dosen</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; $hasSelected = false; @endphp
            @foreach($availableCourses as $course)
                @php $isSelected = isset($selectedJadwal[$course->id_jadwal]); @endphp
                @if($isSelected)
                    @php $hasSelected = true; $isFull = $course->sks_terdaftar >= $course->kuota; @endphp
                    <tr class="course-row selected" data-jadwal-id="{{ $course->id_jadwal }}" data-sks="{{ $course->sks }}" data-jenis="{{ $course->jenis }}">
                        <td style="text-align:center;">
                            @if($isKrsLocked)
                                <i class="bi bi-check-circle-fill" style="color:#10B981;font-size:18px;"></i>
                            @else
                                <input type="checkbox" class="table-checkbox course-checkbox" value="{{ $course->id_jadwal }}" checked>
                            @endif
                        </td>
                        <td>{{ $no++ }}</td>
                        <td><strong>{{ $course->kode_matkul }}</strong></td>
                        <td>
                            <div class="course-name">
                                {{ $course->nama_matkul }}
                                <span class="badge-{{ $course->jenis }}">{{ ucfirst($course->jenis) }}</span>
                            </div>
                        </td>
                        <td style="text-align:center;"><strong>{{ $course->sks }}</strong></td>
                        <td>
                            <div class="schedule-info">
                                <div class="schedule-item"><i class="bi bi-calendar3"></i> {{ $course->hari }} {{ substr($course->jam_mulai,0,5) }}-{{ substr($course->jam_selesai,0,5) }}</div>
                                <div class="schedule-item"><i class="bi bi-geo-alt"></i> {{ $course->ruang }}</div>
                                <div class="schedule-item"><i class="bi bi-person"></i> {{ $course->nama_dosen }}</div>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach

            @if(!$isKrsLocked && $hasSelected)
                <tr style="background:#F3F4F6;">
                    <td colspan="6" style="padding:16px 24px;font-weight:700;color:#0B1E4F;font-size:12px;letter-spacing:1px;text-transform:uppercase;">
                        MATA KULIAH TERSEDIA (BELUM DIAMBIL)
                    </td>
                </tr>
            @endif

            @if(!$isKrsLocked)
                @foreach($availableCourses as $course)
                    @php $isSelected = isset($selectedJadwal[$course->id_jadwal]); @endphp
                    @if(!$isSelected)
                        @php $isFull = $course->sks_terdaftar >= $course->kuota; @endphp
                        <tr class="course-row" data-jadwal-id="{{ $course->id_jadwal }}" data-sks="{{ $course->sks }}" data-jenis="{{ $course->jenis }}">
                            <td>
                                <input type="checkbox" class="table-checkbox course-checkbox" value="{{ $course->id_jadwal }}" {{ $isFull ? 'disabled' : '' }}>
                            </td>
                            <td>{{ $no++ }}</td>
                            <td><strong>{{ $course->kode_matkul }}</strong></td>
                            <td>
                                <div class="course-name">
                                    {{ $course->nama_matkul }}
                                    <span class="badge-{{ $course->jenis }}">{{ ucfirst($course->jenis) }}</span>
                                </div>
                            </td>
                            <td style="text-align:center;"><strong>{{ $course->sks }}</strong></td>
                            <td>
                                <div class="schedule-info">
                                    <div class="schedule-item"><i class="bi bi-calendar3"></i> {{ $course->hari }} {{ substr($course->jam_mulai,0,5) }}-{{ substr($course->jam_selesai,0,5) }}</div>
                                    <div class="schedule-item"><i class="bi bi-geo-alt"></i> {{ $course->ruang }}</div>
                                    <div class="schedule-item"><i class="bi bi-person"></i> {{ $course->nama_dosen }}</div>
                                    @if($isFull)<div class="schedule-item text-danger"><i class="bi bi-exclamation-triangle"></i> Penuh</div>@endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
@if(!$isKrsLocked)
<div class="sticky-footer">
    <div class="footer-progress">
        <span class="progress-text">IPK: <strong>{{ number_format($ipk, 2) }}</strong></span>
        <span class="progress-text">BATAS: <strong>{{ $maxSks }}</strong> SKS</span>
    </div>
    <div class="footer-actions">
        <button class="btn btn-secondary" id="btn-reset"><i class="bi bi-arrow-clockwise"></i> Reset Selection</button>
        <button class="btn btn-primary" id="btn-save"><i class="bi bi-check-circle"></i> Simpan KRS</button>
    </div>
</div>
<script>
const maxSKS = {{ $maxSks }};
const saveUrl = '{{ route('mahasiswa.krs.save') }}';
const csrfToken = '{{ csrf_token() }}';
const courseCheckboxes = document.querySelectorAll('.course-checkbox');

function updateCounters() {
    let totalSKS = 0, totalCourses = 0;
    courseCheckboxes.forEach(cb => {
        if (cb.checked) {
            totalSKS += parseInt(cb.closest('.course-row').dataset.sks);
            totalCourses++;
        }
    });
    document.getElementById('sks-counter').textContent = totalSKS;
    document.getElementById('course-counter').textContent = totalCourses;
    courseCheckboxes.forEach(cb => {
        if (!cb.checked) {
            const sks = parseInt(cb.closest('.course-row').dataset.sks);
            const isFull = cb.closest('.course-row').querySelector('.text-danger') !== null;
            cb.disabled = isFull || (totalSKS + sks > maxSKS);
        }
        cb.closest('.course-row').classList.toggle('selected', cb.checked);
    });
}

courseCheckboxes.forEach(cb => cb.addEventListener('change', updateCounters));

document.getElementById('btn-reset').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin mereset seluruh pilihan?')) {
        courseCheckboxes.forEach(cb => { cb.checked = false; });
        updateCounters();
    }
});

document.getElementById('btn-save').addEventListener('click', function() {
    const selectedJadwal = Array.from(courseCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    if (selectedJadwal.length === 0) { alert('Silakan pilih minimal satu mata kuliah'); return; }
    this.disabled = true;
    this.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
    fetch(saveUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ jadwal_ids: selectedJadwal })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) { alert('KRS berhasil disimpan!'); location.reload(); }
        else { alert('Gagal: ' + (data.errors ? data.errors.join(', ') : 'Unknown error')); this.disabled = false; this.innerHTML = '<i class="bi bi-check-circle"></i> Simpan KRS'; }
    })
    .catch(err => { alert('Error: ' + err.message); this.disabled = false; this.innerHTML = '<i class="bi bi-check-circle"></i> Simpan KRS'; });
});

updateCounters();
</script>
@endif
@endpush
