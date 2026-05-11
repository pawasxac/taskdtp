<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\User;
use Illuminate\Http\Request;

class CommunityCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CommunityComment::with(['post', 'user'])->latest()->get();
        return view('admin.community-comments.index', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = CommunityComment::findOrFail($id);
        return view('admin.community-comments.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = CommunityComment::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $data->update($validated);

        return redirect()->route('community-comments.index')->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        CommunityComment::findOrFail($id)->delete();

        return redirect()->route('community-comments.index')->with('success', 'Komentar berhasil dihapus!');
    }
}

