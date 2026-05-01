@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@push('styles')
<style>
.page-title{font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;}
.grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
.card{background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);}
.card-navy{background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%);color:white;}
.card-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6B7489;margin-bottom:8px;}
.card-navy .card-title{color:rgba(255,255,255,.7);}
.card-value{font-size:28px;font-weight:700;color:#0B1E4F;}
.card-navy .card-value{color:white;}
.section-title{font-size:16px;font-weight:700;color:#0B1E4F;margin-bottom:16px;}
.session-item{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px;background:#F5F6FA;border-radius:12px;margin-bottom:8px;}
.btn-sm{padding:6px 14px;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;}
.btn-primary-sm{background:#0B1E4F;color:white;}
.btn-outline-sm{background:white;color:#0B1E4F;border:1px solid #E4E7EE;}
</style>
@endpush

@section('content')
@php $currentPage = 'dashboard'; @endphp

<h1 class="page-title">Dashboard Dosen</h1>
<p style="color:#6B7489;font-size:14px;margin-bottom:24px;">Selamat datang, {{ $dosen->nama }}</p>

<div class="grid-3">
    <div class="card">
        <div class="card-title">Mata Kuliah Diampu</div>
        <div class="card-value">{{ $jumlahMatkul }}</div>
        <div style="font-size:13px;color:#6B7489;margin-top:4px;">Semester aktif</div>
    </div>
    <div class="card">
        <div class="card-title">Total Mahasiswa</div>
        <div class="card-value">{{ $jumlahMahasiswa }}</div>
        <div style="font-size:13px;color:#6B7489;margin-top:4px;">Di semua kelas Anda</div>
    </div>
    <div class="card card-navy">
        <div class="card-title">Grading Aktif</div>
        <div class="card-value">{{ $jumlahGrading }}</div>
        <div style="font-size:13px;opacity:.6;margin-top:4px;">Nilai belum dikunci</div>
        <a href="{{ route('dosen.input_nilai') }}" style="display:inline-block;margin-top:12px;padding:8px 16px;background:rgba(255,255,255,.15);color:white;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            Input Nilai Sekarang →
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div>
        <div class="section-title">Jadwal Mengajar Hari Ini</div>
        @forelse($jadwalHariIni as $j)
            <div class="session-item">
                <div>
                    <div style="font-size:14px;font-weight:600;color:#0B1E4F;">{{ $j->nama_matkul }}</div>
                    <div style="font-size:12px;color:#6B7489;"><i class="bi bi-clock"></i> {{ substr($j->jam_mulai,0,5) }}-{{ substr($j->jam_selesai,0,5) }} &bull; <i class="bi bi-geo-alt"></i> {{ $j->ruang }}</div>
                    <div style="font-size:12px;color:#6B7489;"><i class="bi bi-people"></i> {{ $j->enrolled }}/{{ $j->kuota }} mahasiswa</div>
                </div>
                <a href="{{ route('dosen.daftar_mahasiswa') }}?id_jadwal={{ $j->id_jadwal }}" class="btn-sm btn-outline-sm">
                    <i class="bi bi-list-ul"></i> Daftar
                </a>
            </div>
        @empty
            <div class="card"><p style="color:#6B7489;font-size:14px;">Tidak ada jadwal mengajar hari ini.</p></div>
        @endforelse
    </div>
    <div>
        <div class="section-title">Mata Kuliah Aktif</div>
        @forelse($matkulAktif as $m)
            <div class="session-item" style="flex-direction:column;align-items:flex-start;">
                <div style="width:100%;display:flex;justify-content:space-between;align-items:center;">
                    <div style="font-size:14px;font-weight:600;color:#0B1E4F;">{{ $m->nama_matkul }}</div>
                    <span style="font-size:12px;color:#6B7489;">{{ $m->enrolled }}/{{ $m->kuota }}</span>
                </div>
                <div style="width:100%;background:#E4E7EE;border-radius:4px;height:6px;margin-top:8px;">
                    <div style="background:#0B1E4F;width:{{ $m->kuota > 0 ? ($m->enrolled/$m->kuota)*100 : 0 }}%;height:100%;border-radius:4px;"></div>
                </div>
                <div style="display:flex;gap:8px;margin-top:8px;">
                    <a href="{{ route('dosen.daftar_mahasiswa') }}?id_jadwal={{ $m->id_jadwal }}" class="btn-sm btn-outline-sm"><i class="bi bi-people"></i> Daftar</a>
                    <a href="{{ route('dosen.input_nilai') }}?id_jadwal={{ $m->id_jadwal }}" class="btn-sm btn-primary-sm"><i class="bi bi-pencil"></i> Input Nilai</a>
                </div>
            </div>
        @empty
            <div class="card"><p style="color:#6B7489;font-size:14px;">Tidak ada mata kuliah aktif.</p></div>
        @endforelse
    </div>
</div>
@endsection
