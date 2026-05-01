@extends('layouts.app')
@section('title', 'Daftar Mahasiswa')
@section('content')
@php $currentPage = 'daftar_mahasiswa'; @endphp

<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:16px;margin-bottom:24px;">
    <div>
        <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;">Daftar Mahasiswa</h1>
        <p style="color:#6B7489;font-size:14px;">{{ $jadwalInfo ? $jadwalInfo->nama_matkul.' - '.$jadwalInfo->kode_matkul : 'Pilih kelas' }}</p>
    </div>
    <form method="GET">
        <select name="id_jadwal" onchange="this.form.submit()" style="padding:10px 14px;border-radius:10px;border:1px solid #E4E7EE;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @foreach($jadwalDosen as $j)
                <option value="{{ $j->id_jadwal }}" {{ $selectedJadwal == $j->id_jadwal ? 'selected' : '' }}>{{ $j->nama_matkul }} ({{ $j->kode_matkul }})</option>
            @endforeach
        </select>
    </form>
</div>

<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">NIM</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Nama</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Program Studi</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Nilai</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaList as $m)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $m->nim }}</strong></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->nama }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->program_studi }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    @if($m->nilai_huruf)
                        <strong>{{ $m->nilai_huruf }}</strong> ({{ $m->nilai_angka }})
                    @else
                        <span style="color:#9CA3AF;">-</span>
                    @endif
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    @if($m->status_kunci)
                        <span style="background:#E8F5E9;color:#2E7D32;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;">Dikunci</span>
                    @elseif($m->nilai_huruf)
                        <span style="background:#FFF4DC;color:#856404;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;">Draft</span>
                    @else
                        <span style="background:#F3F4F6;color:#6B7489;padding:2px 10px;border-radius:4px;font-size:12px;font-weight:700;">Belum Dinilai</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada mahasiswa terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
