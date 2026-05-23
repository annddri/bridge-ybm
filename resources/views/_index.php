<?php
session_start();
include 'config/koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['id_user'])) { 
    header("Location: login.php"); 
    exit; 
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// Ambil data user aktif
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// LOGIKA HITUNG REAL-TIME AMALAN YAUMIYAH
$current_bulan = (int)date('m');
$current_tahun = (int)date('Y');
$nama_bulan_ini = date('F'); 

// Memanggil fungsi global dari config/koneksi.php
$score_spiritual = hitungTotalPersentaseBulanan($conn, $id_user, $current_bulan, $current_tahun);
$score_sisa = 100 - $score_spiritual;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --accent-color: #0d6efd;
            --bg-light: #f4f7fa; /* Warna dasar diubah sedikit abu kebiruan agar card putih lebih kontras dan pop-out */
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

        /* Layout Konten Utama - Menghapus Navbar Atas & Merapatkan Jarak */
        .main-content { margin-left: 280px; padding: 0; transition: all 0.3s ease; }
        .content-body { padding: 15px 30px 30px 30px; } /* Jarak atas mepet ke atas agar tidak ada space kosong */
        
        /* DESAIN CARD BIAR LEBIH HIDUP (GRADIENT + SOFT LAYERED SHADOW) */
        .welcome-box {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid rgba(6, 50, 85, 0.08);
            box-shadow: 0 10px 25px rgba(6, 50, 85, 0.06); /* Shadow halus memberi kedalaman */
        }

        .live-card {
            background: #ffffff;
            border: 1px solid rgba(6, 50, 85, 0.07);
            box-shadow: 0 12px 30px rgba(6, 50, 85, 0.08); /* Efek timbul mewah */
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .live-card:hover {
            transform: translateY(-4px); /* Efek interaktif saat di-hover */
            box-shadow: 0 16px 35px rgba(6, 50, 85, 0.12);
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

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6"><?= htmlspecialchars($u['nama']) ?></h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;"><?= htmlspecialchars($u['role']) ?></small>
        </div>
    </div>

    <div class="mt-3">
        <a href="index.php" class="nav-link active">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        
        <?php if ($role_user != 'awardee'): ?>
        <a href="data_awardee.php" class="nav-link">
            <i class="fas fa-users"></i> Data Awardee
        </a>
        <?php endif; ?>

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
    
    <div class="content-body">
        
        <div class="card welcome-box border-0 mb-4 rounded-4">
            <div class="card-body p-4">
                <h4 class="fw-bold m-0" style="color: var(--navy-theme); letter-spacing: 0.5px;">
                    Hallo, Selamat Datang di Aplikasi BRIGHT
                </h4>
                <p class="text-muted small m-0 mt-1">Sistem informasi monitoring perkembangan program beasiswa, aktivitas keasramaan, dan capaian akademik mandiri.</p>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-4" style="letter-spacing: 0.5px;">
                                <i class="fas fa-chart-pie text-primary me-2"></i>Spiritual Score - <?= $nama_bulan_ini ?>
                            </h6>
                            
                            <div style="position: relative; height: 170px; width: 170px; margin: 0 auto;">
                                <canvas id="spiritualChart"></canvas>
                                <div style="position: absolute; width: 100%; top: 50%; left: 0; transform: translateY(-50%); text-align: center;">
                                    <h3 class="fw-bold m-0" style="color: var(--navy-theme); font-size: 1.8rem;"><?= $score_spiritual ?>%</h3>
                                    <small class="text-muted fw-semibold" style="font-size: 0.65rem;">Target</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4;">Akumulasi otomatis rata-rata seluruh amalan yaumiyah kamu.</p>
                            <a href="amalan.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold w-100" style="font-size: 0.78rem; background-color: var(--navy-theme); border-color: var(--navy-theme);">
                                <i class="fas fa-pray me-1"></i> Isi Amalan Hari Ini
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px;">
                                <i class="fas fa-book-quran text-success me-2"></i>Tahfidz Tracker
                            </h6>
                            <div class="p-3 bg-light rounded-3 mb-2" style="border: 1px solid rgba(0,0,0,0.03);">
                                <small class="text-muted d-block">Capaian Hafalan Saat Ini:</small>
                                <span class="fs-4 fw-bold" style="color: var(--navy-theme);">Juz 30</span>
                            </div>
                            <p class="text-muted small" style="font-size: 0.78rem;">Sistem perekaman kuantitas setoran hafalan baru (Ziyadah) dan pengulangan (Murojaah).</p>
                        </div>
                        <a href="tahfidz.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold w-100" style="font-size: 0.78rem;">Buka Tahfidz</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px;">
                                <i class="fas fa-graduation-cap text-warning me-2"></i>Indeks Prestasi Kumulatif
                            </h6>
                            <div class="p-3 bg-light rounded-3 mb-2" style="border: 1px solid rgba(0,0,0,0.03);">
                                <small class="text-muted d-block">IPK Terakhir Terdata:</small>
                                <span class="fs-4 fw-bold" style="color: var(--navy-theme);">3.75 <span class="fs-6 text-muted fw-normal">/ 4.00</span></span>
                            </div>
                            <p class="text-muted small" style="font-size: 0.78rem;">Riwayat pengumpulan data Kartu Hasil Studi (KHS) mahasiswa awardee aktif per semester.</p>
                        </div>
                        <a href="akademik.php" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-semibold w-100" style="font-size: 0.78rem; color: #d97706; border-color: #f59e0b;">Buka Akademik</a>
                    </div>
                </div>
            </div>

        </div> </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('spiritualChart').getContext('2d');
    
    const scoreTercapai = <?= $score_spiritual ?>;
    const scoreSisa     = <?= $score_sisa ?>;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Target Tercapai', 'Sisa Target'],
            datasets: [{
                data: [scoreTercapai, scoreSisa],
                backgroundColor: [
                    '#063255', // KEMBALI KE WARNA AWAL (Navy Eksklusif BRIGHT)
                    '#f1f5f9'  // Area sisa target kosong
                ],
                borderWidth: 0,
                hoverOffset: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '84%', 
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>