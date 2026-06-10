<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    public function index()
    {
        $data = LoginLog::with('user')->latest()->paginate(20);
        return view('admin.login-logs.index', compact('data'));
    }

    public function destroy($id)
    {
        LoginLog::findOrFail($id)->delete();
        return redirect()->route('login-logs.index')->with('success', 'Log login berhasil dihapus!');
    }
}
