@extends('layouts.app')

@section('content')
<style>
    .bg-custom-green {
        background-color: #729d72 !important;
        border-color: #729d72 !important;
    }
    .bg-custom-green:hover {
        background-color: #618761 !important;
        border-color: #618761 !important;
    }
</style>

<div class="card shadow-sm border-0">
    <div class="card-header bg-custom-green text-white py-3">
        <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i> Edit Data Karyawan</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nik" class="form-label fw-bold">NIK</label>
                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $karyawan->nik) }}" required>
                @error('nik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $karyawan->nama) }}" required>
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="jabatan" class="form-label fw-bold">Jabatan</label>
                <input type="text" class="form-control @error('jabatan') is-invalid @enderror" id="jabatan" name="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}" required>
                @error('jabatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="gaji_pokok" class="form-label fw-bold">Gaji Pokok (Rp)</label>
                <input type="number" class="form-control @error('gaji_pokok') is-invalid @enderror" id="gaji_pokok" name="gaji_pokok" value="{{ old('gaji_pokok', $karyawan->gaji_pokok) }}" required>
                @error('gaji_pokok')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="lembur" class="form-label fw-bold">Lembur (Rp)</label>
                <input type="number" class="form-control @error('lembur') is-invalid @enderror" id="lembur" name="lembur" value="{{ old('lembur', $karyawan->lembur) }}" required>
                @error('lembur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="pinjaman" class="form-label fw-bold">Pinjaman (Rp)</label>
                <input type="number" class="form-control @error('pinjaman') is-invalid @enderror" id="pinjaman" name="pinjaman" value="{{ old('pinjaman', $karyawan->pinjaman) }}" required>
                @error('pinjaman')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('karyawan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary bg-custom-green border-0">
                    <i class="fas fa-save me-1"></i> Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection