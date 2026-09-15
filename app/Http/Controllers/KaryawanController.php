<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Karyawan::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->get();

        return view('karyawan.index', compact('karyawans', 'search'));
    }

    public function checkPeriode(Request $request)
    {
        $search = $request->input('search');
        
        $karyawans = Karyawan::when($search, function($q) use ($search) {
            return $q->where('nama', 'like', "%{$search}%")
                     ->orWhere('nik', 'like', "%{$search}%");
        })->get();

        return view('karyawan.index', compact('karyawans', 'search'))->with('showPeriodeModal', true);
    }

    public function setPeriode(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string',
            'tahun' => 'required|digits:4',
        ]);

        $bulanArray = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        ];

        $namaBulanList = array_keys($bulanArray);
        $indexBulan = array_search($request->bulan, $namaBulanList);

        $namaBulanDepan = $namaBulanList[($indexBulan + 1) % 12];
        $tahunDepan = ($indexBulan == 11) ? ($request->tahun + 1) : $request->tahun;

        $namaPeriode = '25 ' . $request->bulan . ' ' . $request->tahun . ' - 25 ' . $namaBulanDepan . ' ' . $tahunDepan;

        session(['selected_periode_nama' => $namaPeriode]);

        return redirect()->route('karyawan.create');
    }

    public function create()
    {
        if (!session('selected_periode_nama')) {
            return redirect()->route('karyawan.check-periode');
        }

        $periodeNama = session('selected_periode_nama');
        return view('karyawan.create', compact('periodeNama'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'        => 'required|unique:karyawans,nik',
            'nama'       => 'required|string|max:255',
            'jabatan'    => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'lembur'     => 'nullable|numeric|min:0',
            'pinjaman'   => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        Karyawan::create($data);

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
            'nik'        => 'required|unique:karyawans,nik,' . $id,
            'nama'       => 'required|string|max:255',
            'jabatan'    => 'required|string|max:255',
            'gaji_pokok' => 'required|numeric|min:0',
            'lembur'     => 'nullable|numeric|min:0',
            'pinjaman'   => 'nullable|numeric|min:0',
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

    public function slipGaji($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;
        $periodeNama = session('selected_periode_nama', 'PERIODE BULAN INI');

        $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periodeNama'));
        
        return $pdf->stream('Slip-Gaji-' . $karyawan->nik . '.pdf');
    }

    public function downloadSlipGaji($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        
        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;
        $periodeNama = session('selected_periode_nama', 'PERIODE BULAN INI');

        $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periodeNama'));
        
        return $pdf->download('Slip-Gaji-' . $karyawan->nik . '.pdf');
    }

    public function sendWhatsApp($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        return redirect()->back()->with('success', 'Fitur WhatsApp sedang diproses.');
    }

    public function sendEmail(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'email' => 'required|email'
        ]);

        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;
        $periodeNama = session('selected_periode_nama', 'PERIODE BULAN INI');

        $pdf = Pdf::loadView('karyawan.slip-gaji', compact('karyawan', 'totalPenghasilan', 'totalPotongan', 'gajiBersih', 'periodeNama'));
        $pdfOutput = $pdf->output();

        try {
            // Mengambil email dinamis dari inputan modal user
            $emailTujuan = $request->input('email'); 

            $response = Http::withToken(env('RESEND_API_KEY'))
                ->post('https://api.resend.com/emails', [
                    'from' => 'onboarding@resend.dev',
                    'to' => [$emailTujuan],
                    'subject' => 'Slip Gaji Karyawan - ' . $karyawan->nama,
                    'html' => '
                        <h3 style="color: #1f2937;">Slip Gaji Karyawan</h3>
                        <p>Halo <strong>' . $karyawan->nama . '</strong>,</p>
                        <p>Terima kasih atas dedikasi dan kerja keras yang telah Anda berikan kepada perusahaan untuk periode ini.</p>
                        <p>Berikut kami lampirkan dokumen <strong>Slip Gaji Resmi</strong> dalam bentuk file PDF pada email ini untuk rincian pendapatan dan potongan Anda.</p>
                        <p>Jika ada pertanyaan atau ketidaksesuaian mengenai rincian gaji, silakan hubungi bagian HRD atau Keuangan.</p>
                        <br>
                        <p>Salam hormat,<br><strong>Tim HRD & Keuangan</strong></p>
                    ',
                    'attachments' => [
                        [
                            'filename' => 'Slip-Gaji-' . $karyawan->nik . '.pdf',
                            'content' => base64_encode($pdfOutput),
                        ],
                    ],
                ]);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Slip gaji ' . $karyawan->nama . ' berhasil dikirim ke ' . $emailTujuan);
            } else {
                return redirect()->back()->with('error', 'Resend API Error: ' . $response->body());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim email: ' . $e->getMessage());
        }
    }
}