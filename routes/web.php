<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboard;
use App\Http\Controllers\Mahasiswa\ProfilController;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\KhsController;
use App\Http\Controllers\Mahasiswa\JadwalController as MahasiswaJadwal;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboard;
use App\Http\Controllers\Dosen\DaftarMahasiswaController;
use App\Http\Controllers\Dosen\InputNilaiController;
use App\Http\Controllers\Dosen\JadwalController as DosenJadwal;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswa;
use App\Http\Controllers\Admin\DosenController as AdminDosen;
use App\Http\Controllers\Admin\MatkulController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwal;
use App\Http\Controllers\BantuanController;

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth — Mahasiswa & Dosen
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Auth — Admin (separate hidden path)
Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'loginAdmin'])->name('admin.login.post');

// ─── MAHASISWA ───────────────────────────────────────────────────────────────
Route::middleware('auth.mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::get('/krs', [KrsController::class, 'index'])->name('krs');
    Route::post('/krs/save', [KrsController::class, 'save'])->name('krs.save');
    Route::post('/krs/enroll', [KrsController::class, 'enroll'])->name('krs.enroll');
    Route::post('/krs/drop', [KrsController::class, 'drop'])->name('krs.drop');
    Route::get('/khs', [KhsController::class, 'index'])->name('khs');
    Route::get('/jadwal', [MahasiswaJadwal::class, 'index'])->name('jadwal');
    Route::get('/bantuan', [BantuanController::class, 'index'])->name('bantuan');
    Route::post('/logout', [LoginController::class, 'logoutMahasiswa'])->name('logout');
});

// ─── DOSEN ───────────────────────────────────────────────────────────────────
Route::middleware('auth.dosen')->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboard::class, 'index'])->name('dashboard');
    Route::get('/daftar-mahasiswa', [DaftarMahasiswaController::class, 'index'])->name('daftar_mahasiswa');
    Route::get('/input-nilai', [InputNilaiController::class, 'index'])->name('input_nilai');
    Route::post('/input-nilai/save', [InputNilaiController::class, 'save'])->name('input_nilai.save');
    Route::get('/jadwal', [DosenJadwal::class, 'index'])->name('jadwal');
    Route::get('/bantuan', [BantuanController::class, 'index'])->name('bantuan');
    Route::post('/logout', [LoginController::class, 'logoutDosen'])->name('logout');
});

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::middleware('auth.admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('mahasiswa', AdminMahasiswa::class)->parameters(['mahasiswa' => 'nim']);
    Route::resource('dosen', AdminDosen::class)->parameters(['dosen' => 'nidn']);
    Route::resource('matkul', MatkulController::class)->parameters(['matkul' => 'id']);
    Route::resource('semester', SemesterController::class)->parameters(['semester' => 'id']);
    Route::resource('jadwal', AdminJadwal::class)->parameters(['jadwal' => 'id']);
    Route::get('/bantuan', [BantuanController::class, 'index'])->name('bantuan');
    Route::post('/logout', [LoginController::class, 'logoutAdmin'])->name('logout');
});
