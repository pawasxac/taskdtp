<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GatheringRequest;
use App\Models\Komunitas;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;

class GatheringRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = GatheringRequest::with(['komunitas', 'coffeeShop', 'requester'])->latest()->get();
        return view('admin.gathering-requests.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $komunitas = Komunitas::all();
        $coffeeShops = CoffeeShop::all();
        return view('admin.gathering-requests.create', compact('komunitas', 'coffeeShops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'coffee_shop_id' => 'required|exists:coffee_shops,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
        ]);

        GatheringRequest::create([
            'community_id' => $validated['community_id'],
            'coffee_shop_id' => $validated['coffee_shop_id'],
            'requested_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'event_date' => $validated['event_date'],
            'status' => 'pending',
        ]);

        return redirect()->route('gathering-requests.index')->with('success', 'Gathering request berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = GatheringRequest::findOrFail($id);
        $komunitas = Komunitas::all();
        $coffeeShops = CoffeeShop::all();
        return view('admin.gathering-requests.edit', compact('data', 'komunitas', 'coffeeShops'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = GatheringRequest::findOrFail($id);

        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'coffee_shop_id' => 'required|exists:coffee_shops,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data->update($validated);

        return redirect()->route('gathering-requests.index')->with('success', 'Gathering request berhasil diperbarui!');
    }

    /**
     * Update status specifically (quick action).
     */
    public function updateStatus(Request $request, $id)
    {
        $data = GatheringRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data->update(['status' => $validated['status']]);

        return redirect()->route('gathering-requests.index')->with('success', 'Status gathering request berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        GatheringRequest::findOrFail($id)->delete();

        return redirect()->route('gathering-requests.index')->with('success', 'Gathering request berhasil dihapus!');
    }
}

