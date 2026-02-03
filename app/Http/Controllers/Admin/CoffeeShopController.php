<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;
use App\Models\LoginLog;
use App\Models\User;

class CoffeeShopController extends Controller
{
    public function index()
    {
        $data = CoffeeShop::latest()->get();
        return view('admin.coffee-shops.index', compact('data'));
    }

    public function create()
    {
        return view('admin.coffee-shops.create');
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string',
            'jam_tutup' => 'required|string',
            'harga_min' => 'required|integer|min:0',
            'harga_max' => 'required|integer|gte:harga_min',
            'rating' => 'required|numeric|min:1|max:5',
            'deskripsi' => 'required|string',
        ], [
            'rating.min' => 'Rating minimal adalah 1',
            'rating.max' => 'Rating maksimal adalah 5',
            'harga_max.min' => 'Harga maksimal tidak boleh kurang dari harga minimal',
        ]);

        CoffeeShop::create($validated);
        return redirect()->route('coffee.index')->with('success', 'Data coffee shop berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = CoffeeShop::findOrFail($id);
        return view('admin.coffee-shops.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $data = CoffeeShop::findOrFail($id);

        // Validasi data
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jam_buka' => 'required|string',
            'jam_tutup' => 'required|string',
            'harga_min' => 'required|integer|min:0',
            'harga_max' => 'required|integer|gte:harga_min',
            'rating' => 'required|numeric|min:1|max:5',
            'deskripsi' => 'required|string',
        ], [
            'rating.min' => 'Rating minimal adalah 1',
            'rating.max' => 'Rating maksimal adalah 5',
            'harga_max.min' => 'Harga maksimal tidak boleh kurang dari harga minimal',
        ]);

        // Cek apakah ada perubahan minimal 1 field
        $perubahan = 0;
        $fields = ['nama', 'daerah', 'kecamatan', 'alamat', 'jam_buka', 'harga_min', 'harga_max', 'rating'];
        
        foreach ($fields as $field) {
            if ($request->input($field) != $data->$field) {
                $perubahan++;
            }
        }

        if ($perubahan === 0) {
            return back()->with('error', 'Minimal edit 1 bagian data!')->withInput();
        }

        $data->update($validated);
        return redirect()->route('coffee.index')->with('success', 'Data coffee shop berhasil diperbarui!');
    }

    public function destroy($id)
    {
        CoffeeShop::findOrFail($id)->delete();
        return redirect()->route('coffee.index')->with('success', 'Data coffee shop berhasil dihapus!');
    }

    public function indexUser()
    {
        $data = \App\Models\CoffeeShop::all();
        return view('user.dashboard', compact('data'));
    }

    public function loginMonitor()
    {
        $logs = LoginLog::with('user')->latest()->paginate(10);
        return view('admin.login-monitor', compact('logs'));
    }

    public function deleteLog($id)
    {
        LoginLog::findOrFail($id)->delete();
        return back()->with('success', 'Log berhasil dihapus');
    }

}