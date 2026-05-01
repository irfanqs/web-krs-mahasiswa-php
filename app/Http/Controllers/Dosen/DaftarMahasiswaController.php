<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DaftarMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        $nidn = $dosen->nidn;
        $semesterAktif = Semester::aktif();
        $idSemester = $semesterAktif?->id_semester ?? 0;

        $jadwalDosen = DB::table('jadwal_kuliah as jk')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->where('jk.id_dosen', $nidn)->where('jk.id_semester', $idSemester)
            ->select('jk.id_jadwal','mk.nama_matkul','mk.kode_matkul')->get();

        $selectedJadwal = $request->input('id_jadwal', $jadwalDosen->first()?->id_jadwal);

        $mahasiswaList = collect();
        $jadwalInfo = null;
        if ($selectedJadwal) {
            $jadwalInfo = DB::table('jadwal_kuliah as jk')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->where('jk.id_jadwal', $selectedJadwal)->first();

            $mahasiswaList = DB::table('krs as k')
                ->join('mahasiswa as m', 'k.id_mahasiswa', '=', 'm.nim')
                ->leftJoin('nilai as n', 'k.id_krs', '=', 'n.id_krs')
                ->where('k.id_jadwal', $selectedJadwal)
                ->select('m.nim','m.nama','m.program_studi','n.nilai_angka','n.nilai_huruf','n.status_kunci')
                ->orderBy('m.nama')->get();
        }

        return view('dosen.daftar_mahasiswa', compact('dosen','jadwalDosen','selectedJadwal','jadwalInfo','mahasiswaList','semesterAktif'));
    }
}
