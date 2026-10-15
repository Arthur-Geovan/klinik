<?php

namespace App\Http\Controllers;

use App\Models\MUser;
use App\Models\MPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CLogin extends Controller
{
    // Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $loginValue = trim((string) $request->email);
        $user = MUser::where('email', $loginValue)->first();

        $passwordMatches = $user && (
            Hash::check($request->password, $user->password) ||
            (!empty($user->login_password) && $request->password === $user->login_password)
        );

        if ($passwordMatches) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    // Form Registrasi Pasien
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses Registrasi Pasien
    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'nik'           => 'required|numeric|unique:pasiens,nik',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_telp'       => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $user = MUser::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'login_password' => $request->password,
                'role'          => 'pasien',
            ]);

            $count = MPasien::count() + 1;
            $no_rm = 'RM-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            MPasien::create([
                'user_id'       => $user->id,
                'no_rm'         => $no_rm,
                'nik'           => $request->nik,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat'        => $request->alamat,
                'no_telp'       => $request->no_telp,
            ]);
        });

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}