<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\Request;

class MatkulController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::query();
        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('nama_matkul','like',"%{$search}%")->orWhere('kode_matkul','like',"%{$search}%"));
        }
        $matkulList = $query->orderBy('semester')->orderBy('nama_matkul')->paginate(15)->withQueryString();
        return view('admin.matkul.index', compact('matkulList'));
    }

    public function create()
    {
        return view('admin.matkul.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_matkul'  => 'required|string|max:10|unique:mata_kuliah,kode_matkul',
            'nama_matkul'  => 'required|string|max:100',
            'sks'          => 'required|integer|min:1|max:6',
            'semester'     => 'required|integer|min:1|max:8',
            'jenis'        => 'required|in:wajib,pilihan',
        ]);
        MataKuliah::create($request->only(['kode_matkul','nama_matkul','sks','semester','jenis']));
        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $matkul = MataKuliah::findOrFail($id);
        return view('admin.matkul.edit', compact('matkul'));
    }

    public function update(Request $request, int $id)
    {
        $matkul = MataKuliah::findOrFail($id);
        $request->validate([
            'kode_matkul'  => "required|string|max:10|unique:mata_kuliah,kode_matkul,{$id},id_matkul",
            'nama_matkul'  => 'required|string|max:100',
            'sks'          => 'required|integer|min:1|max:6',
            'semester'     => 'required|integer|min:1|max:8',
            'jenis'        => 'required|in:wajib,pilihan',
        ]);
        $matkul->update($request->only(['kode_matkul','nama_matkul','sks','semester','jenis']));
        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        MataKuliah::findOrFail($id)->delete();
        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
