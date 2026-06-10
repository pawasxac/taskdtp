<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DirectMessage;
use Illuminate\Http\Request;

class DirectMessageController extends Controller
{
    public function index()
    {
        $data = DirectMessage::with(['sender', 'receiver'])->latest()->get();
        return view('admin.direct-messages.index', compact('data'));
    }

    public function destroy(string $id)
    {
        DirectMessage::findOrFail($id)->delete();
        return redirect()->route('direct-messages.index')->with('success', 'Direct message berhasil dihapus.');
    }
}
