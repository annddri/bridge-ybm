<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];
$hari_ini = date('Y-m-d');

// Ambil data user untuk sidebar
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// 1. PROSES ABSEN (Klik Hadir oleh Awardee)
if (isset($_GET['do_absen']) && $role_user == 'awardee') {
    $id_keg = $_GET['do_absen'];
    
    // Cek apakah sudah absen sebelumnya
    $cek = mysqli_query($conn, "SELECT * FROM absensi WHERE id_kegiatan = '$id_keg' AND id_user = '$id_user'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO absensi (id_kegiatan, id_user, status_kehadiran) VALUES ('$id_keg', '$id_user', 'Hadir')");
        echo "<script>alert('Presensi berhasil dicatat!'); window.location='absensi.php';</script>";
    }
}

// 2. PROSES BUAT KEGIATAN (Oleh Pengurus)
if (isset($_POST['tambah_kegiatan']) && $role_user != 'awardee') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kegiatan']);
    $tgl = $_POST['tanggal_kegiatan'];
    $jam = $_POST['waktu_mulai'];
    $lok = mysqli_real_escape_string($conn, $_POST['lokasi']);
    
    mysqli_query($conn, "INSERT INTO kegiatan (nama_kegiatan, tanggal_kegiatan, waktu_mulai, lokasi) VALUES ('$nama', '$tgl', '$jam', '$lok')");
    header("Location: absensi.php");
}

// Ambil daftar kegiatan
$query_kegiatan = mysqli_query($conn, "SELECT * FROM kegiatan ORDER BY tanggal_kegiatan DESC, waktu_mulai DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi Kegiatan - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0b141d; --sidebar-text: #ffffff; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; z-index: 1000; }
        .brand-logo { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #2e5a88; margin-bottom: 15px; object-fit: cover; }
        .nav-link-side { color: rgba(255,255,255,0.8); padding: 12px 25px; display: flex; align-items: center; text-decoration: none; transition: 0.3s; }
        .nav-link-side.active { border-left: 4px solid #3b82f6; background-color: #1a2a3a; color: #fff; }
        .main-content { margin-left: 280px; padding: 30px; }
        .card-kegiatan { border: none; border-radius: 15px; transition: 0.3s; }
        .card-kegiatan:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="text-center p-3 border-bottom border-secondary">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" class="brand-logo shadow">
        <h5 class="fw-bold m-0 text-white"><?= htmlspecialchars($u['nama']) ?></h5>
        <small class="text-info fw-bold"><?= strtoupper($u['role']) ?></small>
    </div>
    <div class="mt-4">
        <a href="index.php" class="nav-link-side"><i class="fas fa-home me-3"></i> Home</a>
        <a href="absensi.php" class="nav-link-side active"><i class="fas fa-calendar-check me-3"></i> Presensi</a>
        <a href="perizinan.php" class="nav-link-side"><i class="fas fa-envelope-open-text me-3"></i> Perizinan</a>
    </div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Presensi Kegiatan Asrama</h3>
        <?php if($role_user != 'awardee'): ?>
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
                <i class="fas fa-plus me-2"></i> Buat Kegiatan
            </button>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php while($k = mysqli_fetch_assoc($query_kegiatan)): 
            // Cek status absen user ini untuk kegiatan ini
            $id_keg = $k['id_kegiatan'];
            $cek_absen = mysqli_query($conn, "SELECT status_kehadiran FROM absensi WHERE id_kegiatan = '$id_keg' AND id_user = '$id_user'");
            $data_absen = mysqli_fetch_assoc($cek_absen);
            $sudah_absen = mysqli_num_rows($cek_absen) > 0;
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card card-kegiatan shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($k['tanggal_kegiatan'])) ?>
                    </span>
                    <small class="text-muted fw-bold"><i class="far fa-clock me-1"></i> <?= $k['waktu_mulai'] ?></small>
                </div>
                <h5 class="fw-bold text-dark"><?= $k['nama_kegiatan'] ?></h5>
                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i> <?= $k['lokasi'] ?></p>
                
                <div class="mt-auto">
                    <?php if($role_user == 'awardee'): ?>
                        <?php if($sudah_absen): ?>
                            <button class="btn btn-success w-100 rounded-pill disabled">
                                <i class="fas fa-check-circle me-2"></i> Terabsen: <?= $data_absen['status_kehadiran'] ?>
                            </button>
                        <?php elseif($k['tanggal_kegiatan'] == $hari_ini): ?>
                            <a href="?do_absen=<?= $k['id_kegiatan'] ?>" class="btn btn-outline-primary w-100 rounded-pill">
                                <i class="fas fa-fingerprint me-2"></i> Klik Hadir Sekarang
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light w-100 rounded-pill disabled text-muted">Belum Dimulai</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="detail_absensi.php?id=<?= $k['id_kegiatan'] ?>" class="btn btn-info text-white w-100 rounded-pill">
                            <i class="fas fa-users me-2"></i> Lihat Rekap Peserta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="modalKegiatan" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" class="modal-content">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Buat Jadwal Kegiatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="form-control" placeholder="Contoh: Kajian Malam Rabu" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_kegiatan" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="waktu_mulai" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" value="Asrama" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="tambah_kegiatan" class="btn btn-primary w-100 rounded-pill">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>