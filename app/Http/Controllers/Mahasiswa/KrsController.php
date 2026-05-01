<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Semester;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KrsController extends Controller
{
    private function getMaxSks(float $ipk): int
    {
        if ($ipk >= 3.50) return 24;
        if ($ipk >= 3.00) return 22;
        if ($ipk >= 2.50) return 20;
        if ($ipk >= 2.00) return 18;
        return 15;
    }

    public function index(Request $request)
    {
        $mahasiswa = Auth::guard('mahasiswa')->user();
        $nim = $mahasiswa->nim;

        $ipkData = DB::table('krs as k')
            ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('nilai as n', 'k.id_krs', '=', 'n.id_krs')
            ->where('k.id_mahasiswa', $nim)
            ->where('n.status_kunci', 1)
            ->selectRaw('SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks) as total_bobot, SUM(mk.sks) as total_sks')
            ->first();

        $ipk = ($ipkData && $ipkData->total_sks > 0)
            ? round($ipkData->total_bobot / $ipkData->total_sks, 2)
            : 0;

        $semesterAktif = Semester::aktif();
        $idSemester = $semesterAktif?->id_semester ?? 0;

        $availableCourses = DB::table('jadwal_kuliah as jk')
            ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
            ->join('dosen as d', 'jk.id_dosen', '=', 'd.nidn')
            ->where('jk.id_semester', $idSemester)
            ->selectRaw('jk.id_jadwal, mk.id_matkul, mk.kode_matkul, mk.nama_matkul, mk.sks, mk.jenis, mk.semester, jk.hari, jk.jam_mulai, jk.jam_selesai, jk.ruang, d.nama as nama_dosen, jk.kuota, (SELECT COUNT(*) FROM krs WHERE id_jadwal = jk.id_jadwal) as sks_terdaftar')
            ->orderByRaw('mk.semester ASC, mk.nama_matkul ASC')
            ->get();

        $selectedJadwal = DB::table('krs as k')
            ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
            ->where('k.id_mahasiswa', $nim)
            ->where('jk.id_semester', $idSemester)
            ->pluck('jk.id_jadwal')
            ->flip()
            ->all();

        $currentSks = 0;
        $mandatorySks = 0;
        $electiveSks = 0;
        foreach ($availableCourses as $course) {
            if (isset($selectedJadwal[$course->id_jadwal])) {
                $currentSks += $course->sks;
                if ($course->jenis === 'wajib') $mandatorySks += $course->sks;
                else $electiveSks += $course->sks;
            }
        }

        $maxSks = max($this->getMaxSks($ipk), $currentSks);
        $isKrsLocked = $currentSks > 0 && !$request->has('edit');

        return view('mahasiswa.krs', compact(
            'mahasiswa', 'ipk', 'semesterAktif', 'availableCourses',
            'selectedJadwal', 'currentSks', 'mandatorySks', 'electiveSks',
            'maxSks', 'isKrsLocked'
        ));
    }

    public function save(Request $request)
    {
        $nim = Auth::guard('mahasiswa')->user()->nim;
        $jadwalIds = array_map('intval', (array) $request->input('jadwal_ids', []));

        if (empty($jadwalIds)) {
            return response()->json(['ok' => false, 'errors' => ['Pilih minimal satu mata kuliah']]);
        }

        try {
            DB::beginTransaction();

            $semesterAktif = Semester::aktif();
            if (!$semesterAktif) throw new \Exception('Tidak ada semester aktif');
            $idSemester = $semesterAktif->id_semester;

            $ipkData = DB::table('nilai as n')
                ->join('krs as k', 'n.id_krs', '=', 'k.id_krs')
                ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->where('k.id_mahasiswa', $nim)
                ->where('n.status_kunci', 1)
                ->selectRaw('COALESCE(SUM(CASE n.nilai_huruf WHEN "A" THEN 4.0 WHEN "B+" THEN 3.5 WHEN "B" THEN 3.0 WHEN "C+" THEN 2.5 WHEN "C" THEN 2.0 WHEN "D" THEN 1.0 ELSE 0.0 END * mk.sks) / NULLIF(SUM(mk.sks),0), 0) as ipk')
                ->first();

            $ipk = floatval($ipkData->ipk ?? 0);
            $maxSks = $this->getMaxSks($ipk);

            DB::table('krs')->where('id_mahasiswa', $nim)
                ->whereIn('id_jadwal', DB::table('jadwal_kuliah')->where('id_semester', $idSemester)->pluck('id_jadwal'))
                ->delete();

            $jadwalList = DB::table('jadwal_kuliah as jk')
                ->join('mata_kuliah as mk', 'jk.id_matkul', '=', 'mk.id_matkul')
                ->leftJoin(DB::raw('(SELECT id_jadwal, COUNT(*) as cnt FROM krs GROUP BY id_jadwal) as kc'), 'jk.id_jadwal', '=', 'kc.id_jadwal')
                ->whereIn('jk.id_jadwal', $jadwalIds)
                ->where('jk.id_semester', $idSemester)
                ->select('jk.id_jadwal', 'jk.hari', 'jk.jam_mulai', 'jk.jam_selesai', 'jk.kuota', 'mk.sks', 'mk.id_matkul', 'mk.nama_matkul', DB::raw('COALESCE(kc.cnt, 0) as current_enrollment'))
                ->get()
                ->keyBy('id_jadwal');

            $totalSks = 0;
            foreach ($jadwalIds as $id) {
                if (!isset($jadwalList[$id])) throw new \Exception("Jadwal {$id} tidak ditemukan");
                $totalSks += $jadwalList[$id]->sks;
            }

            if ($totalSks > $maxSks) {
                throw new \Exception("Total SKS ({$totalSks}) melebihi batas ({$maxSks})");
            }

            $selected = $jadwalList->whereIn('id_jadwal', $jadwalIds)->values()->all();
            for ($i = 0; $i < count($selected); $i++) {
                for ($j = $i + 1; $j < count($selected); $j++) {
                    $j1 = $selected[$i]; $j2 = $selected[$j];
                    if ($j1->hari === $j2->hari) {
                        if (!(strtotime($j1->jam_selesai) <= strtotime($j2->jam_mulai) || strtotime($j2->jam_selesai) <= strtotime($j1->jam_mulai))) {
                            throw new \Exception("Bentrok jadwal pada hari {$j1->hari}");
                        }
                    }
                }
            }

            foreach ($jadwalIds as $id) {
                if ($jadwalList[$id]->current_enrollment >= $jadwalList[$id]->kuota) {
                    throw new \Exception("Kuota penuh untuk mata kuliah {$jadwalList[$id]->nama_matkul}");
                }
            }

            $selectedMatkul = [];
            foreach ($selected as $j) {
                if (isset($selectedMatkul[$j->id_matkul])) throw new \Exception("Duplikat mata kuliah: {$j->nama_matkul}");
                $selectedMatkul[$j->id_matkul] = true;
            }

            foreach ($jadwalIds as $id) {
                Krs::create(['id_mahasiswa' => $nim, 'id_jadwal' => $id, 'tanggal_ambil' => now()]);
            }

            DB::commit();
            return response()->json(['ok' => true, 'saved' => count($jadwalIds), 'total_sks' => $totalSks, 'message' => "KRS berhasil disimpan ({$totalSks} SKS)"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'errors' => [$e->getMessage()]], 400);
        }
    }
}
