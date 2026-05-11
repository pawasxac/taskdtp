<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeShopReview;
use App\Models\CoffeeShop;
use App\Models\User;
use Illuminate\Http\Request;

class CoffeeShopReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CoffeeShopReview::with(['user', 'coffeeShop'])->latest()->get();
        return view('admin.coffee-shop-reviews.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coffeeShops = CoffeeShop::all();
        $users = User::all();
        return view('admin.coffee-shop-reviews.create', compact('coffeeShops', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'coffee_shop_id' => 'required|exists:coffee_shops,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        CoffeeShopReview::create($validated);

        return redirect()->route('coffee-shop-reviews.index')->with('success', 'Review berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = CoffeeShopReview::findOrFail($id);
        return view('admin.coffee-shop-reviews.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = CoffeeShopReview::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
        ]);

        $data->update($validated);

        return redirect()->route('coffee-shop-reviews.index')->with('success', 'Review berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CoffeeShopReview::findOrFail($id)->delete();

        return redirect()->route('coffee-shop-reviews.index')->with('success', 'Review berhasil dihapus!');
    }
}

