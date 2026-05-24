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

// 1. Ambil data & hitung total Dana Operasional
$query_ops = mysqli_query($conn, "SELECT * FROM dana_operasional ORDER BY tanggal DESC, id_ops DESC");
$total_ops_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM dana_operasional WHERE jenis_transaksi='Masuk'"))['total'] ?: 0;
$total_ops_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM dana_operasional WHERE jenis_transaksi='Keluar'"))['total'] ?: 0;
$saldo_ops = $total_ops_masuk - $total_ops_keluar;

// 2. Ambil data & hitung total Kas Asrama
$query_kas = mysqli_query($conn, "SELECT * FROM kas_asrama ORDER BY tanggal DESC, id_kas DESC");
$total_kas_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM kas_asrama WHERE jenis_transaksi='Masuk'"))['total'] ?: 0;
$total_kas_keluar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(nominal) as total FROM kas_asrama WHERE jenis_transaksi='Keluar'"))['total'] ?: 0;
$saldo_kas = $total_kas_masuk - $total_kas_keluar;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan Asrama - Bright Asrama</title>
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
        
        /* Keuangan Card Styling */
        .finance-card { border: none; border-radius: 12px; transition: transform 0.2s; color: white; }
        .finance-card:hover { transform: translateY(-3px); }
        .table-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid rgba(6, 50, 85, 0.07); padding: 25px; }
        
        .nav-pills .nav-link { color: var(--navy-theme); font-weight: 600; background-color: #fff; border: 1px solid var(--navy-theme); margin-right: 10px; }
        .nav-pills .nav-link.active { background-color: var(--navy-theme); color: #fff; border-color: var(--navy-theme); }
        .btn-primary-ybm { background-color: var(--navy-theme); color: #fff; border: none; transition: all 0.2s ease; }
        .btn-primary-ybm:hover { background-color: #04243e; color: #fff; transform: translateY(-1px); }
        
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
        <a href="keuangan.php" class="nav-link-side active">
            <i class="fas fa-wallet"></i> Keuangan Asrama
        </a>
        <a href="perizinan.php" class="nav-link-side">
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
                <h3 class="fw-bold text-dark m-0">Keuangan Asrama</h3>
                <p class="text-muted m-0 small">Manajemen terpisah antara Dana Operasional Yayasan dan Uang Kas Mandiri.</p>
            </div>
            <div class="text-end d-none d-md-block">
                <div class="fw-bold" style="color: var(--navy-theme); font-size: 0.95rem;"><?= date('d F Y') ?></div>
                <small class="text-muted" style="font-size: 0.85rem; font-weight: 500;"><?= date('H:i') ?> WIB</small>
            </div>
        </div>

        <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4" id="pills-ops-tab" data-bs-toggle="pill" data-bs-target="#pills-ops" type="button" role="tab"><i class="fas fa-building-columns me-2"></i> Dana Operasional Yayasan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4" id="pills-kas-tab" data-bs-toggle="pill" data-bs-target="#pills-kas" type="button" role="tab"><i class="fas fa-hand-holding-dollar me-2"></i> Uang Kas Asrama (Komunal)</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">
            
            <div class="tab-pane fade show active" id="pills-ops" role="tabpanel">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card finance-card bg-primary p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Total Dana Masuk</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($total_ops_masuk, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card finance-card bg-danger p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Total Pengeluaran</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($total_ops_keluar, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card finance-card bg-success p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Sisa Saldo Operasional</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($saldo_ops, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="card table-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-dark">Riwayat Log Transaksi Operasional</h6>
                        <?php if ($role_user == 'kepala_asrama'): ?>
                            <a href="tambah_ops.php" class="btn btn-primary-ybm btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-plus me-1"></i> Catat Transaksi</a>
                        <?php else: ?>
                            <span class="badge bg-secondary p-2 small"><i class="fas fa-lock me-1"></i> Read-Only (Akses Terkunci)</span>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0" style="font-size: 0.82rem;">
                            <thead>
                                <tr class="table-light text-muted">
                                    <th>Tanggal</th>
                                    <th>Kategori/Alokasi</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Pencatat</th>
                                    <?php if ($role_user == 'kepala_asrama'): ?><th class="text-center">Aksi</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($query_ops)): ?>
                                <tr>
                                    <td class="text-muted"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['kategori']) ?></td>
                                    <td>
                                        <span class="badge <?= $row['jenis_transaksi'] == 'Masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?> px-2 py-1">
                                            <?= $row['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                    <td class="small fw-semibold text-secondary"><?= htmlspecialchars($row['updated_by']) ?></td>
                                    <?php if ($role_user == 'kepala_asrama'): ?>
                                    <td class="text-center">
                                        <a href="edit_ops.php?id=<?= $row['id_ops'] ?>" class="text-warning me-2" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="hapus_ops.php?id=<?= $row['id_ops'] ?>" class="text-danger" onclick="return confirm('Hapus log transaksi ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; if(mysqli_num_rows($query_ops) == 0): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat transaksi operasional.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pills-kas" role="tabpanel">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card finance-card bg-info p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Total Iuran Masuk</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($total_kas_masuk, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card finance-card bg-danger p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Total Pengeluaran Kas</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($total_kas_keluar, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card finance-card bg-success p-3 shadow-sm">
                            <small class="opacity-75 fw-semibold">Sisa Saldo Kas Komunal</small>
                            <h3 class="fw-bold m-0">Rp <?= number_format($saldo_kas, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>

                <div class="card table-card shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0 text-dark">Buku Kas Komunal Awardee</h6>
                        <?php if ($role_user == 'awardee'): ?>
                            <a href="tambah_kas.php" class="btn btn-info text-white btn-sm rounded-pill px-3 fw-bold"><i class="fas fa-plus me-1"></i> Input Transaksi Kas</a>
                        <?php else: ?>
                            <span class="badge bg-secondary p-2 small"><i class="fas fa-eye me-1"></i> Mode Pantau (Khusus Pengurus/HO)</span>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0" style="font-size: 0.82rem;">
                            <thead>
                                <tr class="table-light text-muted">
                                    <th>Tanggal</th>
                                    <th>Pembayar/Tujuan</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                    <th>Penanggung Jawab</th>
                                    <?php if ($role_user == 'awardee'): ?><th class="text-center">Aksi</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($query_kas)): ?>
                                <tr>
                                    <td class="text-muted"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_awardee'] ?: '-') ?></td>
                                    <td>
                                        <span class="badge <?= $row['jenis_transaksi'] == 'Masuk' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?> px-2 py-1">
                                            <?= $row['jenis_transaksi'] ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark">Rp <?= number_format($row['nominal'], 0, ',', '.') ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                                    <td class="small fw-semibold text-secondary"><?= htmlspecialchars($row['updated_by']) ?></td>
                                    <?php if ($role_user == 'awardee'): ?>
                                    <td class="text-center">
                                        <a href="edit_kas.php?id=<?= $row['id_kas'] ?>" class="text-warning me-2" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="hapus_kas.php?id=<?= $row['id_kas'] ?>" class="text-danger" onclick="return confirm('Hapus transaksi kas ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; if(mysqli_num_rows($query_kas) == 0): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada catatan buku kas bulanan.</td></tr>
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