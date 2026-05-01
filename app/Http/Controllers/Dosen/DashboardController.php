<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $dosen = Auth::guard('dosen')->user();
        $nidn = $dosen->nidn;

        $semesterAktif = Semester::aktif();
        $idSemester = $semesterAktif?->id_semester ?? 0;

        $jumlahMatkul = DB::table('jadwal_kuliah')
            ->where('id_dosen', $nidn)->where('id_semester', $idSemester)->count();

        $jumlahMahasiswa = DB::table('jadwal_kuliah as jk')
            ->join('krs as k', 'jk.id_jadwal', '=', 'k.id_jadwal')
            ->where('jk.id_dosen', $nidn)->where('jk.id_semester', $idSemester)
            ->count();

        $jumlahGrading = DB::table('jadwal_kuliah as jk')
            ->join('krs as k', 'jk.id_jadwal', '=', 'k.id_jadwal')
            ->leftJoin('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('jk.id_dosen', $nidn)->where('jk.id_semester', $idSemester)
            ->whereNotNull('n.id_nilai')->where('n.status_kunci', 0)->count();

        $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        $hariToday = $hariMap[now()->format('l')] ?? '';

        $jadwalHariIni = DB::table('jadwal_kuliah as jk')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->where('jk.id_dosen', $nidn)
            ->where('jk.id_semester', $idSemester)
            ->where('jk.hari', $hariToday)
            ->select('mk.nama_matkul','mk.kode_matkul','mk.sks','jk.jam_mulai','jk.jam_selesai','jk.ruang','jk.id_jadwal','jk.kuota',
                DB::raw('(SELECT COUNT(*) FROM krs WHERE id_jadwal = jk.id_jadwal) as enrolled'))
            ->orderBy('jk.jam_mulai')->get();

        $matkulAktif = DB::table('jadwal_kuliah as jk')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->where('jk.id_dosen', $nidn)->where('jk.id_semester', $idSemester)
            ->select('mk.nama_matkul','mk.kode_matkul','jk.id_jadwal','jk.kuota',
                DB::raw('(SELECT COUNT(*) FROM krs WHERE id_jadwal = jk.id_jadwal) as enrolled'))
            ->get();

        return view('dosen.dashboard', compact('dosen','semesterAktif','jumlahMatkul','jumlahMahasiswa','jumlahGrading','jadwalHariIni','matkulAktif'));
    }
}
