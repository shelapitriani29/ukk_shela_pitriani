<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data periode untuk pilihan dropdown
        $periodes = Periode::all();
        
        // Ambil parameter filter periode dan pencarian dari request
        $periodeId = $request->input('periode_id');
        $search = $request->input('search');

        // Query data karyawan dengan filter periode & pencarian
        $query = Karyawan::query();

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->get();

        return view('karyawan.index', compact('karyawans', 'search', 'periodes', 'periodeId'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'         => 'required|unique:karyawans,nik',
            'nama'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
            'jabatan'     => 'required|string|max:255',
            'gaji_pokok'  => 'required|numeric|min:0',
            'lembur'      => 'nullable|numeric|min:0',
            'pinjaman'    => 'nullable|numeric|min:0',
        ]);

        Karyawan::create($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'nik'         => 'required|unique:karyawans,nik,' . $id,
            'nama'        => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
            'no_whatsapp' => 'nullable|string|max:20',
            'jabatan'     => 'required|string|max:255',
            'gaji_pokok'  => 'required|numeric|min:0',
            'lembur'      => 'nullable|numeric|min:0',
            'pinjaman'    => 'nullable|numeric|min:0',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    // Method untuk menampilkan Preview Slip Gaji di Browser (Menggunakan stream agar tidak langsung download)
    public function slipGaji($id)
    {
        $karyawan = Karyawan::with('periode')->findOrFail($id);
        
        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;

        $periode = $karyawan->periode ?? Periode::where('status', 'Aktif')->first();

        $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periode'));
        
        // Menggunakan stream() untuk membuka halaman preview di tab/browser
        return $pdf->stream('Slip-Gaji-' . $karyawan->nik . '.pdf');
    }

    // Method khusus untuk tombol Download PDF di halaman preview
    public function downloadSlipGaji($id)
    {
        $karyawan = Karyawan::with('periode')->findOrFail($id);
        
        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;

        $periode = $karyawan->periode ?? Periode::where('status', 'Aktif')->first();

        $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periode'));
        
        // Menggunakan download() agar file terunduh saat tombol download diklik
        return $pdf->download('Slip-Gaji-' . $karyawan->nik . '.pdf');
    }

    public function sendWhatsApp($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return redirect()->back()->with('success', 'Fitur WhatsApp sedang diproses.');
    }

    public function sendEmail($id)
    {
        $karyawan = Karyawan::with('periode')->findOrFail($id);

        if (empty($karyawan->email)) {
            return redirect()->back()->with('error', 'Gagal mengirim email: Alamat email karyawan belum diisi.');
        }

        try {
            $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
            $totalPotongan = $karyawan->pinjaman;
            $gajiBersih = $totalPenghasilan - $totalPotongan;

            $periode = $karyawan->periode ?? Periode::where('status', 'Aktif')->first();

            // 1. Generate PDF Slip Gaji
            $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periode'));
            $pdfOutput = $pdf->output();

            // 2. Kirim Email via Resend API menggunakan Laravel HTTP Client
            $response = Http::withToken(env('RESEND_API_KEY'))
                ->post('https://api.resend.com/emails', [
                    'from'    => 'HRD Department <onboarding@resend.dev>',
                    'to'      => [$karyawan->email],
                    'subject' => 'Slip Gaji Resmi Periode Bulan Ini - ' . $karyawan->nama,
                    'html'    => '
                        <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
                            <h2 style="color: #2c3e50;">Slip Gaji Karyawan</h2>
                            <p>Halo <strong>' . htmlspecialchars($karyawan->nama) . '</strong>,</p>
                            <p>Terima kasih atas dedikasi dan kerja keras yang telah Anda berikan kepada perusahaan untuk periode ini.</p>
                            <p>Berikut kami lampirkan dokumen <strong>Slip Gaji Resmi</strong> dalam bentuk file PDF pada email ini untuk rincian pendapatan dan potongan Anda.</p>
                            <br>
                            <p>Jika ada pertanyaan atau ketidaksesuaian mengenai rincian gaji, silakan hubungi bagian HRD atau Keuangan.</p>
                            <br>
                            <p>Salam hormat,<br>
                            <strong>Tim HRD & Keuangan</strong></p>
                        </div>
                    ',
                    'attachments' => [
                        [
                            'content'     => base64_encode($pdfOutput),
                            'filename'    => 'Slip-Gaji-' . $karyawan->nik . '.pdf',
                        ],
                    ],
                ]);

            if ($response->failed()) {
                throw new \Exception($response->json('message') ?? 'Gagal terhubung ke API Resend.');
            }

            return redirect()->back()->with('success', 'Slip gaji berhasil dikirim ke email ' . $karyawan->email . ' via Resend!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }

    // ==========================================
    // FITUR LUPA PASSWORD & VERIFIKASI TOKEN 6 DIGIT
    // ==========================================

    public function sendResetToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar di dalam sistem.'
        ]);

        // Generate kode acak 6 digit
        $token = rand(100000, 999999);

        // Simpan token ke dalam tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Kirim kode 6 digit via Resend API
        $response = Http::withToken(env('RESEND_API_KEY'))
            ->post('https://api.resend.com/emails', [
                'from'    => 'Sistem Penggajian <onboarding@resend.dev>',
                'to'      => [$request->email],
                'subject' => 'Kode Verifikasi Reset Password',
                'html'    => '
                    <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
                        <h3 style="color: #2c3e50;">Reset Password Sistem Penggajian</h3>
                        <p>Anda menerima email ini karena ada permintaan untuk mereset kata sandi akun Anda.</p>
                        <p>Berikut adalah kode verifikasi <strong>6 digit</strong> Anda:</p>
                        <div style="background: #f4f6f8; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #4A90E2; border-radius: 6px; margin: 20px 0;">
                            ' . $token . '
                        </div>
                        <p>Masukkan kode ini pada halaman form verifikasi untuk melanjutkan proses pembuatan password baru.</p>
                        <p>Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.</p>
                    </div>
                '
            ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal mengirim email verifikasi via Resend.');
        }

        return back()->with([
            'status' => 'Kode verifikasi 6 digit telah dikirim ke email Anda.',
            'email' => $request->email
        ])->withInput();
    }

    public function updatePasswordWithToken(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'token.digits' => 'Kode verifikasi harus tepat 6 digit angka.',
            'password.min' => 'Password baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->with('error', 'Kode verifikasi 6 digit salah atau sudah tidak valid.');
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password berhasil diubah! Silakan login dengan password baru Anda.');
    }
}