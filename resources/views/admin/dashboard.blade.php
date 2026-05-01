@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
@php $currentPage = 'dashboard'; @endphp

<h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;">Dashboard Admin</h1>
<p style="color:#6B7489;font-size:14px;margin-bottom:24px;">Selamat datang, {{ $admin->nama }}</p>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div style="background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6B7489;margin-bottom:8px;">Total Mahasiswa</div>
        <div style="font-size:28px;font-weight:700;color:#0B1E4F;">{{ $totalMahasiswa }}</div>
        <a href="{{ route('admin.mahasiswa.index') }}" style="font-size:12px;color:#2A4A9E;text-decoration:none;">Kelola →</a>
    </div>
    <div style="background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6B7489;margin-bottom:8px;">Total Dosen</div>
        <div style="font-size:28px;font-weight:700;color:#0B1E4F;">{{ $totalDosen }}</div>
        <a href="{{ route('admin.dosen.index') }}" style="font-size:12px;color:#2A4A9E;text-decoration:none;">Kelola →</a>
    </div>
    <div style="background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6B7489;margin-bottom:8px;">Total Mata Kuliah</div>
        <div style="font-size:28px;font-weight:700;color:#0B1E4F;">{{ $totalMatkul }}</div>
        <a href="{{ route('admin.matkul.index') }}" style="font-size:12px;color:#2A4A9E;text-decoration:none;">Kelola →</a>
    </div>
    <div style="background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%);border-radius:16px;padding:20px 24px;color:white;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;opacity:.7;margin-bottom:8px;">KRS Validation</div>
        <div style="font-size:28px;font-weight:700;">{{ $totalKrs }}</div>
        <div style="font-size:12px;opacity:.6;margin-top:4px;">Semester aktif</div>
        @if($semesterAktif)
            <div style="width:100%;background:rgba(255,255,255,.2);border-radius:4px;height:4px;margin-top:12px;">
                <div style="background:white;width:{{ min(100,($totalKrs/$totalKrsTarget)*100) }}%;height:100%;border-radius:4px;"></div>
            </div>
        @endif
    </div>
</div>

<div style="background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
    <div style="font-size:14px;font-weight:700;color:#0B1E4F;margin-bottom:16px;">Quick Access Management</div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('admin.mahasiswa.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-person-badge"></i> Manajemen Mahasiswa
        </a>
        <a href="{{ route('admin.dosen.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-person-video3"></i> Manajemen Dosen
        </a>
        <a href="{{ route('admin.semester.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-calendar2-check"></i> Manajemen Semester
        </a>
        <a href="{{ route('admin.jadwal.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-calendar3"></i> Manajemen Jadwal
        </a>
    </div>
</div>

@if($semesterAktif)
<div style="margin-top:16px;background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
    <div style="font-size:14px;font-weight:700;color:#0B1E4F;margin-bottom:8px;">Semester Aktif</div>
    <div style="font-size:20px;font-weight:700;color:#0B1E4F;">{{ ucfirst($semesterAktif->tingkatan_semester) }} {{ $semesterAktif->tahun_ajaran }}</div>
    <span style="display:inline-block;margin-top:8px;padding:4px 12px;background:#E8F5E9;color:#2E7D32;border-radius:20px;font-size:12px;font-weight:700;">AKTIF</span>
</div>
@endif
@endsection
