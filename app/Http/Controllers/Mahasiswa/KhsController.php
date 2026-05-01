<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KhsController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nim = $mahasiswa->nim;

        $semesters = Semester::orderBy('tahun_ajaran', 'desc')->orderByRaw("FIELD(tingkatan_semester,'genap','ganjil')")->get();

        $selectedSemesterId = $request->input('semester_id');
        $semesterSelected = $selectedSemesterId
            ? Semester::find($selectedSemesterId)
            : Semester::aktif() ?? $semesters->first();

        $nilaiList = [];
        $ips = 0;
        $totalBobot = 0;
        $totalSks = 0;

        if ($semesterSelected) {
            $nilaiList = DB::table('krs as k')
                ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->leftJoin('nilai as n', 'k.id_krs', '=', 'n.id_krs')
                ->where('k.id_mahasiswa', $nim)
                ->where('jk.id_semester', $semesterSelected->id_semester)
                ->where('n.status_kunci', 1)
                ->select('mk.kode_matkul', 'mk.nama_matkul', 'mk.sks', 'n.nilai_angka', 'n.nilai_huruf')
                ->get();

            foreach ($nilaiList as $n) {
                $bobot = match($n->nilai_huruf) { 'A'=>4.0,'B+'=>3.5,'B'=>3.0,'C+'=>2.5,'C'=>2.0,'D'=>1.0,default=>0.0 };
                $totalBobot += $bobot * $n->sks;
                $totalSks += $n->sks;
            }
            $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
        }

        $ipkData = DB::table('krs as k')
            ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('k.id_mahasiswa', $nim)->where('n.status_kunci', 1)
            ->selectRaw('SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks) as total_bobot, SUM(mk.sks) as total_sks')
            ->first();

        $ipk = ($ipkData && $ipkData->total_sks > 0) ? round($ipkData->total_bobot / $ipkData->total_sks, 2) : 0;
        $sksTempuh = $ipkData->total_sks ?? 0;

        return view('mahasiswa.khs', compact('mahasiswa','semesters','semesterSelected','nilaiList','ips','ipk','totalSks','totalBobot','sksTempuh'));
    }
}
