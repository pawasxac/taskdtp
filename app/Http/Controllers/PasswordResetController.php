<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak ditemukan dalam sistem kami.',
            ]);
        }

        // Generate reset token
        $token = Str::random(60);
        
        // Store reset token
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send email with reset link (you need to implement email)
        // Mail::send('emails.password-reset', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
        //     $message->to($request->email);
        // });

        return back()->with('status', 'Link reset password telah dikirim ke email Anda.');
    }

    /**
     * Show password reset form
     */
    public function showResetForm($token)
    {
        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('token', 'like', '%' . substr($token, 0, 20) . '%')
            ->first();

        if (!$passwordResetToken || $this->isTokenExpired($passwordResetToken->created_at)) {
            return redirect('/login')->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset the password
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ]);

        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordResetToken || !Hash::check($request->token, $passwordResetToken->token)) {
            throw ValidationException::withMessages([
                'token' => 'Token reset password tidak valid.',
            ]);
        }

        if ($this->isTokenExpired($passwordResetToken->created_at)) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => 'Token reset password telah kadaluarsa.',
            ]);
        }

        // Update user password
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Check if token has expired (24 hours)
     */
    private function isTokenExpired($createdAt): bool
    {
        return now()->diffInHours($createdAt) > 24;
    }
}
