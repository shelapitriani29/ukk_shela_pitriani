@extends('layouts.app')

@section('content')
<style>
    .table-custom-green th {
        background-color: #D6E8D4 !important;
        color: #2c3e50 !important;
        border-bottom: 2px solid #b8d8b4;
    }
</style>

<!-- Header & Tombol Tambah -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Data Karyawan</h3>
        <p class="text-muted small mb-0">Kelola data penggajian karyawan</p>
    </div>
    <!-- Tombol diarahkan ke route checkPeriode/pilih periode sebelum menambah karyawan -->
    <a href="{{ route('karyawan.check-periode') }}" class="btn btn-primary bg-custom-green border-0">
        <i class="fas fa-plus"></i> Tambah Karyawan
    </a>
</div>

<!-- Form Search -->
<form action="{{ route('karyawan.index') }}" method="GET" class="mb-4">
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau NIK..." value="{{ $search ?? '' }}">
        <button class="btn btn-primary bg-custom-green border-0" type="submit">Cari</button>
        @if(isset($search) && $search != '')
            <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Tabel Data Karyawan -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-custom-green">
                    <tr>
                        <th class="py-3 ps-3">No</th>
                        <th class="py-3">NIK</th>
                        <th class="py-3">Nama</th>
                        <th class="py-3">Jabatan</th>
                        <th class="py-3">Gaji Pokok</th>
                        <th class="py-3">Lembur</th>
                        <th class="py-3">Pinjaman</th>
                        <th class="py-3">Gaji Bersih</th>
                        <th class="py-3 pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($karyawans as $index => $karyawan)
                    @php
                        $totalPenghasilan = $karyawan->gaji_pokok + $karyawan->lembur;
                        $totalPotongan = $karyawan->pinjaman;
                        $gajiBersih = $totalPenghasilan - $totalPotongan;
                    @endphp
                    <tr>
                        <td class="ps-3">{{ $index + 1 }}</td>
                        <td class="fw-semibold">{{ $karyawan->nik }}</td>
                        <td>{{ $karyawan->nama }}</td>
                        <td>{{ $karyawan->jabatan }}</td>
                        <td>Rp {{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($karyawan->lembur, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($karyawan->pinjaman, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</td>
                        <td class="pe-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Edit -->
                                <a href="{{ route('karyawan.edit', $karyawan->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Hapus -->
                                <form action="{{ route('karyawan.destroy', $karyawan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>

                                <!-- Cetak / Slip Gaji PDF -->
                                <a href="{{ route('karyawan.slip-gaji', $karyawan->id) }}" class="btn btn-info btn-sm text-white" target="_blank" title="Cetak Slip Gaji">
                                    <i class="fas fa-print"></i>
                                </a>

                                <!-- Tombol Pop-up Kirim WhatsApp -->
                                <button type="button" class="btn btn-success btn-sm" title="Kirim via WhatsApp"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#waModal" 
                                    data-nama="{{ $karyawan->nama }}"
                                    data-nik="{{ $karyawan->nik }}"
                                    data-jabatan="{{ $karyawan->jabatan }}"
                                    data-whatsapp="{{ $karyawan->no_whatsapp ?? '' }}"
                                    data-gajipokok="{{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}"
                                    data-lembur="{{ number_format($karyawan->lembur, 0, ',', '.') }}"
                                    data-pinjaman="{{ number_format($karyawan->pinjaman, 0, ',', '.') }}"
                                    data-gajibersih="{{ number_format($gajiBersih, 0, ',', '.') }}">
                                    <i class="fab fa-whatsapp"></i>
                                </button>

                                <!-- Tombol Pop-up Konfirmasi Kirim Email (Resend) -->
                                <button type="button" class="btn btn-secondary btn-sm" title="Kirim via Email (Resend)"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#emailModal" 
                                    data-url="{{ route('karyawan.send-email', $karyawan->id) }}"
                                    data-nama="{{ $karyawan->nama }}"
                                    data-email="{{ $karyawan->email ?? '' }}">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL PILIH PERIODE ================= -->
@if(isset($showPeriodeModal) && $showPeriodeModal)
<div class="modal fade show" id="periodeModal" tabindex="-1" aria-hidden="true" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-alt me-2"></i> Pilih Periode Gaji Karyawan</h5>
                <a href="{{ route('karyawan.index') }}" class="btn-close btn-close-white"></a>
            </div>
            <form action="{{ route('karyawan.set-periode') }}" method="POST">
                @csrf
                <div class="modal-body text-start">
                    <p class="text-muted small">Silakan tentukan Bulan dan Tahun periode gaji sebelum menambahkan data karyawan.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Bulan</label>
                        <select name="bulan" class="form-select" required>
                            <option value="">-- Pilih Bulan --</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tahun</label>
                        <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2026" value="{{ date('Y') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('karyawan.index') }}" class="btn btn-secondary btn-sm">Batal</a>
                    <button type="submit" class="btn btn-success btn-sm">Lanjutkan ke Form <i class="fas fa-arrow-right ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ================= MODAL POP-UP WHATSAPP ================= -->
<div class="modal fade" id="waModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fab fa-whatsapp me-2"></i> Kirim Slip Gaji via WhatsApp</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <p class="text-muted small">Nomor WhatsApp dan pesan di bawah terisi otomatis sesuai rincian gaji karyawan.</p>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nomor WhatsApp Penerima</label>
                    <input type="text" id="wa_nomor" class="form-control" placeholder="08xxxxxxxxxx">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Preview Pesan WhatsApp:</label>
                    <textarea id="wa_pesan" class="form-control" rows="8" readonly style="font-size: 13px; background: #f8f9fa;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnKirimWa" target="_blank" class="btn btn-success btn-sm">
                    <i class="fab fa-whatsapp me-1"></i> Buka & Kirim via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL POP-UP EMAIL (RESEND) ================= -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-envelope me-2"></i> Kirim Slip Gaji via Gmail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formKirimEmail" method="POST" action="">
                @csrf
                <div class="modal-body text-start">
                    <p class="text-muted small mb-3">Masukkan alamat email karyawan. Sistem akan mengirimkan email beserta lampiran dokumen <strong>Slip Gaji Resmi</strong> dalam bentuk file PDF kepada <strong id="info_nama_karyawan" class="text-dark"></strong>.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alamat Email Penerima</label>
                        <input type="email" name="email" id="email_penerima" class="form-control" placeholder="karyawan@example.com" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane me-1"></i> Kirim via Gmail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= SCRIPT JAVASCRIPT DINAMIS ================= -->
<script>
    const waModal = document.getElementById('waModal');
    if (waModal) {
        waModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            
            let nama = button.getAttribute('data-nama');
            let nik = button.getAttribute('data-nik');
            let jabatan = button.getAttribute('data-jabatan');
            let whatsapp = button.getAttribute('data-whatsapp');
            let gajiPokok = button.getAttribute('data-gajipokok');
            let lembur = button.getAttribute('data-lembur');
            let pinjaman = button.getAttribute('data-pinjaman');
            let gajiBersih = button.getAttribute('data-gajibersih');

            let pesan = `Halo ${nama},\n\nBerikut adalah rincian slip gaji Anda:\n\n` +
                        `Nama: ${nama}\n` +
                        `NIK: ${nik}\n` +
                        `Jabatan: ${jabatan}\n` +
                        `----------------------------------------\n` +
                        `Gaji Pokok: Rp ${gajiPokok}\n` +
                        `Uang Lembur: Rp ${lembur}\n` +
                        `Potongan Pinjaman: Rp ${pinjaman}\n` +
                        `----------------------------------------\n` +
                        `TOTAL GAJI BERSIH: Rp ${gajiBersih}\n\n` +
                        `Terima kasih atas kerja keras Anda!`;

            document.getElementById('wa_nomor').value = whatsapp;
            document.getElementById('wa_pesan').value = pesan;

            let encodedPesan = encodeURIComponent(pesan);
            let formatNoHp = whatsapp ? whatsapp.replace(/^0/, '62') : '';
            document.getElementById('btnKirimWa').href = `https://wa.me/${formatNoHp}?text=${encodedPesan}`;
        });
    }

    const emailModal = document.getElementById('emailModal');
    if (emailModal) {
        emailModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            
            let url = button.getAttribute('data-url');
            let nama = button.getAttribute('data-nama');
            let email = button.getAttribute('data-email');

            document.getElementById('formKirimEmail').action = url;
            document.getElementById('email_penerima').value = email;
            document.getElementById('info_nama_karyawan').textContent = nama;
        });
    }
</script>
@endsection