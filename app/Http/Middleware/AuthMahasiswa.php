<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMahasiswa
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('mahasiswa')->check()) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai mahasiswa terlebih dahulu.');
        }
        return $next($request);
    }
}
