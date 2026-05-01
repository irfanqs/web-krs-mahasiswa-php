@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')
@php $currentPage = 'dashboard'; @endphp

<h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;">Dashboard Admin</h1>
<p style="color:#6B7489;font-size:14px;margin-bottom:24px;">Selamat datang, {{ $admin->nama }}</p>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <x-stat-card label="Total Mahasiswa" :value="$totalMahasiswa" link="admin.mahasiswa.index" link-text="Kelola →" />
    <x-stat-card label="Total Dosen" :value="$totalDosen" link="admin.dosen.index" link-text="Kelola →" />
    <x-stat-card label="Total Mata Kuliah" :value="$totalMatkul" link="admin.matkul.index" link-text="Kelola →" />
    <x-stat-card label="KRS Validation" :value="$totalKrs" sub="Semester aktif" :navy="true">
        @if($semesterAktif)
        <div style="width:100%;background:rgba(255,255,255,.2);border-radius:4px;height:4px;margin-top:12px;">
            <div style="background:white;width:{{ min(100,($totalKrs/max(1,$totalKrsTarget))*100) }}%;height:100%;border-radius:4px;"></div>
        </div>
        @endif
    </x-stat-card>
</div>

<div style="background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
    <div style="font-size:14px;font-weight:700;color:#0B1E4F;margin-bottom:16px;">Quick Access Management</div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('admin.mahasiswa.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-person-badge"></i> Manajemen Mahasiswa</a>
        <a href="{{ route('admin.dosen.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-person-video3"></i> Manajemen Dosen</a>
        <a href="{{ route('admin.semester.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-calendar2-check"></i> Manajemen Semester</a>
        <a href="{{ route('admin.jadwal.index') }}" style="padding:12px 20px;background:#EEF2FF;color:#1B3679;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-calendar3"></i> Manajemen Jadwal</a>
    </div>
</div>

@if($semesterAktif)
<div style="margin-top:16px;background:white;border-radius:16px;padding:20px 24px;box-shadow:0 2px 10px rgba(11,30,79,.06);">
    <div style="font-size:14px;font-weight:700;color:#0B1E4F;margin-bottom:8px;">Semester Aktif</div>
    <div style="font-size:20px;font-weight:700;color:#0B1E4F;">{{ ucfirst($semesterAktif->tingkatan_semester) }} {{ $semesterAktif->tahun_ajaran }}</div>
    <x-badge type="aktif">AKTIF</x-badge>
</div>
@endif
@endsection
