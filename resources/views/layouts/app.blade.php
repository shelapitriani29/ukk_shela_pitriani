<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penggajian Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-custom-green {
            background-color: #87B884 !important; /* Warna hijau segar ala mockup */
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-green mb-4 shadow-sm">
        <div class="container">
            <!-- Brand / Logo di Kiri -->
            <a class="navbar-brand fw-bold me-4" href="{{ route('karyawan.index') }}">
                <i class="fas fa-users me-2"></i> Sistem Penggajian
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Menu Navigasi Dikosongkan / Dihapus -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                </ul>

                <!-- Profil & Logout di Kanan -->
                <div class="d-flex align-items-center text-white">
                    <span class="me-3 fw-semibold"><i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>