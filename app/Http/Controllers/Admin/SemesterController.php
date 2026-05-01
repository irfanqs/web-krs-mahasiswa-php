<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index()
    {
        $semesterList = Semester::orderBy('tahun_ajaran', 'desc')->paginate(15);
        return view('admin.semester.index', compact('semesterList'));
    }

    public function create()
    {
        return view('admin.semester.tambah');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun_ajaran'       => 'required|string|max:9',
            'tingkatan_semester' => 'required|in:ganjil,genap',
            'status'             => 'required|in:aktif,nonaktif',
        ]);

        if ($request->status === 'aktif') {
            Semester::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        Semester::create($request->only(['tahun_ajaran','tingkatan_semester','status']));
        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $semester = Semester::findOrFail($id);
        return view('admin.semester.edit', compact('semester'));
    }

    public function update(Request $request, int $id)
    {
        $semester = Semester::findOrFail($id);
        $request->validate([
            'tahun_ajaran'       => 'required|string|max:9',
            'tingkatan_semester' => 'required|in:ganjil,genap',
            'status'             => 'required|in:aktif,nonaktif',
        ]);

        if ($request->status === 'aktif') {
            Semester::where('status', 'aktif')->where('id_semester', '!=', $id)->update(['status' => 'nonaktif']);
        }

        $semester->update($request->only(['tahun_ajaran','tingkatan_semester','status']));
        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        Semester::findOrFail($id)->delete();
        return redirect()->route('admin.semester.index')->with('success', 'Semester berhasil dihapus.');
    }
}
