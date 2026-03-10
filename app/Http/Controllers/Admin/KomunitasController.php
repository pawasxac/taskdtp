<?php

namespace App\Http\Controllers\Admin;

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
        $data = Komunitas::latest()->get();
        return view('admin.komunitas.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.komunitas.create');
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

        Komunitas::create($validated);

        return redirect()->route('komunitas.index')->with('success', 'Data komunitas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Komunitas::findOrFail($id);
        return view('admin.komunitas.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Komunitas::findOrFail($id);
        return view('admin.komunitas.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = Komunitas::findOrFail($id);

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

        $data->update($validated);

        return redirect()->route('komunitas.index')->with('success', 'Data komunitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Komunitas::findOrFail($id)->delete();
        return redirect()->route('komunitas.index')->with('success', 'Data komunitas berhasil dihapus!');
    }
}

