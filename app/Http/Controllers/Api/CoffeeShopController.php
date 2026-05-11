<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;

class CoffeeShopController extends Controller
{
    public function index()
    {
        $coffeeShops = CoffeeShop::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $coffeeShops,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string|max:10',
            'jam_tutup' => 'required|string|max:10',
            'harga_min' => 'required|integer|min:0',
            'harga_max' => 'required|integer|gte:harga_min',
            'rating' => 'required|numeric|min:0|max:5',
            'deskripsi' => 'required|string',
        ]);

        $coffeeShop = CoffeeShop::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coffee shop berhasil dibuat.',
            'data' => $coffeeShop,
        ], 201);
    }

    public function show(CoffeeShop $coffeeShop)
    {
        return response()->json([
            'success' => true,
            'data' => $coffeeShop,
        ]);
    }

    public function update(Request $request, CoffeeShop $coffeeShop)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string|max:10',
            'jam_tutup' => 'required|string|max:10',
            'harga_min' => 'required|integer|min:0',
            'harga_max' => 'required|integer|gte:harga_min',
            'rating' => 'required|numeric|min:0|max:5',
            'deskripsi' => 'required|string',
        ]);

        $coffeeShop->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Coffee shop berhasil diperbarui.',
            'data' => $coffeeShop,
        ]);
    }

    public function destroy(CoffeeShop $coffeeShop)
    {
        $coffeeShop->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coffee shop berhasil dihapus.',
        ], 200);
    }
}
