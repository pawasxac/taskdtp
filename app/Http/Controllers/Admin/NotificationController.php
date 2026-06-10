<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $data = DB::table('notifications')->latest()->paginate(15);
        return view('admin.notifications.index', compact('data'));
    }

    public function destroy($id)
    {
        DB::table('notifications')->where('id', $id)->delete();
        return redirect()->route('notifications.index')->with('success', 'Notifikasi berhasil dihapus!');
    }
}
