<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return redirect()->route('mahasiswa.dashboard');
        }
        if (Auth::guard('dosen')->check()) {
            return redirect()->route('dosen.dashboard');
        }
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $role     = $request->input('role', 'mahasiswa');
        $username = $request->input('username');
        $password = $request->input('password');

        if (empty($username) || empty($password)) {
            return back()->with('error', 'Username dan password tidak boleh kosong.')->withInput();
        }

        if ($role === 'mahasiswa') {
            $credentials = ['nim' => $username, 'password' => $password];
            if (Auth::guard('mahasiswa')->attempt($credentials)) {
                $user = Auth::guard('mahasiswa')->user();
                if ($user->status !== 'aktif') {
                    Auth::guard('mahasiswa')->logout();
                    return back()->with('error', 'Akun Anda tidak aktif.')->withInput();
                }
                $request->session()->regenerate();
                return redirect()->route('mahasiswa.dashboard');
            }
        } elseif ($role === 'dosen') {
            $credentials = ['nidn' => $username, 'password' => $password];
            if (Auth::guard('dosen')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('dosen.dashboard');
            }
        } elseif ($role === 'admin') {
            $credentials = ['username' => $username, 'password' => $password];
            if (Auth::guard('admin')->attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->with('error', 'Username atau password salah, atau akun tidak aktif.')->withInput();
    }

    public function logoutMahasiswa(Request $request)
    {
        Auth::guard('mahasiswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function logoutDosen(Request $request)
    {
        Auth::guard('dosen')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function logoutAdmin(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
