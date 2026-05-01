@extends('layouts.app')
@section('title', 'Kartu Hasil Studi')

@push('styles')
<style>
.page-title { font-size:28px; font-weight:700; color:#0B1E4F; margin-bottom:4px; }
.page-subtitle { color:#6B7489; font-size:14px; }
.grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px; }
.card { background:white; border-radius:16px; padding:20px 24px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.card-navy { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; }
.card-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6B7489; margin-bottom:8px; }
.card-navy .card-title { color:rgba(255,255,255,.7); }
.card-value { font-size:28px; font-weight:700; color:#0B1E4F; }
.card-navy .card-value { color:white; }
.table-wrapper { background:white; border-radius:16px; box-shadow:0 2px 10px rgba(11,30,79,.06); overflow:hidden; margin-bottom:24px; }
.table { width:100%; border-collapse:collapse; font-size:14px; }
.table thead { background:#F5F6FA; }
.table th { padding:12px 16px; text-align:left; font-weight:600; color:#0B1E4F; border-bottom:2px solid #E4E7EE; }
.table td { padding:12px 16px; border-bottom:1px solid #E4E7EE; }
.badge-huruf { display:inline-block; padding:2px 10px; border-radius:4px; font-size:12px; font-weight:700; }
.badge-A { background:#2BB673; color:white; } .badge-Bplus { background:#F4B43C; color:white; }
.badge-B { background:#2A4A9E; color:white; } .badge-Cplus { background:#F39E45; color:white; }
.badge-C { background:#6B7489; color:white; } .badge-D { background:#E08B5F; color:white; } .badge-E { background:#E04F5F; color:white; }
@media print { .sidebar,.topbar,.print-hide { display:none!important; } .page-layout { margin-left:0!important; } .page-content { padding:0!important; } }
</style>
@endpush

@section('content')
@php $currentPage = 'khs'; @endphp

<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <div>
        <h1 class="page-title">Kartu Hasil Studi</h1>
        <p class="page-subtitle">{{ $semesterSelected ? ucfirst($semesterSelected->tingkatan_semester).' '.$semesterSelected->tahun_ajaran : '-' }}</p>
    </div>
    <div style="display:flex;gap:12px;align-items:center;" class="print-hide">
        <form method="GET">
            <select name="semester_id" onchange="this.form.submit()" style="padding:10px 14px;border-radius:10px;border:1px solid #E4E7EE;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id_semester }}" {{ $semesterSelected?->id_semester == $sem->id_semester ? 'selected' : '' }}>
                        {{ ucfirst($sem->tingkatan_semester) }} {{ $sem->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
        </form>
        <button onclick="window.print()" style="padding:10px 20px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:14px;">
            <i class="bi bi-printer"></i> Cetak KHS
        </button>
    </div>
</div>

<div class="grid-3">
    <div class="card card-navy">
        <div class="card-title">IP Semester</div>
        <div class="card-value">{{ number_format($ips, 2) }}</div>
        <div style="font-size:13px;opacity:.6;margin-top:4px;">Semester ini</div>
    </div>
    <div class="card">
        <div class="card-title">IP Kumulatif (IPK)</div>
        <div class="card-value">{{ number_format($ipk, 2) }}</div>
        <div style="font-size:13px;color:#6B7489;margin-top:4px;">Akumulasi semua semester</div>
    </div>
    <div class="card">
        <div class="card-title">SKS Tempuh</div>
        <div class="card-value">{{ $totalSks }} <span style="font-size:16px;font-weight:normal;color:#6B7489;">/ 144</span></div>
        <div style="font-size:13px;color:#6B7489;margin-top:4px;">SKS semester ini</div>
    </div>
</div>

<div class="table-wrapper">
    <table class="table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Mata Kuliah</th>
                <th style="text-align:center;">SKS</th>
                <th style="text-align:center;">Nilai</th>
                <th style="text-align:center;">Bobot</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nilaiList as $n)
                @php
                    $bobot = match($n->nilai_huruf) { 'A'=>4.0,'B+'=>3.5,'B'=>3.0,'C+'=>2.5,'C'=>2.0,'D'=>1.0,default=>0.0 };
                    $badgeClass = match($n->nilai_huruf) { 'A'=>'badge-A','B+'=>'badge-Bplus','B'=>'badge-B','C+'=>'badge-Cplus','C'=>'badge-C','D'=>'badge-D',default=>'badge-E' };
                @endphp
                <tr>
                    <td><strong>{{ $n->kode_matkul }}</strong></td>
                    <td>{{ $n->nama_matkul }}</td>
                    <td style="text-align:center;">{{ $n->sks }}</td>
                    <td style="text-align:center;">
                        <span class="badge-huruf {{ $badgeClass }}">{{ $n->nilai_huruf ?? '-' }}</span>
                    </td>
                    <td style="text-align:center;">{{ number_format($bobot, 1) }}</td>
                    <td style="text-align:right;">{{ number_format($bobot * $n->sks, 1) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:#6B7489;">Belum ada nilai untuk semester ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background:#F5F6FA;font-weight:700;">
                <td colspan="2">TOTAL</td>
                <td style="text-align:center;">{{ $totalSks }}</td>
                <td colspan="2"></td>
                <td style="text-align:right;">{{ number_format($totalBobot, 1) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div style="display:flex;gap:16px;flex-wrap:wrap;">
    <div class="card" style="flex:1;min-width:200px;">
        <div class="card-title">Status Akademik</div>
        <span style="display:inline-block;padding:4px 14px;background:#E8F5E9;color:#2E7D32;border-radius:20px;font-size:13px;font-weight:700;">AKTIF / MEMUASKAN</span>
    </div>
    <div class="card" style="flex:2;min-width:200px;">
        <div class="card-title">Academic Notice</div>
        <p style="font-size:13px;color:#6B7489;margin:0;">Data KHS ini adalah hasil resmi yang telah diverifikasi oleh dosen dan dikunci oleh sistem akademik. Harap simpan sebagai dokumen resmi Anda.</p>
    </div>
</div>
@endsection
