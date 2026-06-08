<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoffeeShop;
use App\Traits\SearchableFilterable;
use Illuminate\Http\Request;

class CoffeeShopController extends Controller
{
    use SearchableFilterable;

    protected function getSortableFields(): array
    {
        return ['id', 'nama', 'created_at', 'updated_at', 'rating', 'harga_min', 'harga_max'];
    }

    public function index(Request $request)
    {
        $query = CoffeeShop::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query = $this->applySearch($query, $request->search, [
                'nama', 'daerah', 'kecamatan', 'alamat', 'deskripsi'
            ]);
        }

        // Filters
        $filters = [];
        if ($request->has('kecamatan_id')) $filters['kecamatan_id'] = $request->kecamatan_id;
        if ($request->has('is_verified')) $filters['is_verified'] = $request->is_verified;
        if ($request->has('is_active')) $filters['is_active'] = $request->is_active;
        
        // Price range filters
        if ($request->has('price_min')) {
            $query->where('harga_min', '>=', $request->price_min);
        }
        if ($request->has('price_max')) {
            $query->where('harga_max', '<=', $request->price_max);
        }
        
        // Rating filter (minimum rating)
        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'desc');
        $query = $this->applySorting($query, $sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 15);
        $coffeeShops = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $coffeeShops->items(),
            'pagination' => [
                'total' => $coffeeShops->total(),
                'per_page' => $coffeeShops->perPage(),
                'current_page' => $coffeeShops->currentPage(),
                'last_page' => $coffeeShops->lastPage(),
                'from' => $coffeeShops->firstItem(),
                'to' => $coffeeShops->lastItem(),
            ]
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
            'rating' => 'nullable|numeric|min:0|max:5',
            'deskripsi' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo_url' => 'nullable|url',
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
            'nama' => 'nullable|string|max:255',
            'daerah' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
            'alamat' => 'nullable|string',
            'jam_buka' => 'nullable|string|max:10',
            'jam_tutup' => 'nullable|string|max:10',
            'harga_min' => 'nullable|integer|min:0',
            'harga_max' => 'nullable|integer',
            'rating' => 'nullable|numeric|min:0|max:5',
            'deskripsi' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo_url' => 'nullable|url',
            'is_verified' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
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
