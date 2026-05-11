<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityMember;
use App\Models\Komunitas;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CommunityMember::with(['komunitas', 'user'])->latest()->get();
        return view('admin.community-members.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $komunitas = Komunitas::all();
        $users = User::all();
        return view('admin.community-members.create', compact('komunitas', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        // Check if user is already a member
        $exists = CommunityMember::where('community_id', $validated['community_id'])
            ->where('user_id', $validated['user_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'User ini sudah menjadi anggota komunitas!')->withInput();
        }

        CommunityMember::create([
            'community_id' => $validated['community_id'],
            'user_id' => $validated['user_id'],
            'role' => $validated['role'],
            'joined_at' => now(),
        ]);

        return redirect()->route('community-members.index')->with('success', 'Anggota komunitas berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = CommunityMember::findOrFail($id);
        $komunitas = Komunitas::all();
        $users = User::all();
        return view('admin.community-members.edit', compact('data', 'komunitas', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = CommunityMember::findOrFail($id);

        $validated = $request->validate([
            'community_id' => 'required|exists:komunitas,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        $data->update($validated);

        return redirect()->route('community-members.index')->with('success', 'Anggota komunitas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CommunityMember::findOrFail($id)->delete();

        return redirect()->route('community-members.index')->with('success', 'Anggota komunitas berhasil dihapus!');
    }
}

