<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nim = $mahasiswa->nim;

        $ipkData = DB::table('krs as k')
            ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('k.id_mahasiswa', $nim)->where('n.status_kunci', 1)
            ->selectRaw('SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks) as total_bobot, SUM(mk.sks) as total_sks')
            ->first();

        $ipk = ($ipkData && $ipkData->total_sks > 0) ? round($ipkData->total_bobot / $ipkData->total_sks, 2) : 0;
        $sksTempuh = (int)($ipkData->total_sks ?? 0);

        $predikat = match(true) {
            $ipk >= 3.5 => 'Dengan Pujian',
            $ipk >= 3.0 => 'Sangat Memuaskan',
            $ipk >= 2.5 => 'Memuaskan',
            default     => 'Cukup',
        };

        $semesterAktif = Semester::aktif();

        $semesterPerformance = DB::table('semester as s')
            ->join('jadwal_kuliah as jk', 's.id_semester', '=', 'jk.id_semester')
            ->join('krs as k', 'jk.id_jadwal', '=', 'k.id_jadwal')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('k.id_mahasiswa', $nim)->where('n.status_kunci', 1)
            ->groupBy('s.id_semester', 's.tahun_ajaran', 's.tingkatan_semester')
            ->selectRaw('s.tahun_ajaran, s.tingkatan_semester, SUM(mk.sks) as total_sks, SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks)/NULLIF(SUM(mk.sks),0) as ips')
            ->orderBy('s.tahun_ajaran')->orderByRaw("FIELD(s.tingkatan_semester,'ganjil','genap')")
            ->get();

        return view('mahasiswa.profil', compact('mahasiswa','ipk','sksTempuh','predikat','semesterAktif','semesterPerformance'));
    }
}
