@extends('layouts.app')
@section('title', 'Tambah Jadwal Kuliah')
@section('content')
@php $currentPage = 'jadwal'; @endphp
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.jadwal.index') }}" style="color:#6B7489;text-decoration:none;"><i class="bi bi-arrow-left"></i></a>
    <h1 style="font-size:28px;font-weight:700;color:#0B1E4F;margin:0;">Tambah Jadwal Kuliah</h1>
</div>
<div style="background:white;border-radius:16px;padding:28px;box-shadow:0 2px 10px rgba(11,30,79,.06);max-width:600px;">
    <form method="POST" action="{{ route('admin.jadwal.store') }}">
        @csrf
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Mata Kuliah</label>
            <select name="id_matkul" style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('id_matkul')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                <option value="">-- Pilih Mata Kuliah --</option>
                @foreach($matkulList as $m)
                <option value="{{ $m->id_matkul }}" {{ old('id_matkul')==$m->id_matkul?'selected':'' }}>{{ $m->kode_matkul }} — {{ $m->nama_matkul }}</option>
                @endforeach
            </select>
            @error('id_matkul')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Dosen</label>
            <select name="id_dosen" style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('id_dosen')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                <option value="">-- Pilih Dosen --</option>
                @foreach($dosenList as $d)
                <option value="{{ $d->nidn }}" {{ old('id_dosen')===$d->nidn?'selected':'' }}>{{ $d->nama }}</option>
                @endforeach
            </select>
            @error('id_dosen')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Semester</label>
            <select name="id_semester" style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('id_semester')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                <option value="">-- Pilih Semester --</option>
                @foreach($semesterList as $s)
                <option value="{{ $s->id_semester }}" {{ old('id_semester')==$s->id_semester?'selected':'' }}>{{ $s->tahun_ajaran }} {{ ucfirst($s->tingkatan_semester) }} {{ $s->status==='aktif'?'(Aktif)':'' }}</option>
                @endforeach
            </select>
            @error('id_semester')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="margin-bottom:16px;">
            <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Hari</label>
            <select name="hari" style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('hari')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                <option value="">-- Pilih Hari --</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                <option value="{{ $h }}" {{ old('hari')===$h?'selected':'' }}>{{ $h }}</option>
                @endforeach
            </select>
            @error('hari')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Jam Mulai</label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}"
                    style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('jam_mulai')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @error('jam_mulai')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Jam Selesai</label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}"
                    style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('jam_selesai')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @error('jam_selesai')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Ruang</label>
                <input type="text" name="ruang" value="{{ old('ruang') }}" placeholder="e.g. R.402"
                    style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('ruang')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @error('ruang')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#6B7489;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Kuota</label>
                <input type="number" name="kuota" value="{{ old('kuota', 30) }}" min="1"
                    style="width:100%;padding:12px 14px;border:1px solid {{ $errors->has('kuota')?'#E04F5F':'#E4E7EE' }};border-radius:10px;font-family:inherit;font-size:14px;color:#0B1E4F;">
                @error('kuota')<div style="font-size:12px;color:#E04F5F;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" style="padding:12px 24px;background:#0B1E4F;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;"><i class="bi bi-check-circle"></i> Simpan</button>
    </form>
</div>
@endsection
