<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Komunitas;
use Illuminate\Http\Request;

class KomunitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $komunitas = Komunitas::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $komunitas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_komunitas' => 'required|string|max:255',
            'domisili' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_dibentuk' => 'required|date',
            'jumlah_anggota' => 'required|integer|min:0',
            'kontak' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $komunitas = Komunitas::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komunitas berhasil dibuat.',
            'data' => $komunitas,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Komunitas $komunitas)
    {
        return response()->json([
            'success' => true,
            'data' => $komunitas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Komunitas $komunitas)
    {
        $validated = $request->validate([
            'nama_komunitas' => 'required|string|max:255',
            'domisili' => 'required|string|max:255',
            'ketua' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_dibentuk' => 'required|date',
            'jumlah_anggota' => 'required|integer|min:0',
            'kontak' => 'nullable|string|max:255',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $komunitas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Komunitas berhasil diperbarui.',
            'data' => $komunitas,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komunitas $komunitas)
    {
        $komunitas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komunitas berhasil dihapus.',
        ], 200);
    }
}
