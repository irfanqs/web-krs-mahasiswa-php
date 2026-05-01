@extends('layouts.app')
@section('title', 'Tambah Semester')
@section('content')
@php $currentPage = 'semester'; @endphp
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.semester.index') }}" style="color:#6B7489;text-decoration:none;"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Tambah Semester</h1>
</div>
<div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 10px rgba(11,30,79,.06);max-width:600px;">
    <form method="POST" action="{{ route('admin.semester.store') }}">
        @csrf
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Tahun Ajaran</label>
            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" placeholder="e.g. 2023/2024"
                style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('tahun_ajaran')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @error('tahun_ajaran')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Tingkatan Semester</label>
            <select name="tingkatan_semester" style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('tingkatan_semester')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @foreach(['ganjil','genap'] as $t)
                <option value="{{ $t }}" {{ old('tingkatan_semester')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            @error('tingkatan_semester')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Status</label>
            <select name="status" style="width:100%;padding:12px 14px;border:1px solid #E4E7EE;border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                <option value="nonaktif" {{ old('status','nonaktif')==='nonaktif'?'selected':'' }}>Non-aktif</option>
                <option value="aktif" {{ old('status')==='aktif'?'selected':'' }}>Aktif</option>
            </select>
            <div style="font-size:12px;color:#6B7489;margin-top:4px;">Menjadikan semester aktif akan menonaktifkan semester lain yang sedang aktif.</div>
        </div>
        <button type="submit" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;"><i class="bi bi-check-circle"></i> Simpan</button>
    </form>
</div>
@endsection
