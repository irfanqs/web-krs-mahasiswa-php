@extends('layouts.app')
@section('title', 'Jadwal Mengajar')
@section('content')
@php $currentPage = 'jadwal'; @endphp

<h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin-bottom:4px;">Jadwal Mengajar</h1>
<p style="color:#6B7489;font-size:14px;margin-bottom:24px;">{{ $semesterAktif ? ucfirst($semesterAktif->tingkatan_semester).' '.$semesterAktif->tahun_ajaran : '-' }}</p>

<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Mata Kuliah</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Hari</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Waktu</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Ruang</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">SKS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jadwalList as $j)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $j->nama_matkul }}</strong><br><small style="color:#6B7489;">{{ $j->kode_matkul }}</small></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $j->hari }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $j->ruang }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">{{ $j->sks }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada jadwal mengajar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
