<?php

namespace App\Http\Controllers;

use App\Models\MPoli;
use Illuminate\Http\Request;

class CPoli extends Controller
{
    public function index()
    {
        $polis = MPoli::all();
        return view('admin.poli.index', compact('polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        MPoli::create($request->all());
        return back()->with('success', 'Data Poli berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $poli = MPoli::findOrFail($id);
        $poli->update($request->all());
        return back()->with('success', 'Data Poli berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MPoli::findOrFail($id)->delete();
        return back()->with('success', 'Data Poli berhasil dihapus.');
    }
}