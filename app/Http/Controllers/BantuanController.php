<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class BantuanController extends Controller
{
    public function index()
    {
        if (Auth::guard('mahasiswa')->check()) {
            $role = 'mahasiswa';
            $user = Auth::guard('mahasiswa')->user();
        } elseif (Auth::guard('dosen')->check()) {
            $role = 'dosen';
            $user = Auth::guard('dosen')->user();
        } else {
            $role = 'admin';
            $user = Auth::guard('admin')->user();
        }

        return view('bantuan', compact('role', 'user'));
    }
}
