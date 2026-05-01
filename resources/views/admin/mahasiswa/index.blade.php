@extends('layouts.app')
@section('title', 'Manajemen Mahasiswa')
@section('content')
@php $currentPage = 'mahasiswa'; @endphp

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Manajemen Mahasiswa</h1>
    <a href="{{ route('admin.mahasiswa.create') }}" style="padding:10px 20px;background:#0B1E4F;color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
    </a>
</div>

<form method="GET" style="margin-bottom:16px;">
    <input name="search" value="{{ request('search') }}" placeholder="Cari NIM atau nama..." style="padding:10px 16px;border:1px solid #E4E7EE;border-radius:10px;font-size:14px;width:300px;">
    <button type="submit" style="padding:10px 16px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;margin-left:8px;">Cari</button>
</form>

<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">NIM</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Nama</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Program Studi</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Angkatan</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Status</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswaList as $m)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $m->nim }}</strong></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->nama }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->program_studi }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">{{ $m->angkatan }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <x-badge :type="$m->status" />
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <a href="{{ route('admin.mahasiswa.edit', $m->nim) }}" style="padding:5px 12px;background:#EEF2FF;color:#1B3679;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;margin-right:4px;"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.mahasiswa.destroy', $m->nim) }}" style="display:inline;" onsubmit="return confirm('Hapus mahasiswa ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 12px;background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada data mahasiswa.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">{{ $mahasiswaList->links() }}</div>
</div>
@endsection
