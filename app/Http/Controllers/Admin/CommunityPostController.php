<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\Komunitas;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CommunityPost::with(['komunitas', 'user', 'comments'])->latest()->get();
        return view('admin.community-posts.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $komunitas = Komunitas::all();
        $users = User::all();
        return view('admin.community-posts.create', compact('komunitas', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        CommunityPost::create($validated);

        return redirect()->route('community-posts.index')->with('success', 'Postingan komunitas berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = CommunityPost::findOrFail($id);
        $komunitas = Komunitas::all();
        $users = User::all();
        return view('admin.community-posts.edit', compact('data', 'komunitas', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = CommunityPost::findOrFail($id);

        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'user_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $data->update($validated);

        return redirect()->route('community-posts.index')->with('success', 'Postingan komunitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CommunityPost::findOrFail($id)->delete();

        return redirect()->route('community-posts.index')->with('success', 'Postingan komunitas berhasil dihapus!');
    }
}

