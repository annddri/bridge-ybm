<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] == 'awardee') {
    header("Location: absensi.php");
    exit;
}

$id_keg = $_GET['id'];
// Ambil detail kegiatan
$kegiatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kegiatan WHERE id_kegiatan = '$id_keg'"));
$tgl_keg = $kegiatan['tanggal_kegiatan'];

// Ambil semua daftar awardee untuk dibandingkan dengan data absen
$query_awardee = mysqli_query($conn, "SELECT id, nama FROM users WHERE role = 'awardee' ORDER BY nama ASC");

// Ambil data user login untuk sidebar
$id_log = $_SESSION['id_user'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_log'"));
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi - <?= $kegiatan['nama_kegiatan'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0b141d; --sidebar-text: #ffffff; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; }
        .main-content { margin-left: 280px; padding: 30px; }
        .card-rekap { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
        .status-hadir { color: #198754; font-weight: bold; }
        .status-izin { color: #0dcaf0; font-weight: bold; }
        .status-alpa { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="text-center p-3 border-bottom border-secondary">
        <img src="<?= $foto_path ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
        <h5 class="fw-bold mt-2 text-white"><?= $u['nama'] ?></h5>
        <small class="text-info"><?= strtoupper($u['role']) ?></small>
    </div>
    <div class="mt-4">
        <a href="absensi.php" class="nav-link text-white px-4 text-decoration-none"><i class="fas fa-arrow-left me-3"></i> Kembali</a>
    </div>
</div>

<div class="main-content">
    <div class="mb-4">
        <h3 class="fw-bold">Rekap Presensi Peserta</h3>
        <p class="text-muted"><?= $kegiatan['nama_kegiatan'] ?> | <?= date('d M Y', strtotime($tgl_keg)) ?></p>
    </div>

    <div class="card card-rekap p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Awardee</th>
                        <th>Waktu Presensi</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while($a = mysqli_fetch_assoc($query_awardee)): 
                        $id_aw = $a['id'];
                        
                        // 1. Cek apakah ada di tabel absensi (Hadir)
                        $cek_hadir = mysqli_query($conn, "SELECT * FROM absensi WHERE id_kegiatan = '$id_keg' AND id_user = '$id_aw'");
                        $data_h = mysqli_fetch_assoc($cek_hadir);

                        // 2. Cek apakah ada izin yang disetujui pada tanggal ini
                        $cek_izin = mysqli_query($conn, "SELECT * FROM perizinan WHERE id_user = '$id_aw' AND status_approval = 'Disetujui' AND '$tgl_keg' BETWEEN tgl_mulai AND tgl_selesai");
                        $data_i = mysqli_fetch_assoc($cek_izin);

                        // Penentuan Status Akhir
                        if (mysqli_num_rows($cek_hadir) > 0) {
                            $status = "Hadir";
                            $waktu = date('H:i', strtotime($data_h['waktu_presensi'])) . " WIB";
                            $class = "status-hadir";
                            $ket = "Melakukan presensi mandiri";
                        } elseif (mysqli_num_rows($cek_izin) > 0) {
                            $status = "Izin";
                            $waktu = "-";
                            $class = "status-izin";
                            $ket = "Izin Resmi: " . $data_i['jenis_izin'];
                        } else {
                            $status = "Alpa";
                            $waktu = "-";
                            $class = "status-alpa";
                            $ket = "Tanpa keterangan";
                        }
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="fw-semibold"><?= $a['nama'] ?></td>
                        <td><?= $waktu ?></td>
                        <td><span class="<?= $class ?>"><?= $status ?></span></td>
                        <td class="small text-muted"><?= $ket ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

</body>
</html>