<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendResetEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Mengirim Email menggunakan API SMTP yang ada di .env
        try {
            Mail::raw("Halo, berikut adalah instruksi reset password untuk akun Anda di Sistem Penggajian. Silakan hubungi Administrator untuk mengganti sandi Anda.", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Permintaan Reset Password - Sistem Penggajian');
            });

            return back()->with('status', 'Email instruksi reset password berhasil dikirim ke ' . $email);
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Gagal mengirim email: ' . $e->getMessage()]);
        }
    }
}
