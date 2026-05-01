@extends('layouts.app')
@section('title', 'Academic Profile')

@push('styles')
<style>
.page-title { font-size:28px; font-weight:700; color:#0B1E4F; margin-bottom:4px; }
.grid-2 { display:grid; grid-template-columns:1fr 2fr; gap:24px; margin-bottom:24px; }
.card { background:white; border-radius:16px; padding:20px 24px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.card-navy { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; }
.card-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6B7489; margin-bottom:8px; }
.card-navy .card-title { color:rgba(255,255,255,.7); }
.avatar-circle { width:80px; height:80px; border-radius:50%; background:#0B1E4F; color:white; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:700; margin:0 auto 16px; }
.info-label { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:.5px; }
.info-value { font-size:14px; font-weight:600; color:#0B1E4F; margin-bottom:12px; }
.table { width:100%; border-collapse:collapse; font-size:14px; }
.table thead { background:#F5F6FA; }
.table th { padding:10px 14px; text-align:left; font-weight:600; color:#0B1E4F; border-bottom:2px solid #E4E7EE; font-size:12px; text-transform:uppercase; }
.table td { padding:10px 14px; border-bottom:1px solid #E4E7EE; }
</style>
@endpush

@section('content')
@php $currentPage = 'profil'; @endphp

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 class="page-title">Academic Profile</h1>
    <button onclick="window.print()" style="padding:10px 20px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:14px;">
        <i class="bi bi-printer"></i> Print Transcript
    </button>
</div>

<div class="grid-2">
    <div>
        <div class="card" style="text-align:center;margin-bottom:16px;">
            <div class="avatar-circle">{{ strtoupper(substr($mahasiswa->nama,0,1)) }}</div>
            <div style="font-size:18px;font-weight:700;color:#0B1E4F;">{{ $mahasiswa->nama }}</div>
            <div style="font-size:13px;color:#6B7489;margin-top:4px;">{{ $mahasiswa->nim }}</div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #E4E7EE;">
                <div class="info-label">Program Studi</div>
                <div class="info-value">{{ $mahasiswa->program_studi }}</div>
                <div class="info-label">Angkatan</div>
                <div class="info-value">{{ $mahasiswa->angkatan }}</div>
                <div class="info-label">Status</div>
                <div><span style="display:inline-block;padding:3px 12px;background:#E8F5E9;color:#2E7D32;border-radius:20px;font-size:12px;font-weight:700;">{{ ucfirst($mahasiswa->status) }}</span></div>
            </div>
        </div>
        <div class="card card-navy">
            <div class="card-title">Cumulative GPA (IPK)</div>
            <div style="font-size:40px;font-weight:700;margin-bottom:4px;">{{ number_format($ipk,2) }}</div>
            <div style="font-size:13px;opacity:.6;margin-bottom:16px;">{{ $predikat }}</div>
            <div class="card-title">SKS Earned</div>
            <div style="font-size:24px;font-weight:700;">{{ $sksTempuh }} <span style="font-size:14px;font-weight:normal;opacity:.6;">/ 144</span></div>
        </div>
    </div>
    <div>
        <div class="card" style="margin-bottom:16px;">
            <div class="card-title" style="margin-bottom:12px;">Personal Details</div>
            <div class="info-label">Email</div>
            <div class="info-value">{{ $mahasiswa->email }}</div>
            <div class="info-label">NIM</div>
            <div class="info-value">{{ $mahasiswa->nim }}</div>
            <div class="info-label">Semester Aktif</div>
            <div class="info-value">{{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester).' '.$semesterAktif->tahun_ajaran : '-' }}</div>
        </div>
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">Semester Performance</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Tahun Ajaran</th>
                        <th style="text-align:center;">SKS</th>
                        <th style="text-align:center;">IPS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semesterPerformance as $sp)
                        <tr>
                            <td>{{ ucfirst($sp->tingkatan_semester) }}</td>
                            <td>{{ $sp->tahun_ajaran }}</td>
                            <td style="text-align:center;">{{ $sp->total_sks }}</td>
                            <td style="text-align:center;"><strong>{{ number_format($sp->ips, 2) }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:24px;color:#6B7489;">Belum ada data nilai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
