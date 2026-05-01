<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::query();
        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('nama','like',"%{$search}%")->orWhere('nim','like',"%{$search}%"));
        }
        $mahasiswaList = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('admin.mahasiswa.index', compact('mahasiswaList'));
    }

    public function create()
    {
        return view('admin.mahasiswa.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'           => 'required|string|max:15|unique:mahasiswa,nim',
            'nama'          => 'required|string|max:100',
            'email'         => 'required|email|unique:mahasiswa,email',
            'password'      => 'required|string|min:6',
            'angkatan'      => 'required|digits:4',
            'program_studi' => 'required|string|max:60',
            'status'        => 'required|in:aktif,cuti,lulus',
        ]);

        Mahasiswa::create([
            'nim'           => $request->nim,
            'nama'          => $request->nama,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'angkatan'      => $request->angkatan,
            'program_studi' => $request->program_studi,
            'status'        => $request->status,
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function edit(string $nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, string $nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $request->validate([
            'nama'          => 'required|string|max:100',
            'email'         => "required|email|unique:mahasiswa,email,{$nim},nim",
            'angkatan'      => 'required|digits:4',
            'program_studi' => 'required|string|max:60',
            'status'        => 'required|in:aktif,cuti,lulus',
        ]);

        $data = $request->only(['nama','email','angkatan','program_studi','status']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $mahasiswa->update($data);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroy(string $nim)
    {
        Mahasiswa::findOrFail($nim)->delete();
        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
