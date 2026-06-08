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
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // For now, auto-verify. Later integrate with email system
        $user->update(['email_verified_at' => now()]);

        Auth::login($user);

        return redirect()->route('user.dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');
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
            'profile_picture' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        // cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama salah!');
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $user->profile_picture))) {
                unlink(public_path('uploads/profile_pictures/' . $user->profile_picture));
            }

            // Upload new profile picture
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile_pictures'), $filename);
            $user->profile_picture = $filename;
        }

        // update username & email
        $user->username = $request->username;
        $user->email = $request->email;
        
        // Update bio and phone number
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

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

    /*
    |--------------------------------------------------------------------------
    | VIEW PROFILE
    |--------------------------------------------------------------------------
    */

    public function viewProfile()
    {
        return view('user.view-profile', [
            'user' => Auth::user()
        ]);
    }
}

