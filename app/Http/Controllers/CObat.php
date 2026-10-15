<?php

namespace App\Http\Controllers;

use App\Models\MObat;
use Illuminate\Http\Request;

class CObat extends Controller
{
    public function index()
    {
        $this->authorizeMedicineManagement();

        $obats = MObat::all();
        return view('admin.obat.index', compact('obats'));
    }

    public function store(Request $request)
    {
        $this->authorizeMedicineManagement();

        $request->validate([
            'nama_obat' => 'required|string|max:150',
            'satuan'    => 'required|string',
            'stok'      => 'required|integer|min:0',
            'harga'     => 'required|numeric|min:0',
        ]);

        MObat::create($request->all());
        return back()->with('success', 'Obat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeMedicineManagement();

        $request->validate([
            'nama_obat' => 'required|string|max:150',
            'satuan'    => 'required|string',
            'stok'      => 'required|integer|min:0',
            'harga'     => 'required|numeric|min:0',
        ]);

        $obat = MObat::findOrFail($id);
        $obat->update($request->all());
        return back()->with('success', 'Data Obat berhasil diperbarui.');
    }

    public function tambahStok(Request $request, $id)
    {
        $this->authorizeMedicineManagement();

        $request->validate([
            'tambahan_stok' => 'required|integer|min:1',
        ]);

        $obat = MObat::findOrFail($id);
        $obat->stok += $request->tambahan_stok;
        $obat->save();

        return back()->with('success', 'Stok obat berhasil ditambah.');
    }

    public function destroy($id)
    {
        $this->authorizeMedicineManagement();

        MObat::findOrFail($id)->delete();
        return back()->with('success', 'Obat berhasil dihapus.');
    }

    private function authorizeMedicineManagement(): void
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'apoteker'], true), 403);
    }
}