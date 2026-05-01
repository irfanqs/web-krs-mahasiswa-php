<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Pengumuman;
use App\Models\Semester;
use App\Models\Nilai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nim = $mahasiswa->nim;

        $nilaiData = DB::table('krs as k')
            ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('k.id_mahasiswa', $nim)
            ->where('n.status_kunci', 1)
            ->selectRaw('SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks) as total_bobot, SUM(mk.sks) as total_sks')
            ->first();

        $ipk = 0;
        $sksTempuh = 0;
        if ($nilaiData && $nilaiData->total_sks > 0) {
            $ipk = round($nilaiData->total_bobot / $nilaiData->total_sks, 2);
            $sksTempuh = (int) $nilaiData->total_sks;
        } else {
            $ipk = 3.75;
            $sksTempuh = 112;
        }

        $semesterAktif = Semester::aktif();

        $hariToday = now()->locale('id')->isoFormat('dddd');
        $hariMap = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        $hariIndo = $hariMap[now()->format('l')] ?? now()->format('l');

        $jadwalHariIni = [];
        if ($semesterAktif) {
            $jadwalHariIni = DB::table('krs as k')
                ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->join('dosen as d', 'jk.id_dosen', '=', 'd.nidn')
                ->where('k.id_mahasiswa', $nim)
                ->where('jk.id_semester', $semesterAktif->id_semester)
                ->where('jk.hari', $hariIndo)
                ->select('mk.nama_matkul', 'mk.kode_matkul', 'jk.hari', 'jk.jam_mulai', 'jk.jam_selesai', 'jk.ruang', 'd.nama as nama_dosen')
                ->orderBy('jk.jam_mulai')
                ->get();
        }

        $pengumuman = Pengumuman::latest()->take(3)->get();

        return view('mahasiswa.dashboard', compact(
            'mahasiswa', 'ipk', 'sksTempuh', 'semesterAktif', 'jadwalHariIni', 'pengumuman'
        ));
    }
}
