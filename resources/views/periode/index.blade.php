@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Data Periode Gaji</h3>
        <p class="text-muted small mb-0">Kelola tanggal awal dan akhir periode penggajian</p>
    </div>
    <a href="{{ route('periode.create') }}" class="btn btn-primary bg-custom-green border-0">
        <i class="fas fa-plus"></i> Tambah Periode
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 ps-3">No</th>
                        <th class="py-3">Tanggal Mulai</th>
                        <th class="py-3">Tanggal Selesai</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodes as $index => $periode)
                    <tr>
                        <td class="ps-3">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($periode->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($periode->tanggal_selesai)->translatedFormat('d F Y') }}</td>
                        <td>
                            <span class="badge {{ $periode->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $periode->status }}
                            </span>
                        </td>
                        <td class="pe-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Tombol Edit yang mengarah ke route edit dengan parameter id -->
                                <a href="{{ route('periode.edit', $periode->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('periode.destroy', $periode->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus periode ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data periode.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection