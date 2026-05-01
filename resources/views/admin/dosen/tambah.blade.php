@extends('layouts.app')
@section('title', 'Tambah Dosen')
@section('content')
@php $currentPage = 'dosen'; @endphp
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.dosen.index') }}" style="color:#6B7489;text-decoration:none;"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Tambah Dosen</h1>
</div>
<div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 10px rgba(11,30,79,.06);max-width:600px;">
    <form method="POST" action="{{ route('admin.dosen.store') }}">
        @csrf
        @foreach(['nidn'=>'NIDN','nama'=>'Nama Lengkap','email'=>'Email','jurusan'=>'Jurusan'] as $field => $label)
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">{{ $label }}</label>
            <input type="{{ $field==='email'?'email':'text' }}" name="{{ $field }}" value="{{ old($field) }}"
                style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has($field)?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @error($field)<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        @endforeach
        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Password</label>
            <input type="password" name="password" style="width:100%;padding:12px 14px;border:1px solid #E4E7EE;border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;" required>
        </div>
        <button type="submit" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;"><i class="bi bi-check-circle"></i> Simpan</button>
    </form>
</div>
@endsection
