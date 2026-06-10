<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.gateway')
                : redirect()->route('dashboard');
        }

        return inertia('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginInput = $request->login;
        
        
        $userExists = User::where('email', $loginInput)->first();
        if (!$userExists) {
            $userExists = User::where('username', $loginInput)->first();
        }

        if (!$userExists) {
            return back()->with('error', 'Akunmu nggak ketemu di radar. Coba cek lagi email atau username-nya.');
        }

        
        if (!Hash::check($request->password, $userExists->password)) {
            return back()->with('error', 'Password-nya masih zonk. Coba racik ulang, ya.');
        }

        
        Auth::login($userExists);
        $request->session()->regenerate();

        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.gateway');
        }

        return redirect()->route('dashboard');
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
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.gateway')
                : redirect()->route('dashboard');
        }

        return inertia('Auth/Register');
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
            'password' => $request->password,
            'role' => 'user',
        ]);

        
        $user->update(['email_verified_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Akunmu udah gacor. Tinggal gas ngopi dan nimbrung.');
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

        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama salah!');
        }

        
        if ($request->hasFile('profile_picture')) {
            
            if ($user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $user->profile_picture))) {
                unlink(public_path('uploads/profile_pictures/' . $user->profile_picture));
            }

            
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile_pictures'), $filename);
            $user->profile_picture = $filename;
        }

        
        $user->username = $request->username;
        $user->email = $request->email;
        
        
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        
        if ($request->new_password) {
            $request->validate([
                'new_password' => 'min:6|confirmed'
            ]);

            $user->password = $request->new_password;
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
