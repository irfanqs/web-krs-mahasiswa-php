@extends('layouts.app')
@section('title', 'Manajemen Semester')
@section('content')
@php $currentPage = 'semester'; @endphp
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Manajemen Semester</h1>
    <a href="{{ route('admin.semester.create') }}" style="padding:10px 20px;background:#0B1E4F;color:white;border-radius:10px;text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-plus-circle"></i> Tambah Semester</a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div style="background:white;border-radius:16px;box-shadow:0 2px 10px rgba(11,30,79,.06);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead style="background:#F5F6FA;">
            <tr>
                @foreach(['Tahun Ajaran','Semester','Status','Aksi'] as $h)
                <th style="padding:12px 16px;text-align:{{ $loop->last?'center':'left' }};font-weight:600;color:#0B1E4F;border-bottom:2px solid #E4E7EE;">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($semesterList as $s)
            <tr>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;"><strong>{{ $s->tahun_ajaran }}</strong></td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">{{ ucfirst($s->tingkatan_semester) }}</td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;">
                    <x-badge :type="$s->status" />
                </td>
                <td style="padding:12px 16px;border-bottom:1px solid #E4E7EE;text-align:center;">
                    <a href="{{ route('admin.semester.edit', $s->id_semester) }}" style="padding:5px 12px;background:#EEF2FF;color:#1B3679;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;margin-right:4px;"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('admin.semester.destroy', $s->id_semester) }}" style="display:inline;" onsubmit="return confirm('Hapus semester ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="padding:5px 12px;background:#FEF2F2;color:#DC2626;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:32px;text-align:center;color:#6B7489;">Tidak ada data semester.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:16px 20px;">{{ $semesterList->links() }}</div>
</div>
@endsection
