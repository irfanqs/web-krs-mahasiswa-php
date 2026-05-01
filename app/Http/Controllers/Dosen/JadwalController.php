<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        $nidn = $dosen->nidn;
        $semesterAktif = Semester::aktif();
        $idSemester = $semesterAktif?->id_semester ?? 0;

        $jadwalList = DB::table('jadwal_kuliah as jk')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->where('jk.id_dosen', $nidn)->where('jk.id_semester', $idSemester)
            ->select('mk.nama_matkul','mk.kode_matkul','mk.sks','jk.hari','jk.jam_mulai','jk.jam_selesai','jk.ruang','jk.id_jadwal')
            ->orderByRaw("FIELD(jk.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
            ->orderBy('jk.jam_mulai')->get();

        return view('dosen.jadwal', compact('dosen','semesterAktif','jadwalList'));
    }
}
