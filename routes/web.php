<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeriodeController;

Route::get('/', function () {
    return redirect()->route('karyawan.index');
});

// Route Login & Lupa Password (Dapat diakses publik sebelum login)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Lupa Password dengan Resend API (6 Digit Token & Ubah Password Baru)
Route::post('/forgot-password', [KaryawanController::class, 'sendResetToken'])->name('password.email');
Route::post('/reset-password-token', [KaryawanController::class, 'updatePasswordWithToken'])->name('password.update');

// Route Karyawan & Slip Gaji (Diproteksi Middleware Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    // Route Slip Gaji (Detail & Cetak)
    Route::get('/karyawan/{id}/slip-gaji', [KaryawanController::class, 'slipGaji'])->name('karyawan.slip-gaji');
    
    // Route Khusus Download PDF Slip Gaji
    Route::get('/karyawan/{id}/download-slip-gaji', [KaryawanController::class, 'downloadSlipGaji'])->name('karyawan.download-slip-gaji');

    // Route Kirim WhatsApp via Fonnte API
    Route::post('/karyawan/{id}/send-wa', [KaryawanController::class, 'sendWhatsApp'])->name('karyawan.send-wa');

    // Route Kirim Email Slip Gaji via Resend API
    Route::post('/karyawan/{id}/send-email', [KaryawanController::class, 'sendEmail'])->name('karyawan.send-email');

    // Route Data Periode Gaji (Otomatis mendaftarkan index, create, store, edit, update, destroy)
    Route::resource('periode', PeriodeController::class);
});