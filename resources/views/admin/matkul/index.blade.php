@extends('layouts.app')
@section('title', 'Manajemen Mata Kuliah')
@section('content')
@php $currentPage = 'matkul'; @endphp
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Manajemen Mata Kuliah</h1>
    <a href="{{ route('admin.matkul.create') }}" style="padding:10px 20px;background:#0B1E4F;color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-plus-circle"></i> Tambah Matkul</a>
</div>
<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                @foreach(['Kode','Nama Mata Kuliah','SKS','Semester','Jenis','Aksi'] as $h)
                <th style="padding:12px 16px;text-align:{{ $loop->last?'center':'left' }};font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($matkulList as $m)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $m->kode_matkul }}</strong></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->nama_matkul }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->sks }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ $m->semester }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">
                    <x-badge :type="$m->jenis" />
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <a href="{{ route('admin.matkul.edit', $m->id_matkul) }}" style="padding:5px 12px;background:#EEF2FF;color:#1B3679;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;margin-right:4px;"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.matkul.destroy', $m->id_matkul) }}" style="display:inline;" onsubmit="return confirm('Hapus mata kuliah ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 12px;background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada data mata kuliah.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">{{ $matkulList->links() }}</div>
</div>
@endsection
