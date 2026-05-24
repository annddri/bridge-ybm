
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahfidz Tracker - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --accent-color: #0d6efd;
            --bg-light: #f4f7fa; 
            --navy-theme: #063255;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Navigasi Sidebar Terintegrasi Premium */
        .sidebar { 
            width: 280px; height: 100vh; position: fixed; top: 0; left: 0; 
            background: linear-gradient(180deg, #063255 0%, #041f35 100%); 
            color: var(--sidebar-text); padding-top: 10px; z-index: 1000; overflow-y: auto; 
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-brand { text-align: center; padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .brand-logo { width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); margin-bottom: 12px; object-fit: cover; }
        
        .nav-link { color: var(--sidebar-text); padding: 11px 25px; display: flex; align-items: center; transition: all 0.2s ease; font-size: 0.92rem; text-decoration: none; border-left: 4px solid transparent; }
        .nav-link i { width: 24px; margin-right: 12px; font-size: 1.05rem; opacity: 0.8; }
        .nav-link:hover { color: #fff; background-color: var(--sidebar-hover); padding-left: 28px; }
        .nav-link.active { color: #fff; background-color: rgba(13, 110, 253, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }

        /* Layout Konten Utama */
        .main-content { margin-left: 280px; padding: 0; transition: all 0.3s ease; }
        .content-body { padding: 30px; } 

        /* STYLE HEADER SAMA DENGAN SPIRITUAL TRACKER */
        .tracker-header-box {
            background: #ffffff;
            border: 1px solid rgba(6, 50, 85, 0.07);
            box-shadow: 0 10px 25px rgba(6, 50, 85, 0.05);
        }
        .header-title {
            color: #041f35;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            color: #64748b;
            font-size: 0.85rem;
        }
        
        /* CARD LIVE-STYLE WITH DEPTH ELEVATION */
        .live-card {
            background: #ffffff;
            border: 1px solid rgba(6, 50, 85, 0.07);
            box-shadow: 0 12px 30px rgba(6, 50, 85, 0.08);
        }

        .table > thead {
            background-color: #f8fafc;
            color: var(--navy-theme);
            font-weight: 700;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .btn-action {
            padding: 5px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 30px;
        }

        /* BUTTON KEMBALI CAPSULE (BLACK MATTE) SESUAI GAMBAR */
        .btn-kembali {
            background-color: #212529;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 7px 24px;
            border-radius: 30px;
            border: none;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .btn-kembali:hover {
            background-color: #000000;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 991.98px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
            .sidebar-brand { display: flex; align-items: center; text-align: left; padding: 15px; }
            .brand-logo { width: 45px; height: 45px; margin-bottom: 0; margin-right: 15px; }
            .logout-link { border-top: none; margin-top: 0; padding-top: 11px !important; }
        }
    </style>
</head>
<body>


<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="{{ $foto_path }}?t={{ time() }}" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6">{{ $u->name }}</h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;">
                {{ $u->role }}
            </small>
        </div>
    </div>

    <div class="mt-3">
        <a href="/dashboard" class="nav-link">
            <i class="fas fa-home"></i> Home
        </a>

        <a href="/profile" class="nav-link">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>

        @if ($role_user !== 'mahasiswa')
            <a href="/data-awardee" class="nav-link">
                <i class="fas fa-users"></i> Data Awardee
            </a>
        @endif

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Monitoring
        </div>

        <a href="/amalan" class="nav-link">
            <i class="fas fa-pray"></i> Spiritual Tracker
        </a>

        <a href="/tahfidz" class="nav-link active">
            <i class="fas fa-book-quran"></i> Tahfidz Tracker
        </a>

        <a href="/akademik" class="nav-link">
            <i class="fas fa-graduation-cap"></i> Akademik
        </a>

        <a href="/keaktifan" class="nav-link">
            <i class="fas fa-award"></i> Portofolio
        </a>

        <a href="/masyarakat" class="nav-link">
            <i class="fas fa-people-group"></i> Sosial Masyarakat
        </a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Asrama
        </div>

        <a href="/inventaris" class="nav-link">
            <i class="fas fa-boxes-stacked"></i> Inventaris Asrama
        </a>

        <a href="/keuangan" class="nav-link">
            <i class="fas fa-wallet"></i> Keuangan Asrama
        </a>

        <a href="/perizinan" class="nav-link">
            <i class="fas fa-envelope-open-text"></i> Perizinan Asrama
        </a>

        <a href="/logout" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="content-body">

        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-book-quran fs-3 text-success me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">TAHFIDZ TRACKER</h4>
                        <p class="m-0 header-subtitle">
                            Input Setoran Kuantitas Hafalan Baru (Ziyadah) & Murojaah
                        </p>
                    </div>
                </div>

                <div>
                    <a href="/dashboard" class="btn-kembali shadow-sm">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($role_user === 'mahasiswa')
            <div class="card live-card border-0 p-4 rounded-4 mb-4">
                <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                    Input Setoran Baru
                </h6>

                <hr class="text-muted opacity-25 my-3">

                <form action="{{ route('tahfidz.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted fw-semibold small mb-1">
                                Nama Surah / Materi
                            </label>
                            <input type="text"
                                   name="nama_surah"
                                   class="form-control form-control-sm rounded-3"
                                   placeholder="Contoh: Al-Mulk"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">
                                Tanggal Tes
                            </label>
                            <input type="date"
                                   name="tanggal_tes"
                                   class="form-control form-control-sm rounded-3"
                                   required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-muted fw-semibold small mb-1">
                                Bukti Verifikasi PDF/Gambar
                            </label>
                            <input type="file"
                                   name="file_verifikasi"
                                   class="form-control form-control-sm rounded-3"
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   required>
                        </div>

                        <div class="col-md-2">
                            <button type="submit"
                                    class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2"
                                    style="background-color: var(--navy-theme); border-color: var(--navy-theme);">
                                <i class="fas fa-paper-plane me-1"></i> Kirim
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        <div class="card live-card border-0 p-4 rounded-4">
            <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                Daftar Capaian Tahfidz
            </h6>

            <hr class="text-muted opacity-25 my-3">

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr>
                            @if ($role_user === 'kepala_asrama')
                                <th class="border-0 px-3 py-3">Nama Mahasiswa</th>
                            @endif

                            <th class="border-0 {{ $role_user === 'mahasiswa' ? 'px-3' : '' }} py-3">
                                Surah / Materi
                            </th>

                            <th class="border-0 py-3">Tanggal</th>
                            <th class="border-0 py-3 text-center">Bukti</th>
                            <th class="border-0 py-3 text-center">Status</th>

                            @if ($role_user === 'kepala_asrama')
                                <th class="border-0 py-3 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data_tahfidz as $row)
                            <tr>
                                @if ($role_user === 'kepala_asrama')
                                    <td class="px-3 fw-semibold" style="color: #334155;">
                                        {{ $row->name }}
                                    </td>
                                @endif

                                <td class="{{ $role_user === 'mahasiswa' ? 'px-3' : '' }}">
                                    <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold">
                                        {{ $row->nama_surah }}
                                    </span>
                                </td>

                                <td class="text-muted">
                                    {{ date('d M Y', strtotime($row->tanggal_tes)) }}
                                </td>

                                <td class="text-center">
                                    @if ($row->file_verifikasi)
                                        <a href="{{ asset('uploads/tahfidz/' . $row->file_verifikasi) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                           style="font-size: 0.75rem;">
                                            <i class="fas fa-eye me-1"></i> File
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($row->status === 'Lulus')
                                        <span class="badge-status bg-success text-white">
                                            <i class="fas fa-check me-1"></i> Lulus
                                        </span>
                                    @else
                                        <span class="badge-status bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>

                                @if ($role_user === 'kepala_asrama')
                                    <td class="text-center">
                                        @if ($row->status === 'Belum Lulus')
                                            <a href="{{ route('tahfidz.status', [$row->id, 'Lulus']) }}"
                                               class="btn btn-action btn-success shadow-sm">
                                                <i class="fas fa-check-double me-1"></i> Verifikasi
                                            </a>
                                        @else
                                            <a href="{{ route('tahfidz.status', [$row->id, 'Belum Lulus']) }}"
                                               class="btn btn-action btn-danger shadow-sm">
                                                <i class="fas fa-undo me-1"></i> Batalkan
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $role_user === 'kepala_asrama' ? 6 : 4 }}"
                                    class="text-center text-muted py-4">
                                    Belum ada data setoran hafalan terdata.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>