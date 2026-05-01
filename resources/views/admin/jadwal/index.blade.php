@extends('layouts.app')
@section('title', 'Manajemen Jadwal Kuliah')
@section('content')
@php $currentPage = 'jadwal'; @endphp
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Manajemen Jadwal Kuliah</h1>
    <a href="{{ route('admin.jadwal.create') }}" style="padding:10px 20px;background:#0B1E4F;color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-plus-circle"></i> Tambah Jadwal</a>
</div>

@if(session('success'))
<div style="background:#D1FAE5;color:#065F46;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;">{{ session('success') }}</div>
@endif

<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                @foreach(['Mata Kuliah','Dosen','Semester','Hari & Waktu','Ruang','Kuota','Aksi'] as $h)
                <th style="padding:12px 16px;text-align:{{ $loop->last?'center':'left' }};font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($jadwalList as $j)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">
                    <strong>{{ $j->mataKuliah->kode_matkul }}</strong><br>
                    <span style="font-size:12px;color:#6B7489;">{{ $j->mataKuliah->nama_matkul }}</span>
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $j->dosen->nama }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">
                    {{ $j->semester->tahun_ajaran }}<br>
                    <span style="font-size:12px;color:#6B7489;">{{ ucfirst($j->semester->tingkatan_semester) }}</span>
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">
                    {{ $j->hari }}<br>
                    <span style="font-size:12px;color:#6B7489;">{{ substr($j->jam_mulai,0,5) }} – {{ substr($j->jam_selesai,0,5) }}</span>
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $j->ruang }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $j->kuota }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <a href="{{ route('admin.jadwal.edit', $j->id_jadwal) }}" style="padding:5px 12px;background:#EEF2FF;color:#1B3679;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;margin-right:4px;"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id_jadwal) }}" style="display:inline;" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 12px;background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada data jadwal kuliah.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">{{ $jadwalList->links() }}</div>
</div>
@endsection
