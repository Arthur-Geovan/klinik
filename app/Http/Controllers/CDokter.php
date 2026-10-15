<?php

namespace App\Http\Controllers;

use App\Models\MDokter;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CDokter extends Controller
{
    public function index()
    {
        $dokters = MDokter::with(['user', 'poli'])->get();
        return view('admin.dokter.index', compact('dokters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'poli_id'      => 'required|exists:polis,id',
            'no_str'       => 'required|string|unique:dokters,no_str',
            'spesialisasi' => 'required|string',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $plainPassword = $request->password;

            $user = MUser::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($plainPassword),
                'login_password' => $plainPassword,
                'role'          => 'dokter',
            ]);

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('dokter', 'public');
            }

            MDokter::create([
                'user_id'      => $user->id,
                'poli_id'      => $request->poli_id,
                'no_str'       => $request->no_str,
                'spesialisasi' => $request->spesialisasi,
                'foto'         => $fotoPath,
            ]);
        });

        return back()->with('success', 'Data Dokter berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $dokter = MDokter::with('user')->findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $dokter->user_id,
            'poli_id'      => 'required|exists:polis,id',
            'no_str'       => 'required|string|unique:dokters,no_str,' . $dokter->id,
            'spesialisasi' => 'required|string',
            'password'     => 'nullable|string|min:6',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $dokter->user;
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $plainPassword = $request->password;
            $user->password = Hash::make($plainPassword);
            $user->login_password = $plainPassword;
        }

        $user->save();

        if ($request->hasFile('foto')) {
            if ($dokter->foto) {
                Storage::disk('public')->delete($dokter->foto);
            }
            $dokter->foto = $request->file('foto')->store('dokter', 'public');
        }

        $dokter->poli_id = $request->poli_id;
        $dokter->no_str = $request->no_str;
        $dokter->spesialisasi = $request->spesialisasi;
        $dokter->save();

        return back()->with('success', 'Data Dokter berhasil diubah.');
    }

    public function destroy($id)
    {
        $dokter = MDokter::findOrFail($id);
        if ($dokter->foto) {
            Storage::disk('public')->delete($dokter->foto);
        }
        $dokter->user->delete();
        return back()->with('success', 'Data Dokter berhasil dihapus.');
    }
}