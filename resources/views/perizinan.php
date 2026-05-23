<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// Ambil data user untuk sidebar
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// PROSES INPUT IZIN (Untuk Awardee)
if (isset($_POST['ajukan_izin'])) {
    $jenis = $_POST['jenis_izin'];
    $mulai = $_POST['tgl_mulai'];
    $selesai = $_POST['tgl_selesai'];
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);

    $query_input = mysqli_query($conn, "INSERT INTO perizinan (id_user, jenis_izin, tgl_mulai, tgl_selesai, alasan, status_approval) 
                                        VALUES ('$id_user', '$jenis', '$mulai', '$selesai', '$alasan', 'Pending')");
    if ($query_input) {
        echo "<script>alert('Izin berhasil diajukan! Tunggu persetujuan pengurus.'); window.location='perizinan.php';</script>";
    }
}

// PROSES APPROVAL (Untuk Kepala Asrama/Fasilitator/HO/SPV)
if (isset($_GET['aksi']) && isset($_GET['id_izin'])) {
    if ($role_user != 'awardee') {
        $id_iz = $_GET['id_izin'];
        $status = ($_GET['aksi'] == 'setuju') ? 'Disetujui' : 'Ditolak';
        $admin_nama = $u['nama'];

        mysqli_query($conn, "UPDATE perizinan SET status_approval = '$status', disetujui_oleh = '$admin_nama' WHERE id_izin = '$id_iz'");
        header("Location: perizinan.php");
    }
}

// Ambil data izin (Awardee hanya lihat miliknya, Pengurus lihat semua)
if ($role_user == 'awardee') {
    $query_izin = mysqli_query($conn, "SELECT * FROM perizinan WHERE id_user = '$id_user' ORDER BY id_izin DESC");
} else {
    $query_izin = mysqli_query($conn, "SELECT p.*, u.nama FROM perizinan p JOIN users u ON p.id_user = u.id ORDER BY status_approval ASC, id_izin DESC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perizinan - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --navy-theme: #063255; 
            --accent-color: #0284c7; 
            --bg-light: #f4f7fa; 
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Sidebar Layout Solid */
        .sidebar {
            width: 280px; height: 100vh; position: fixed; top: 0; left: 0;
            background: linear-gradient(180deg, #063255 0%, #041f35 100%);
            color: var(--sidebar-text); padding-top: 10px; z-index: 1000; overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-brand { text-align: center; padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .brand-logo { width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); margin-bottom: 12px; object-fit: cover; }
        
        .nav-link-side { color: var(--sidebar-text); padding: 11px 25px; display: flex; align-items: center; transition: all 0.2s ease; font-size: 0.92rem; text-decoration: none; border-left: 4px solid transparent; }
        .nav-link-side i { width: 24px; margin-right: 12px; font-size: 1.05rem; opacity: 0.8; }
        .nav-link-side:hover { color: #fff; background-color: var(--sidebar-hover); padding-left: 28px; }
        .nav-link-side.active { color: #fff; background-color: rgba(2, 132, 199, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }
        
        .main-content { margin-left: 280px; padding: 30px; transition: all 0.3s ease; }
        .card-custom { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid rgba(6, 50, 85, 0.07); padding: 25px; }
        
        /* Tombol & Judul - Tema Biru Navy YBM */
        .btn-primary-ybm { background-color: var(--navy-theme); color: #fff; border: none; transition: all 0.2s ease; }
        .btn-primary-ybm:hover { background-color: #04243e; color: #fff; transform: translateY(-1px); }
        .text-primary-ybm { color: var(--navy-theme) !important; }
        
        /* Badge Custom Mulus */
        .badge-pending { background-color: #fef3c7; color: #d97706; font-weight: 700; }
        .badge-setuju { background-color: #d1fae5; color: #059669; font-weight: 700; }
        .badge-tolak { background-color: #fee2e2; color: #dc2626; font-weight: 700; }
        
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
        <a href="index.php" class="nav-link-side">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link-side">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        
        <?php if ($_SESSION['role'] != 'awardee'): ?>
        <a href="data_awardee.php" class="nav-link-side">
            <i class="fas fa-users"></i> Data Awardee
        </a>
        <?php endif; ?>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px;">Fitur Monitoring</div>
        <a href="amalan.php" class="nav-link-side">
            <i class="fas fa-pray"></i> Spiritual Tracker
        </a>
        <a href="tahfidz.php" class="nav-link-side">
            <i class="fas fa-book-quran"></i> Tahfidz Tracker
        </a>
        <a href="akademik.php" class="nav-link-side">
            <i class="fas fa-graduation-cap"></i> Akademik
        </a>
        <a href="keaktifan.php" class="nav-link-side">
            <i class="fas fa-award"></i> Portofolio
        </a>
        <a href="masyarakat.php" class="nav-link-side">
            <i class="fas fa-people-group"></i> Sosial Masyarakat
        </a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px;">Fitur Asrama</div>
        <a href="inventaris.php" class="nav-link-side">
            <i class="fas fa-boxes-stacked"></i> Inventaris Asrama
        </a>
        <a href="keuangan.php" class="nav-link-side">
            <i class="fas fa-wallet"></i> Keuangan Asrama
        </a>
        <a href="perizinan.php" class="nav-link-side active">
            <i class="fas fa-envelope-open-text"></i> Perizinan Asrama
        </a>

        <a href="logout.php" class="nav-link-side logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Pusat Perizinan Asrama</h3>
                <p class="text-muted m-0 small">Sistem pengajuan mobilitas keluar masuk lingkungan asrama komunal.</p>
            </div>
            <div class="text-end d-none d-md-block">
                <div class="fw-bold text-primary-ybm" style="font-size: 0.95rem;"><?= date('d F Y') ?></div>
                <small class="text-muted" style="font-size: 0.85rem; font-weight: 500;"><?= date('H:i') ?> WIB</small>
            </div>
        </div>

        <div class="row g-4">
            
            <?php if ($role_user == 'awardee'): ?>
            <div class="col-12">
                <div class="card card-custom shadow-sm">
                    <h6 class="fw-bold mb-3 text-dark text-primary-ybm"><i class="fas fa-paper-plane me-2"></i>Ajukan Izin Baru</h6>
                    <form action="" method="POST">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Jenis Izin</label>
                                <select name="jenis_izin" class="form-select form-select-sm" required>
                                    <option value="Pulang Kampung">Pulang Kampung</option>
                                    <option value="Urusan Kampus">Urusan Kampus</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Organisasi">Organisasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-secondary">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary">Alasan Pengajuan</label>
                                <textarea name="alasan" class="form-control form-control-sm" rows="2" placeholder="Tuliskan keterangan keperluan logis..." required></textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" name="ajukan_izin" class="btn btn-primary-ybm btn-sm rounded-pill fw-bold px-4">
                                    Kirim Pengajuan Izin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-12">
                <div class="card card-custom shadow-sm">
                    <h6 class="fw-bold mb-3 text-dark">
                        <?= ($role_user == 'awardee') ? '<i class="fas fa-clock-rotate-left me-2"></i>Riwayat Izin Saya' : '<i class="fas fa-list-check me-2"></i>Daftar Pengajuan Izin Awardee' ?>
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0" style="font-size: 0.82rem;">
                            <thead>
                                <tr class="text-muted text-uppercase" style="font-size: 0.75rem;">
                                    <?php if($role_user != 'awardee'): ?><th>Awardee</th><?php endif; ?>
                                    <th>Jenis Izin</th>
                                    <th>Durasi Waktu</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi / Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if(mysqli_num_rows($query_izin) > 0):
                                    while($row = mysqli_fetch_assoc($query_izin)): 
                                        $badge = ($row['status_approval'] == 'Pending') ? 'badge-pending' : (($row['status_approval'] == 'Disetujui') ? 'badge-setuju' : 'badge-tolak');
                                ?>
                                <tr>
                                    <?php if($role_user != 'awardee'): ?>
                                        <td><span class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></span></td>
                                    <?php endif; ?>
                                    <td class="fw-semibold text-secondary"><?= htmlspecialchars($row['jenis_izin']) ?></td>
                                    <td class="small text-dark">
                                        <i class="far fa-calendar-days text-muted me-1"></i>
                                        <?= date('d/m/y', strtotime($row['tgl_mulai'])) ?> 
                                        <span class="text-muted">s/d</span> 
                                        <?= date('d/m/y', strtotime($row['tgl_selesai'])) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $badge ?> rounded-pill px-2.5 py-1.5" style="font-size: 0.72rem;">
                                            <?= $row['status_approval'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if($role_user != 'awardee' && $row['status_approval'] == 'Pending'): ?>
                                            <div class="btn-group btn-group-sm">
                                                <a href="?aksi=setuju&id_izin=<?= $row['id_izin'] ?>" class="btn btn-success rounded-circle me-1" title="Setujui" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-check" style="font-size: 0.75rem;"></i>
                                                </a>
                                                <a href="?aksi=tolak&id_izin=<?= $row['id_izin'] ?>" class="btn btn-danger rounded-circle" title="Tolak" onclick="return confirm('Tolak izin ini?')" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-times" style="font-size: 0.75rem;"></i>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <button class="btn btn-light btn-sm fw-semibold text-secondary px-2.5 rounded-pill border" style="font-size: 0.72rem;" title="Alasan: <?= htmlspecialchars($row['alasan']) ?>" onclick="alert('Alasan Izin:\n<?= htmlspecialchars($row['alasan']) ?>\n\n<?= $row['disetujui_oleh'] ? 'Diproses oleh: '.htmlspecialchars($row['disetujui_oleh']) : '' ?>')">
                                                <i class="fas fa-circle-info me-1"></i> Detail
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4 small">Belum ada riwayat rekaman perizinan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>