<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InputNilaiController extends Controller
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
            ->select('jk.id_jadwal', 'mk.nama_matkul', 'mk.kode_matkul')
            ->get();

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
                ->select('m.nim','m.nama','k.id_krs','n.id_nilai','n.tugas','n.uts','n.uas','n.nilai_angka','n.nilai_huruf','n.status_kunci')
                ->orderBy('m.nama')->get();
        }

        $totalEnrolled = $mahasiswaList->count();
        $graded = $mahasiswaList->filter(fn($m) => $m->nilai_angka !== null)->count();
        $avgScore = $graded > 0 ? round($mahasiswaList->filter(fn($m) => $m->nilai_angka !== null)->avg('nilai_angka'), 2) : 0;

        return view('dosen.input_nilai', compact('dosen','jadwalDosen','selectedJadwal','jadwalInfo','mahasiswaList','totalEnrolled','graded','avgScore','semesterAktif'));
    }

    public function save(Request $request)
    {
        $nidn = Auth::guard('dosen')->user()->nidn;
        $data = $request->input('nilai', []);

        try {
            DB::beginTransaction();
            foreach ($data as $idKrs => $row) {
                $jadwal = DB::table('krs as k')
                    ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                    ->where('k.id_krs', $idKrs)->where('jk.id_dosen', $nidn)
                    ->first();
                if (!$jadwal) continue;

                $tugas = floatval($row['tugas'] ?? 0);
                $uts   = floatval($row['uts'] ?? 0);
                $uas   = floatval($row['uas'] ?? 0);
                $nilaiAngka = Nilai::hitungNilaiAngka($tugas, $uts, $uas);
                $nilaiHuruf = Nilai::hitungNilaiHuruf($nilaiAngka);

                Nilai::updateOrCreate(
                    ['id_krs' => $idKrs],
                    ['tugas'=>$tugas,'uts'=>$uts,'uas'=>$uas,'nilai_angka'=>$nilaiAngka,'nilai_huruf'=>$nilaiHuruf,'status_kunci'=>0]
                );
            }
            DB::commit();
            return response()->json(['ok' => true, 'message' => 'Nilai berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'errors' => [$e->getMessage()]], 400);
        }
    }
}
