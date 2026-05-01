<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen     = Dosen::count();
        $totalMatkul    = MataKuliah::count();
        $semesterAktif  = Semester::aktif();

        $totalKrs = 0;
        $totalKrsTarget = 1;
        if ($semesterAktif) {
            $totalKrs = DB::table('krs as k')
                ->join('jadwal_kuliah as jk', 'k.id_jadwal', '=', 'jk.id_jadwal')
                ->where('jk.id_semester', $semesterAktif->id_semester)
                ->count();
            $totalKrsTarget = max(1, Mahasiswa::where('status','aktif')->count());
        }

        return view('admin.dashboard', compact('admin','totalMahasiswa','totalDosen','totalMatkul','semesterAktif','totalKrs','totalKrsTarget'));
    }
}
