@extends('layouts.app')

@section('content')
<style>
    .slip-container {
        max-width: 750px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .slip-header {
        text-align: center;
        border-bottom: 2px dashed #ccc;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .section-bar {
        background-color: #D6E8D4;
        color: #2c3e50;
        font-weight: bold;
        padding: 8px 15px;
        margin: 15px 0 10px 0;
        border-radius: 4px;
        text-align: center;
    }
</style>

<div class="slip-container">
    <div class="slip-header">
        <h5 class="fw-bold text-dark mb-1">TAMBAH DATA KARYAWAN & SLIP GAJI</h5>
        <p class="text-muted small mb-0">Silakan lengkapi data karyawan dan pilih periode gaji</p>
    </div>

    <form action="{{ route('karyawan.store') }}" method="POST" id="formSlip">
        @csrf

        <!-- Pilihan Periode Gaji -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-3 fw-semibold">PERIODE GAJI</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-8">
                <select name="periode_id" class="form-control @error('periode_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Periode Gaji --</option>
                    @foreach(\App\Models\Periode::all() as $p)
                        <option value="{{ $p->id }}" {{ old('periode_id') == $p->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($p->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($p->tanggal_selesai)->translatedFormat('d F Y') }} (Status: {{ $p->status }})
                        </option>
                    @endforeach
                </select>
                @error('periode_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Informasi Karyawan -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-3 fw-semibold">NAMA</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-8">
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Masukkan nama karyawan" required>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-md-3 fw-semibold">NIK</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-8">
                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" placeholder="Contoh: KRY001" required>
            </div>
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-md-3 fw-semibold">JABATAN</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-8">
                <input type="text" name="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan') }}" placeholder="Contoh: Staff / Programmer" required>
            </div>
        </div>

        <!-- Tabel Penghasilan & Potongan (Grid 2 Kolom) -->
        <div class="section-bar">
            PENGHASILAN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; POTONGAN
        </div>

        <div class="row">
            <!-- Kolom Penghasilan (Kiri) -->
            <div class="col-md-6 border-end pe-md-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Gaji Pokok</label>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control" value="{{ old('gaji_pokok', 0) }}" oninput="hitungTotal()" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Lembur</label>
                    <input type="number" name="lembur" id="lembur" class="form-control" value="{{ old('lembur', 0) }}" oninput="hitungTotal()">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Total Penghasilan</label>
                    <input type="text" id="total_penghasilan" class="form-control bg-light fw-bold" readonly value="Rp 0">
                </div>
            </div>

            <!-- Kolom Potongan (Kanan) -->
            <div class="col-md-6 ps-md-4">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pinjaman Karyawan</label>
                    <input type="number" name="pinjaman" id="pinjaman" class="form-control" value="{{ old('pinjaman', 0) }}" oninput="hitungTotal()">
                </div>
                <div class="mb-3" style="visibility: hidden;">
                    <label class="form-label small fw-semibold">-</label>
                    <input type="text" class="form-control" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Total Potongan</label>
                    <input type="text" id="total_potongan" class="form-control bg-light fw-bold text-danger" readonly value="Rp 0">
                </div>
            </div>
        </div>

        <!-- Gaji Bersih -->
        <div class="section-bar text-center">
            GAJI BERSIH
        </div>
        <div class="mb-4">
            <input type="text" id="gaji_bersih" class="form-control text-center fw-bold fs-5 text-success bg-light" readonly value="Rp 0">
        </div>

        <!-- Captcha Section -->
        <div class="mb-3 p-3 bg-light rounded border">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-secondary" id="captchaLabel">Captcha : 7 * 6</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="generateCaptcha()" title="Refresh Captcha">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <input type="number" id="captchaInput" class="form-control" placeholder="Masukkan hasil perhitungan di atas" required>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary px-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary px-5 bg-custom-green border-0 fw-bold">
                Submit
            </button>
        </div>
    </form>
</div>

<!-- Script Hitung Otomatis & Captcha -->
<script>
    let correctAnswer = 42; // default dari 7 * 6

    function generateCaptcha() {
        const num1 = Math.floor(Math.random() * 8) + 2; // angka 2 - 9
        const num2 = Math.floor(Math.random() * 8) + 2;
        correctAnswer = num1 * num2;
        document.getElementById('captchaLabel').innerText = `Captcha : ${num1} * ${num2}`;
    }

    function hitungTotal() {
        let gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
        let lembur = parseFloat(document.getElementById('lembur').value) || 0;
        let pinjaman = parseFloat(document.getElementById('pinjaman').value) || 0;

        let totalPenghasilan = gajiPokok + lembur;
        let totalPotongan = pinjaman;
        let gajiBersih = totalPenghasilan - totalPotongan;

        document.getElementById('total_penghasilan').value = 'Rp ' + totalPenghasilan.toLocaleString('id-ID');
        document.getElementById('total_potongan').value = 'Rp ' + totalPotongan.toLocaleString('id-ID');
        document.getElementById('gaji_bersih').value = 'Rp ' + gajiBersih.toLocaleString('id-ID');
    }

    // Validasi captcha sebelum submit
    document.getElementById('formSlip').addEventListener('submit', function(e) {
        let userCaptcha = parseInt(document.getElementById('captchaInput').value);
        if (userCaptcha !== correctAnswer) {
            e.preventDefault();
            alert('Hasil perhitungan Captcha salah! Silakan coba lagi.');
            generateCaptcha();
            document.getElementById('captchaInput').value = '';
        }
    });

    // Jalankan hitung saat halaman dimuat
    hitungTotal();
    generateCaptcha();
</script>
@endsection