@extends('layouts.app')
@section('title', 'Edit Mahasiswa')
@section('content')
@php $currentPage = 'mahasiswa'; @endphp

<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.mahasiswa.index') }}" style="color:#6B7489;text-decoration:none;"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Edit Mahasiswa</h1>
</div>

<div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 10px rgba(11,30,79,.06);max-width:600px;">
    <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa->nim) }}">
        @csrf @method('PUT')
        @foreach(['nama'=>['Nama Lengkap','text',''],'email'=>['Email','email',''],'angkatan'=>['Angkatan','number',''],'program_studi'=>['Program Studi','text','']] as $field => [$label,$type,$placeholder])
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $mahasiswa->$field) }}" placeholder="{{ $placeholder }}"
                style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has($field)?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @error($field)<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        @endforeach
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Password Baru (kosongkan jika tidak diubah)</label>
            <input type="password" name="password" style="width:100%;padding:12px 14px;border:1px solid #E4E7EE;border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
        </div>
        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Status</label>
            <select name="status" style="width:100%;padding:12px 14px;border:1px solid #E4E7EE;border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @foreach(['aktif','cuti','lulus'] as $s)
                    <option value="{{ $s }}" {{ (old('status',$mahasiswa->status)===$s)?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">
            <i class="bi bi-check-circle"></i> Update
        </button>
    </form>
</div>
@endsection
