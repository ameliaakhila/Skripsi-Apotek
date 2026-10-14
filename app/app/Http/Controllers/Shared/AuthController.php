<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        // Redirect to dashboard if already authenticated
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return response()
            ->view('auth.login')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Check status after verifying credentials
            if (Auth::user()->status !== 'Aktif') {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun tidak aktif',
                ])->onlyInput('username');
            }

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username/Password salah',
        ])->onlyInput('username');
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); // menghapus semua data sesi
        $request->session()->regenerateToken(); // membuat token CSRF baru

        return redirect('/');
    }
}
