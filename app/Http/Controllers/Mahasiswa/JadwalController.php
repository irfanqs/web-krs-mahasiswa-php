<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nim = $mahasiswa->nim;

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->get();
        $selectedSemesterId = $request->input('semester_id');
        $semesterSelected = $selectedSemesterId
            ? Semester::find($selectedSemesterId)
            : Semester::aktif() ?? $semesters->first();

        $jadwalList = collect();
        $totalSks = 0;

        if ($semesterSelected) {
            $jadwalList = DB::table('krs as k')
                ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->join('dosen as d', 'jk.id_dosen', '=', 'd.nidn')
                ->where('k.id_mahasiswa', $nim)
                ->where('jk.id_semester', $semesterSelected->id_semester)
                ->select('mk.nama_matkul', 'mk.kode_matkul', 'mk.sks', 'mk.jenis', 'jk.hari', 'jk.jam_mulai', 'jk.jam_selesai', 'jk.ruang', 'd.nama as nama_dosen')
                ->orderByRaw("FIELD(jk.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu')")
                ->orderBy('jk.jam_mulai')
                ->get();

            $totalSks = $jadwalList->sum('sks');
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalByHari = $jadwalList->groupBy('hari');

        $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        $hariToday = $hariMap[now()->format('l')] ?? '';
        $dosenHariIni = $jadwalByHari->get($hariToday, collect());

        return view('mahasiswa.jadwal', compact('mahasiswa','semesters','semesterSelected','jadwalByHari','hariList','totalSks','dosenHariIni'));
    }
}
