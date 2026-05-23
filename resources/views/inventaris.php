<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);

// Menggunakan folder 'assets/img/' sesuai struktur asli proyek
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

$query_inventaris = mysqli_query($conn, "SELECT * FROM inventaris ORDER BY id_barang DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Asrama - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --navy-theme: #063255; /* Biru Gelap Utama */
            --accent-color: #0284c7; /* Biru Terang / Sky Blue */
            --bg-light: #f4f7fa; 
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Sidebar Styles */
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
        .nav-link.active { color: #fff; background-color: rgba(2, 132, 199, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }
        
        .main-content { margin-left: 280px; padding: 30px; transition: all 0.3s ease; }
        .table-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid rgba(6, 50, 85, 0.07); padding: 20px; }
        
        /* Tombol & Judul - Tema Biru Navy YBM */
        .btn-primary-ybm { background-color: var(--navy-theme); color: #fff; border: none; transition: all 0.2s ease; }
        .btn-primary-ybm:hover { background-color: #04243e; color: #fff; transform: translateY(-1px); }
        .text-primary-ybm { color: var(--navy-theme) !important; }
        
        .table > thead { background-color: #f8fafc; color: var(--navy-theme); font-weight: 700; }
        
        @media (max-width: 992px) { .sidebar { margin-left: -280px; } .main-content { margin-left: 0; } }
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
        <a href="index.php" class="nav-link">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        
        <?php if ($_SESSION['role'] != 'awardee'): ?>
        <a href="data_awardee.php" class="nav-link">
            <i class="fas fa-users"></i> Data Awardee
        </a>
        <?php endif; ?>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px;">Fitur Monitoring</div>
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

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px;">Fitur Asrama</div>
        <a href="inventaris.php" class="nav-link active">
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
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Inventaris Asrama</h3>
                <p class="text-muted m-0 small">Kelola data logistik dan fasilitas bersama secara komunal.</p>
            </div>
            <div class="text-end d-none d-md-block">
                <div class="fw-bold text-primary-ybm" style="font-size: 0.95rem;"><?= date('d F Y') ?></div>
                <small id="clock" class="text-muted" style="font-size: 0.85rem; font-weight: 500;"></small>
            </div>
        </div>

        <div class="card table-card shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0 text-dark">Daftar Barang Komunal</h6>
                <a href="tambah_inventaris.php" class="btn btn-primary-ybm btn-sm rounded-pill px-3 fw-bold">
                    <i class="fas fa-plus me-2"></i> Tambah Barang
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th>No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Lokasi</th>
                            <th>Kondisi</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($query_inventaris) > 0):
                            while($row = mysqli_fetch_assoc($query_inventaris)): 
                                if($row['kondisi'] == 'Baik') {
                                    $badge = 'bg-success bg-opacity-10 text-success';
                                } elseif($row['kondisi'] == 'Rusak Ringan') {
                                    $badge = 'bg-warning bg-opacity-10 text-warning';
                                } else {
                                    $badge = 'bg-danger bg-opacity-10 text-danger';
                                }
                        ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td class="fw-bold text-secondary"><?= htmlspecialchars($row['kode_barang']) ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></td>
                            <td><span class="badge bg-secondary opacity-75 fw-semibold"><?= $row['jumlah'] ?> Unit</span></td>
                            <td><?= htmlspecialchars($row['lokasi']) ?></td>
                            <td><span class="badge <?= $badge ?> px-2.5 py-1.5 fw-bold" style="font-size: 0.72rem; border-radius: 30px;"><?= $row['kondisi'] ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_inventaris.php?id=<?= $row['id_barang'] ?>" class="btn btn-outline-secondary py-1" title="Edit">
                                        <i class="fas fa-edit text-warning"></i>
                                    </a>
                                    <a href="hapus_inventaris.php?id=<?= $row['id_barang'] ?>" class="btn btn-outline-secondary py-1" onclick="return confirm('Yakin ingin menghapus barang ini?')" title="Hapus">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center class-muted py-4 small text-muted">Belum ada barang terdata. Silakan klik Tambah Barang.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString();
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>