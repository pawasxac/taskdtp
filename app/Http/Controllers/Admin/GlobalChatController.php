<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalChat;
use Illuminate\Http\Request;

class GlobalChatController extends Controller
{
    public function index()
    {
        $data = GlobalChat::with(['user', 'replyTo'])->latest()->get();
        return view('admin.global-chats.index', compact('data'));
    }

    public function destroy(string $id)
    {
        GlobalChat::findOrFail($id)->delete();
        return redirect()->route('global-chats.index')->with('success', 'Pesan global chat berhasil dihapus.');
    }
}
