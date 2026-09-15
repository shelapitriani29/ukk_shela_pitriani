@extends('layouts.app')

@section('content')
<style>
    .periode-container {
        max-width: 650px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .periode-header {
        text-align: center;
        border-bottom: 2px dashed #ccc;
        padding-bottom: 15px;
        margin-bottom: 25px;
    }
</style>

<div class="periode-container">
    <div class="periode-header">
        <h5 class="fw-bold text-dark mb-1">TAMBAH PERIODE GAJI</h5>
        <p class="text-muted small mb-0">Silakan tentukan rentang tanggal dan status periode gaji</p>
    </div>

    <form action="{{ route('periode.store') }}" method="POST">
        @csrf

        <!-- Tanggal Mulai -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-4 fw-semibold">TANGGAL MULAI</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-7">
                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                @error('tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Tanggal Selesai -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-4 fw-semibold">TANGGAL SELESAI</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-7">
                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-4 fw-semibold">STATUS</div>
            <div class="col-md-1 text-center">:</div>
            <div class="col-md-7">
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="d-flex justify-content-between">
            <a href="{{ route('periode.index') }}" class="btn btn-secondary px-4">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary px-5 fw-bold">
                Simpan Periode
            </button>
        </div>
    </form>
</div>
@endsection