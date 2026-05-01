@extends('layouts.app')
@section('title', 'Edit Mata Kuliah')
@section('content')
@php $currentPage = 'matkul'; @endphp
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.matkul.index') }}" style="color:#6B7489;text-decoration:none;"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Edit Mata Kuliah</h1>
</div>
<div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 10px rgba(11,30,79,.06);max-width:600px;">
    <form method="POST" action="{{ route('admin.matkul.update', $matkul->id_matkul) }}">
        @csrf @method('PUT')
        @foreach(['kode_matkul'=>['Kode Mata Kuliah','text','e.g. TIK-302'],'nama_matkul'=>['Nama Mata Kuliah','text',''],'sks'=>['SKS','number','1-6'],'semester'=>['Semester','number','1-8']] as $field => [$label,$type,$placeholder])
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">{{ $label }}</label>
            <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $matkul->$field) }}" placeholder="{{ $placeholder }}"
                style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has($field)?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
            @error($field)<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        @endforeach
        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Jenis</label>
            <select name="jenis" style="width:100%;padding:12px 14px;border:1px solid #E4E7EE;border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @foreach(['wajib','pilihan'] as $j)
                <option value="{{ $j }}" {{ old('jenis',$matkul->jenis)===$j?'selected':'' }}>{{ ucfirst($j) }}</option>
                @endforeach
            </select>
            @error('jenis')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <button type="submit" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;"><i class="bi bi-check-circle"></i> Update</button>
    </form>
</div>
@endsection
