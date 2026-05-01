<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::query();
        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('nama','like',"%{$search}%")->orWhere('nidn','like',"%{$search}%"));
        }
        $dosenList = $query->orderBy('nama')->paginate(15)->withQueryString();
        return view('admin.dosen.index', compact('dosenList'));
    }

    public function create()
    {
        return view('admin.dosen.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nidn'     => 'required|string|max:15|unique:dosen,nidn',
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|unique:dosen,email',
            'password' => 'required|string|min:6',
            'jurusan'  => 'required|string|max:60',
        ]);

        Dosen::create([
            'nidn'    => $request->nidn,
            'nama'    => $request->nama,
            'email'   => $request->email,
            'password'=> Hash::make($request->password),
            'jurusan' => $request->jurusan,
        ]);

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(string $nidn)
    {
        $dosen = Dosen::findOrFail($nidn);
        return view('admin.dosen.edit', compact('dosen'));
    }

    public function update(Request $request, string $nidn)
    {
        $dosen = Dosen::findOrFail($nidn);
        $request->validate([
            'nama'    => 'required|string|max:100',
            'email'   => "required|email|unique:dosen,email,{$nidn},nidn",
            'jurusan' => 'required|string|max:60',
        ]);
        $data = $request->only(['nama','email','jurusan']);
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);
        $dosen->update($data);
        return redirect()->route('admin.dosen.index')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(string $nidn)
    {
        Dosen::findOrFail($nidn)->delete();
        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }
}
