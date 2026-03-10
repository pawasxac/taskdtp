<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\LoginLog;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginInput = $request->login;
        
        // Try to find user by email first, then by username
        $userExists = User::where('email', $loginInput)->first();
        if (!$userExists) {
            $userExists = User::where('username', $loginInput)->first();
        }

        if (!$userExists) {
            return back()->with('error', 'User tidak ditemukan: ' . $loginInput);
        }

        // Check password
        if (!Hash::check($request->password, $userExists->password)) {
            return back()->with('error', 'Password salah');
        }

        // Attempt login
        Auth::login($userExists);
        $request->session()->regenerate();

        LoginLog::create([
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'login_at' => now(),
        ]);

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | USER DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function userDashboard()
    {
        return view('user.dashboard');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PROFILE (CRUD USER)
    |--------------------------------------------------------------------------
    */

    public function editProfile()
    {
        return view('user.profile', [
            'user' => Auth::user()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        // cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama salah!');
        }

        // update username & email
        $user->username = $request->username;
        $user->email = $request->email;

        // jika ingin ganti password
        if ($request->new_password) {
            $request->validate([
                'new_password' => 'min:6|confirmed'
            ]);

            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}

