@extends('layouts.app')
@section('title', 'Input Nilai')

@push('styles')
<style>
.page-title{font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;}
.card{background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);}
.card-navy{background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%);color:white;}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
.card-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6B7489;margin-bottom:8px;}
.card-navy .card-title{color:rgba(255,255,255,.7);}
.card-value{font-size:28px;font-weight:700;color:#0B1E4F;}
.card-navy .card-value{color:white;}
.table-wrapper{background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;margin-bottom:80px;}
.table{width:100%;border-collapse:collapse;font-size:14px;}
.table thead{background:#F5F6FA;}
.table th{padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;}
.table td{padding:8px 16px;border-bottom:1px solid #E4E7EE;}
.grade-input{width:70px;padding:6px 8px;border:1px solid #E4E7EE;border-radius:8px;font-family:inherit;font-size:14px;text-align:center;}
.grade-input:focus{outline:none;border-color:#0B1E4F;box-shadow:0 0 0 3px rgba(11,30,79,.1);}
.badge-huruf{display:inline-block;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;}
.badge-A{background:#2BB673;color:white;}.badge-Bplus{background:#F4B43C;color:white;}
.badge-B{background:#2A4A9E;color:white;}.badge-Cplus{background:#F39E45;color:white;}
.badge-C{background:#6B7489;color:white;}.badge-D{background:#E08B5F;color:white;}.badge-E{background:#E04F5F;color:white;}
.sticky-save{position:sticky;bottom:0;left:0;width:100%;background:white;border-top:1px solid #E4E7EE;padding:16px 48px;box-shadow:0 -2px 10px rgba(11,30,79,.06);display:flex;justify-content:flex-end;gap:12px;z-index:50;}
</style>
@endpush

@section('content')
@php $currentPage = 'input_nilai'; @endphp

<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <div>
        <h1 class="page-title">Input Nilai</h1>
        <p style="color:#6B7489;font-size:14px;">{{ $jadwalInfo ? $jadwalInfo->nama_matkul.' ('.$jadwalInfo->kode_matkul.')' : 'Pilih kelas terlebih dahulu' }}</p>
    </div>
    <form method="GET">
        <select name="id_jadwal" onchange="this.form.submit()" style="padding:10px 14px;border-radius:10px;border:1px solid #E4E7EE;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @foreach($jadwalDosen as $j)
                <option value="{{ $j->id_jadwal }}" {{ $selectedJadwal == $j->id_jadwal ? 'selected' : '' }}>
                    {{ $j->nama_matkul }} ({{ $j->kode_matkul }})
                </option>
            @endforeach
        </select>
    </form>
</div>

<div class="grid-3">
    <div class="card">
        <div class="card-title">Total Enrolled</div>
        <div class="card-value">{{ $totalEnrolled }}</div>
    </div>
    <div class="card">
        <div class="card-title">Graded</div>
        <div class="card-value">{{ $graded }}</div>
    </div>
    <div class="card card-navy">
        <div class="card-title">Avg. Score</div>
        <div class="card-value">{{ $avgScore }}</div>
    </div>
</div>

@if($mahasiswaList->isNotEmpty())
<form id="nilai-form">
@csrf
<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>NIM</th>
                <th>Nama Mahasiswa</th>
                <th style="text-align:center;">Tugas (20%)</th>
                <th style="text-align:center;">UTS (30%)</th>
                <th style="text-align:center;">UAS (50%)</th>
                <th style="text-align:center;">Nilai Akhir</th>
                <th style="text-align:center;">Huruf</th>
                <th style="text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mahasiswaList as $m)
            @php
                $locked = $m->status_kunci == 1;
                $badgeClass = match($m->nilai_huruf) { 'A'=>'badge-A','B+'=>'badge-Bplus','B'=>'badge-B','C+'=>'badge-Cplus','C'=>'badge-C','D'=>'badge-D',default=>'badge-E' };
            @endphp
            <tr data-id-krs="{{ $m->id_krs }}">
                <td><strong>{{ $m->nim }}</strong></td>
                <td>{{ $m->nama }}</td>
                <td style="text-align:center;">
                    @if($locked)
                        {{ $m->tugas }}
                    @else
                        <input type="number" class="grade-input tugas-input" value="{{ $m->tugas ?? '' }}" min="0" max="100" step="0.01" {{ $locked ? 'readonly' : '' }}>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($locked)
                        {{ $m->uts }}
                    @else
                        <input type="number" class="grade-input uts-input" value="{{ $m->uts ?? '' }}" min="0" max="100" step="0.01" {{ $locked ? 'readonly' : '' }}>
                    @endif
                </td>
                <td style="text-align:center;">
                    @if($locked)
                        {{ $m->uas }}
                    @else
                        <input type="number" class="grade-input uas-input" value="{{ $m->uas ?? '' }}" min="0" max="100" step="0.01" {{ $locked ? 'readonly' : '' }}>
                    @endif
                </td>
                <td style="text-align:center;"><span class="nilai-angka">{{ $m->nilai_angka ?? '-' }}</span></td>
                <td style="text-align:center;"><span class="nilai-huruf badge-huruf {{ $m->nilai_huruf ? $badgeClass : '' }}">{{ $m->nilai_huruf ?? '-' }}</span></td>
                <td style="text-align:center;">
                    @if($locked)
                        <span style="background:#E8F5E9;color:#2E7D32;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;">Dikunci</span>
                    @else
                        <span style="background:#FFF4DC;color:#856404;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;">Belum Disimpan</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</form>

<div class="sticky-save">
    <button id="btn-save-nilai" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-check-circle"></i> Simpan Perubahan
    </button>
</div>
@else
    <div class="card" style="text-align:center;padding:40px;"><p style="color:#6B7489;">Pilih kelas untuk melihat daftar mahasiswa.</p></div>
@endif
@endsection

@push('scripts')
<script>
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
    const map = {'A':'badge-A','B+':'badge-Bplus','B':'badge-B','C+':'badge-Cplus','C':'badge-C','D':'badge-D','E':'badge-E'};
    return map[huruf] || '';
}
document.querySelectorAll('tr[data-id-krs]').forEach(row => {
    const tugasInput = row.querySelector('.tugas-input');
    const utsInput = row.querySelector('.uts-input');
    const uasInput = row.querySelector('.uas-input');
    if (!tugasInput) return;
    function recalc() {
        const t = parseFloat(tugasInput.value)||0;
        const u = parseFloat(utsInput.value)||0;
        const a = parseFloat(uasInput.value)||0;
        const angka = Math.round((0.2*t + 0.3*u + 0.5*a)*100)/100;
        const huruf = hitungNilaiHuruf(angka);
        row.querySelector('.nilai-angka').textContent = angka.toFixed(2);
        const hurufEl = row.querySelector('.nilai-huruf');
        hurufEl.textContent = huruf;
        hurufEl.className = 'nilai-huruf badge-huruf ' + badgeClass(huruf);
        row.querySelector('span:last-child').textContent = 'Belum Disimpan';
    }
    [tugasInput, utsInput, uasInput].forEach(i => i.addEventListener('input', recalc));
});

document.getElementById('btn-save-nilai')?.addEventListener('click', function() {
    const payload = {};
    document.querySelectorAll('tr[data-id-krs]').forEach(row => {
        const idKrs = row.dataset.idKrs;
        const tugasInput = row.querySelector('.tugas-input');
        if (!tugasInput) return;
        payload[idKrs] = {
            tugas: tugasInput.value,
            uts: row.querySelector('.uts-input').value,
            uas: row.querySelector('.uas-input').value,
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
        if (d.ok) { alert('Nilai berhasil disimpan!'); location.reload(); }
        else { alert('Gagal: ' + (d.errors ? d.errors.join(', ') : 'Error')); }
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle"></i> Simpan Perubahan';
    })
    .catch(e => { alert('Error: ' + e.message); this.disabled = false; this.innerHTML = '<i class="bi bi-check-circle"></i> Simpan Perubahan'; });
});
</script>
@endpush
