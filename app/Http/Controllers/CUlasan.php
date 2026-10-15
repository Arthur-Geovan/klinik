<?php

namespace App\Http\Controllers;

use App\Models\MDokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CUlasan extends Controller
{
    public function store(Request $request, $dokter_id)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string',
        ]);

        $dokter = MDokter::findOrFail($dokter_id);

        $dokter->ulasan()->create([
            'user_id'  => Auth::id(),
            'rating'   => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan dan rating yang Anda berikan!');
    }
}