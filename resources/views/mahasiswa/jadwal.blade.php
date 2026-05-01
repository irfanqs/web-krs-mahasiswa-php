@extends('layouts.app')
@section('title', 'Weekly Schedule')

@push('styles')
<style>
.page-title { font-size:28px; font-weight:700; color:#0B1E4F; margin-bottom:4px; }
.schedule-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:24px; }
.day-column { background:white; border-radius:12px; padding:12px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.day-header { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#6B7489; padding-bottom:8px; border-bottom:1px solid #E4E7EE; margin-bottom:8px; text-align:center; }
.schedule-card { border-radius:10px; padding:10px 12px; margin-bottom:8px; }
.schedule-card-wajib { background:#EEF2FF; border-left:3px solid #2A4A9E; }
.schedule-card-pilihan { background:#FEF9E7; border-left:3px solid #F4B43C; }
.sc-name { font-size:13px; font-weight:700; color:#0B1E4F; margin-bottom:2px; }
.sc-time { font-size:11px; color:#6B7489; }
.sc-room { font-size:11px; color:#6B7489; }
.sc-dosen { font-size:11px; color:#6B7489; }
.empty-day { text-align:center; padding:20px 0; color:#9CA3AF; font-size:12px; }
.card { background:white; border-radius:16px; padding:20px 24px; box-shadow:0 2px 10px rgba(11,30,79,.06); }
.card-navy { background:linear-gradient(135deg,#0B1E4F 0%,#1C3578 100%); color:white; }
@media(max-width:900px){.schedule-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php $currentPage = 'jadwal'; @endphp

<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <h1 class="page-title">Weekly Schedule</h1>
    <div style="display:flex;gap:12px;align-items:center;">
        <form method="GET">
            <select name="semester_id" onchange="this.form.submit()" style="padding:10px 14px;border-radius:10px;border:1px solid #E4E7EE;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id_semester }}" {{ $semesterSelected?->id_semester == $sem->id_semester ? 'selected' : '' }}>
                        {{ ucfirst($sem->tingkatan_semester) }} {{ $sem->tahun_ajaran }}
                    </option>
                @endforeach
            </select>
        </form>
        <button onclick="window.print()" style="padding:10px 20px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-download"></i> Download
        </button>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:24px;">
    <div>
        <div class="schedule-grid">
            @foreach($hariList as $hari)
                <div class="day-column">
                    <div class="day-header">{{ $hari }}</div>
                    @php $courses = $jadwalByHari->get($hari, collect()); @endphp
                    @forelse($courses as $c)
                        <div class="schedule-card schedule-card-{{ $c->jenis }}">
                            <div class="sc-name">{{ $c->nama_matkul }}</div>
                            <div class="sc-time"><i class="bi bi-clock"></i> {{ substr($c->jam_mulai,0,5) }}-{{ substr($c->jam_selesai,0,5) }}</div>
                            <div class="sc-room"><i class="bi bi-geo-alt"></i> {{ $c->ruang }}</div>
                            <div class="sc-dosen"><i class="bi bi-person"></i> {{ $c->nama_dosen }}</div>
                        </div>
                    @empty
                        <div class="empty-day">Tidak ada jadwal</div>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
    <div>
        <div class="card card-navy" style="margin-bottom:16px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;opacity:.7;margin-bottom:8px;">TOTAL CREDITS</div>
            <div style="font-size:36px;font-weight:700;">{{ $totalSks }}</div>
            <div style="font-size:13px;opacity:.6;margin-top:4px;">SKS terdaftar</div>
        </div>
        <div class="card">
            <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9CA3AF;margin-bottom:12px;">TODAY'S LECTURERS</div>
            @forelse($dosenHariIni as $d)
                <div style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #F0F0F0;">
                    <div style="font-size:13px;font-weight:600;color:#0B1E4F;">{{ $d->nama_dosen }}</div>
                    <div style="font-size:12px;color:#6B7489;">{{ $d->nama_matkul }}</div>
                    <div style="font-size:12px;color:#6B7489;">{{ substr($d->jam_mulai,0,5) }}-{{ substr($d->jam_selesai,0,5) }} · {{ $d->ruang }}</div>
                </div>
            @empty
                <p style="font-size:13px;color:#9CA3AF;">Tidak ada jadwal hari ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
