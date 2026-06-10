<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function index()
    {
        $data = DB::table('conversations')->latest()->paginate(15);
        return view('admin.conversations.index', compact('data'));
    }

    public function destroy($id)
    {
        DB::table('conversations')->where('id', $id)->delete();
        DB::table('conversation_members')->where('conversation_id', $id)->delete();
        DB::table('conversation_messages')->where('conversation_id', $id)->delete();
        return redirect()->route('conversations.index')->with('success', 'Percakapan berhasil dihapus!');
    }
}
