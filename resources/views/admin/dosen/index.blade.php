@extends('layouts.app')
@section('title', 'Manajemen Dosen')
@section('content')
@php $currentPage = 'dosen'; @endphp
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Manajemen Dosen</h1>
    <a href="{{ route('admin.dosen.create') }}" style="padding:10px 20px;background:#0B1E4F;color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-plus-circle"></i> Tambah Dosen</a>
</div>
<form method="GET" style="margin-bottom:16px;">
    <input name="search" value="{{ request('search') }}" placeholder="Cari NIDN atau nama..." style="padding:10px 16px;border:1px solid #E4E7EE;border-radius:10px;font-size:14px;width:300px;">
    <button type="submit" style="padding:10px 16px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;margin-left:8px;">Cari</button>
</form>
<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">NIDN</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Nama</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Jurusan</th>
                <th style="padding:12px 16px;text-align:left;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Email</th>
                <th style="padding:12px 16px;text-align:center;font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dosenList as $d)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $d->nidn }}</strong></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $d->nama }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $d->jurusan }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $d->email }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <a href="{{ route('admin.dosen.edit', $d->nidn) }}" style="padding:5px 12px;background:#EEF2FF;color:#1B3679;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;margin-right:4px;"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.dosen.destroy', $d->nidn) }}" style="display:inline;" onsubmit="return confirm('Hapus dosen ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 12px;background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada data dosen.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">{{ $dosenList->links() }}</div>
</div>
@endsection
