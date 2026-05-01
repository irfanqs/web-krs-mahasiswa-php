<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $semesterAktif = Semester::aktif();
        $selectedSemesterId = $request->input('semester_id', $semesterAktif?->id_semester);
        $semesters = Semester::orderBy('tahun_ajaran','desc')->get();

        $jadwalList = JadwalKuliah::with(['mataKuliah','dosen','semester'])
            ->when($selectedSemesterId, fn($q) => $q->where('id_semester', $selectedSemesterId))
            ->orderByRaw("FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('jam_mulai')
            ->paginate(20)->withQueryString();

        return view('admin.jadwal.index', compact('jadwalList','semesters','selectedSemesterId'));
    }

    public function create()
    {
        $matkulList = MataKuliah::orderBy('nama_matkul')->get();
        $dosenList  = Dosen::orderBy('nama')->get();
        $semesterList = Semester::orderBy('tahun_ajaran','desc')->get();
        return view('admin.jadwal.tambah', compact('matkulList','dosenList','semesterList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_matkul'   => 'required|exists:mata_kuliah,id_matkul',
            'id_dosen'    => 'required|exists:dosen,nidn',
            'id_semester' => 'required|exists:semester,id_semester',
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'ruang'       => 'required|string|max:30',
            'kuota'       => 'required|integer|min:1',
        ]);
        JadwalKuliah::create($request->only(['id_matkul','id_dosen','id_semester','hari','jam_mulai','jam_selesai','ruang','kuota']));
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $matkulList   = MataKuliah::orderBy('nama_matkul')->get();
        $dosenList    = Dosen::orderBy('nama')->get();
        $semesterList = Semester::orderBy('tahun_ajaran','desc')->get();
        return view('admin.jadwal.edit', compact('jadwal','matkulList','dosenList','semesterList'));
    }

    public function update(Request $request, int $id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $request->validate([
            'id_matkul'   => 'required|exists:mata_kuliah,id_matkul',
            'id_dosen'    => 'required|exists:dosen,nidn',
            'id_semester' => 'required|exists:semester,id_semester',
            'hari'        => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'ruang'       => 'required|string|max:30',
            'kuota'       => 'required|integer|min:1',
        ]);
        $jadwal->update($request->only(['id_matkul','id_dosen','id_semester','hari','jam_mulai','jam_selesai','ruang','kuota']));
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        JadwalKuliah::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
