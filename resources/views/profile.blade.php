<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - {{ $u->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --accent-color: #0d6efd;
            --bg-light: #f8fafc;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; }
        
        /* Navigasi Sidebar Terintegrasi */
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

        /* Pembungkus Konten Utama */
        .main-content { margin-left: 280px; padding: 0 0 35px 0; transition: all 0.3s ease; }
        
        /* Desain Header Profil Melebar */
        .header-gradient {
            background: linear-gradient(135deg, #063255, #0b426e);
            height: 200px;
            border-radius: 0 0 30px 30px;
        }
        .profile-wrapper { padding: 0 30px; }
        .profile-container { margin-top: -100px; position: relative; z-index: 10; }
        
        .profile-img {
            width: 140px; height: 140px;
            border-radius: 50%; border: 5px solid white;
            object-fit: cover; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* Card Profile dengan Gradasi Abu-abu Halus */
        .card-profile { 
            border: 1px solid rgba(255, 255, 255, 0.7); 
            border-radius: 24px; 
            background: linear-gradient(135deg, #ffffff 0%, #f4f6f9 100%); 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }

        .info-label { font-size: 0.72rem; font-weight: 700; color: #8a94a0; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 0.95rem; font-weight: 600; color: #1e293b; margin-top: 2px; }

        /* Penyesuaian Responsif HP */
        @media (max-width: 991.98px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
            .profile-wrapper { padding: 0 15px; }
            .sidebar-brand { display: flex; align-items: center; text-align: left; padding: 15px; }
            .brand-logo { width: 45px; height: 45px; margin-bottom: 0; margin-right: 15px; }
            .logout-link { border-top: none; margin-top: 0; padding-top: 11px !important; }
        }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6">{{ $u->name }}</h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;">{{ $u->role }}</small>
        </div>
    </div>

    <div class="mt-3">
        <a href="index.php" class="nav-link">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link active">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        
        @if ($role_user != 'mahasiswa')
            <a href="data_awardee.php" class="nav-link">
                <i class="fas fa-users"></i> Data Awardee
            </a>
        @endif

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Monitoring
        </div>
        <a href="amalan.php" class="nav-link">
            <i class="fas fa-pray"></i> Spiritual Tracker
        </a>
        <a href="tahfidz.php" class="nav-link">
            <i class="fas fa-book-quran"></i> Tahfidz Tracker
        </a>
        <a href="akademik.php" class="nav-link">
            <i class="fas fa-graduation-cap"></i> Akademik
        </a>
        <a href="keaktifan.php" class="nav-link">
            <i class="fas fa-award"></i> Portofolio
        </a>
        <a href="masyarakat.php" class="nav-link">
            <i class="fas fa-people-group"></i> Sosial Masyarakat
        </a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Asrama
        </div>
        <a href="inventaris.php" class="nav-link">
            <i class="fas fa-boxes-stacked"></i> Inventaris Asrama
        </a>
        <a href="keuangan.php" class="nav-link">
            <i class="fas fa-wallet"></i> Keuangan Asrama
        </a>
        <a href="perizinan.php" class="nav-link">
            <i class="fas fa-envelope-open-text"></i> Perizinan Asrama
        </a>
        
        <a href="logout.php" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="header-gradient"></div>

    <div class="profile-wrapper">
        <div class="container-fluid profile-container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card card-profile p-4 p-md-5">
                        
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $foto_path }}?t={{ time() }}" alt="Profile" class="brand-logo shadow">
                                <span class="badge bg-<?= $theme ?> position-absolute bottom-0 end-0 px-3 py-2 rounded-pill shadow-sm text-uppercase fw-bold" style="transform: translate(-5%, -15%); font-size: 0.72rem;">
                                    {{ $u->role }}
                                </span>
                            </div>

                            <h3 class="fw-bold mt-2 text-dark">{{ $u->role }}</h3>
                            
                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <a href="edit_profile.php" class="btn btn-primary px-4 rounded-pill btn-sm fw-bold shadow-sm">Edit Profil</a>
                                <a href="index.php" class="btn btn-outline-secondary px-4 rounded-pill btn-sm fw-semibold shadow-sm">Ke Dashboard</a>
                            </div>
                        </div>

                        <hr class="opacity-25 my-4">

                        <div class="row px-md-2">
                            <div class="col-md-6 border-end border-light mb-4 mb-md-0">
                                <h6 class="fw-bold text-primary mb-4"><i class="fas fa-graduation-cap me-2"></i>Informasi Akademik</h6>
                                
                                <div class="mb-3">
                                    <div class="info-label">NIBS (Nomor Induk Beasiswa)</div>
                                    <div class="info-value">{{ $u->nibs}}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">NIM (Nomor Induk Mahasiswa)</div>
                                    <div class="info-value">{{ $u->nim }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Universitas</div>
                                    <div class="info-value">{{ $u->universitas }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Program Studi</div>
                                    <div class="info-value">{{ $u->prodi }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Angkatan Beasiswa</div>
                                    <div class="info-value">{{ $u->angkatan }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-4"><i class="fas fa-id-card me-2"></i>Kontak & Hubungan Internal</h6>
                                
                                <div class="mb-3">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $u->email}}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Nomor Telepon (WhatsApp)</div>
                                    <div class="info-value">{{ $u->no_telp ?? 'Belum diisi' }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>