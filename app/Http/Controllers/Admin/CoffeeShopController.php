<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoffeeShop;
use Illuminate\Http\Request;
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
        $kecamatans = \App\Models\Kecamatan::all();
        return view('admin.coffee-shops.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kecamatan_id' => 'nullable|exists:kecamatans,id',
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
        $kecamatans = \App\Models\Kecamatan::all();
        return view('admin.coffee-shops.edit', compact('data', 'kecamatans'));
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
        $users = User::latest()->paginate(10);
        return view('admin.login-monitor', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.login.monitor')->with('success', 'User berhasil ditambahkan!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($validated['password']) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.login.monitor')->with('success', 'User berhasil diperbarui!');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.login.monitor')->with('success', 'User berhasil dihapus!');
    }

}
