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

// Root redirect
Route::get('/', fn() => redirect()->route('login'));

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// ─── MAHASISWA ───────────────────────────────────────────────────────────────
Route::middleware('auth.mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboard::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::get('/krs', [KrsController::class, 'index'])->name('krs');
    Route::post('/krs/save', [KrsController::class, 'save'])->name('krs.save');
    Route::get('/khs', [KhsController::class, 'index'])->name('khs');
    Route::get('/jadwal', [MahasiswaJadwal::class, 'index'])->name('jadwal');
    Route::post('/logout', [LoginController::class, 'logoutMahasiswa'])->name('logout');
});

// ─── DOSEN ───────────────────────────────────────────────────────────────────
Route::middleware('auth.dosen')->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboard::class, 'index'])->name('dashboard');
    Route::get('/daftar-mahasiswa', [DaftarMahasiswaController::class, 'index'])->name('daftar_mahasiswa');
    Route::get('/input-nilai', [InputNilaiController::class, 'index'])->name('input_nilai');
    Route::post('/input-nilai/save', [InputNilaiController::class, 'save'])->name('input_nilai.save');
    Route::get('/jadwal', [DosenJadwal::class, 'index'])->name('jadwal');
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
    Route::post('/logout', [LoginController::class, 'logoutAdmin'])->name('logout');
});
