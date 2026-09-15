<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - {{ $karyawan->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 25px;
            border-radius: 8px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #87B884;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
        }
        .table-rincian {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-rincian th, .table-rincian td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        .table-rincian th {
            background-color: #D6E8D4;
            color: #2c3e50;
            font-size: 13px;
        }
        .text-end {
            text-align: right;
        }
        .box-gaji-bersih {
            background-color: #D6E8D4;
            border: 1px solid #b8d8b4;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .box-gaji-bersih h4 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #2c3e50;
        }
        .box-gaji-bersih h2 {
            margin: 0;
            color: #198754;
        }
        /* Menyembunyikan tombol saat dicetak/di-generate PDF */
        .no-print {
            margin-top: 25px;
            text-align: center;
        }
        .btn {
            padding: 8px 16px;
            background-color: #87B884;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: inline-block;
        }
        .btn:hover {
            background-color: #6da36a;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Perusahaan & Periode Dinamis -->
    <div class="header">
        <h3>PT. SISTEM PENGGAJIAN KARYAWAN</h3>
        <p>
            LAPORAN SLIP GAJI RESMI PERIODE 
            <strong>{{ strtoupper($periodeNama ?? 'BULAN INI') }}</strong>
        </p>
    </div>

    <!-- Informasi Karyawan (Email & No. WhatsApp telah dihapus) -->
    <table class="info-table">
        <tr>
            <td width="18%"><strong>Nama</strong></td>
            <td width="2%">:</td>
            <td>{{ $karyawan->nama }}</td>
        </tr>
        <tr>
            <td><strong>NIK</strong></td>
            <td>:</td>
            <td>{{ $karyawan->nik }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>:</td>
            <td>{{ $karyawan->jabatan }}</td>
        </tr>
    </table>

    <!-- Tabel Rincian Gaji -->
    <table class="table-rincian">
        <thead>
            <tr>
                <th>PENGHASILAN</th>
                <th>JUMLAH (Rp)</th>
                <th>POTONGAN</th>
                <th>JUMLAH (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td>Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
                <td>Pinjaman Karyawan</td>
                <td>Rp {{ number_format($karyawan->pinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Lembur</td>
                <td>Rp {{ number_format($karyawan->lembur, 0, ',', '.') }}</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td>Total Penghasilan</td>
                <td>Rp {{ number_format($karyawan->gaji_pokok + $karyawan->lembur, 0, ',', '.') }}</td>
                <td>Total Potongan</td>
                <td>Rp {{ number_format($karyawan->pinjaman, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Kotak Gaji Bersih -->
    @php
        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
        $totalPotongan = $karyawan->pinjaman;
        $gajiBersih = $totalPenghasilan - $totalPotongan;
    @endphp
    <div class="box-gaji-bersih">
        <h4>TOTAL GAJI BERSIH YANG DITERIMA</h4>
        <h2>Rp {{ number_format($gajiBersih, 0, ',', '.') }}</h2>
    </div>

    <!-- Tombol Navigasi, Cetak, dan Download PDF -->
    <div class="no-print">
        <a href="{{ route('karyawan.index') }}" class="btn" style="background-color: #6c757d; margin-right: 5px;">Kembali</a>
        <button onclick="window.print()" class="btn" style="background-color: #0dcaf0; color: #000; margin-right: 5px;">Cetak / Print</button>
        
        <a href="{{ route('karyawan.download-slip-gaji', $karyawan->id) }}" class="btn" style="background-color: #198754;">Download PDF</a>
    </div>
</div>

</body>
</html>