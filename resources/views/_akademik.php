<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) { 
    header("Location: login.php"); 
    exit; 
}

$id_logon = $_SESSION['id_user'];
$role_logon = $_SESSION['role']; // Nilainya 'Admin' atau 'awardee'
$pesan = "";

// Ambil data user aktif untuk Sidebar Pemantauan
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_logon'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// --- LOGIKA 1: PROSES SIMPAN IP SEMESTER (Khusus Awardee) ---
if (isset($_POST['simpan_ip'])) {
    $semester = mysqli_real_escape_string($conn, $_POST['semester']);
    $ip = mysqli_real_escape_string($conn, $_POST['ip']);
    $file_khs = "NULL";

    if (!empty($_FILES['bukti_khs']['name'])) {
        $nama_file = "khs_smstr".$semester."_".time().".".pathinfo($_FILES['bukti_khs']['name'], PATHINFO_EXTENSION);
        if(move_uploaded_file($_FILES['bukti_khs']['tmp_name'], "uploads/".$nama_file)) { 
            $file_khs = "'$nama_file'"; 
        }
    }

    $query = "INSERT INTO akademik (id_user, semester, ip, file_verifikasi, status) VALUES ('$id_logon', '$semester', '$ip', $file_khs, 'Belum Lulus')";
    mysqli_query($conn, $query);
    $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center'><i class='fas fa-check-circle me-2'></i> Data IP Semester $semester berhasil dikirim! Menunggu verifikasi.</div>";
}

// --- LOGIKA 2: PROSES SIMPAN RIWAYAT TOEFL (Khusus Awardee) ---
if (isset($_POST['simpan_toefl'])) {
    $score = mysqli_real_escape_string($conn, $_POST['score']);
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis_tes']);
    $file_toefl = "NULL";

    if (!empty($_FILES['bukti_toefl']['name'])) {
        $nama_file_t = "toefl_".time().".".pathinfo($_FILES['bukti_toefl']['name'], PATHINFO_EXTENSION);
        if(move_uploaded_file($_FILES['bukti_toefl']['tmp_name'], "uploads/".$nama_file_t)) { 
            $file_toefl = "'$nama_file_t'"; 
        }
    }

    $query = "INSERT INTO toefl (id_user, score, jenis_tes, file_sertifikat, status) VALUES ('$id_logon', '$score', '$jenis', $file_toefl, 'Belum Lulus')";
    mysqli_query($conn, $query);
    $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center'><i class='fas fa-check-circle me-2'></i> Riwayat TOEFL berhasil ditambahkan! Menunggu verifikasi.</div>";
}

// --- LOGIKA 3: PROSES VERIFIKASI AKADEMIK & TOEFL (Khusus Admin) ---
if (isset($_GET['aksi']) && $role_logon == 'Admin') {
    $id_target = mysqli_real_escape_string($conn, $_GET['id']);
    $status_baru = mysqli_real_escape_string($conn, $_GET['status']);
    
    if ($_GET['aksi'] == 'update_ip') {
        mysqli_query($conn, "UPDATE akademik SET status='$status_baru' WHERE id='$id_target'");
    } elseif ($_GET['aksi'] == 'update_toefl') {
        mysqli_query($conn, "UPDATE toefl SET status='$status_baru' WHERE id='$id_target'");
    }
    header("Location: akademik.php");
    exit;
}

// Hitung IPK Otomatis Kumulatif (Untuk Awardee melihat nilainya sendiri)
$res_ipk = mysqli_query($conn, "SELECT AVG(ip) as ipk_total FROM akademik WHERE id_user = '$id_logon'");
$ipk_sekarang = number_format(mysqli_fetch_assoc($res_ipk)['ipk_total'] ?? 0, 2);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademik - BRIGHT</title>
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
        
        /* Sidebar Navigasi */
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

        /* Main Layout */
        .main-content { margin-left: 280px; padding: 0; transition: all 0.3s ease; }
        .content-body { padding: 30px; } 

        .tracker-header-box {
            background: #ffffff;
            border: 1px solid rgba(6, 50, 85, 0.07);
            box-shadow: 0 10px 25px rgba(6, 50, 85, 0.05);
        }
        .header-title { color: #041f35; font-size: 1.5rem; letter-spacing: 0.5px; }
        .header-subtitle { color: #64748b; font-size: 0.85rem; }
        
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
            padding: 5px 10px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.72rem;
            display: inline-block;
        }
        .status-lulus { background-color: #198754; color: white; }
        .status-pending { background-color: #ffc107; color: #212529; }

        .btn-action {
            padding: 4px 12px;
            font-size: 0.72rem;
            font-weight: 600;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-kembali {
            background-color: #212529;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 7px 24px;
            border-radius: 30px;
            border: none;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        .btn-kembali:hover { background-color: #000000; }

        @media (max-width: 991.98px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
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
        <a href="index.php" class="nav-link">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>
        
        <?php if ($role_logon == 'Admin'): ?>
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
        <a href="akademik.php" class="nav-link active">
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
        
        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-graduation-cap fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">ACADEMIC PERFORMANCE</h4>
                        <p class="m-0 header-subtitle">Monitoring Nilai IP Semester, IPK Kumulatif, dan Riwayat Capaian TOEFL</p>
                    </div>
                </div>
                <div>
                    <a href="index.php" class="btn-kembali shadow-sm">Kembali</a>
                </div>
            </div>
        </div>
        
        <?php echo $pesan; ?>

        <div class="row g-4">
            <?php if ($role_logon != 'Admin') : ?>
            <div class="col-xl-4 col-lg-5">
                <div class="card live-card border-0 p-4 rounded-4 mb-4">
                    <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">Input IP Semester</h6>
                    <hr class="text-muted opacity-25 my-3">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label text-muted small mb-1 fw-semibold">Semester</label>
                                <input type="number" name="semester" class="form-control form-control-sm rounded-3" placeholder="Sms" min="1" max="8" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label text-muted small mb-1 fw-semibold">IP Semester</label>
                                <input type="number" step="0.01" name="ip" class="form-control form-control-sm rounded-3" placeholder="Contoh: 3.75" min="0" max="4" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1 fw-semibold">Berkas KHS (PDF/Gambar)</label>
                            <input type="file" name="bukti_khs" class="form-control form-control-sm rounded-3">
                        </div>
                        <button type="submit" name="simpan_ip" class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2" style="background-color: var(--navy-theme); border-color: var(--navy-theme);">Simpan IP</button>
                    </form>
                </div>

                <div class="card live-card border-0 p-4 rounded-4">
                    <h6 class="fw-bold mb-0" style="color: #198754;">Input Riwayat TOEFL</h6>
                    <hr class="text-muted opacity-25 my-3">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Skor TOEFL</label>
                                <input type="number" name="score" class="form-control form-control-sm rounded-3" placeholder="Score" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Jenis Tes</label>
                                <select name="jenis_tes" class="form-select form-select-sm rounded-3" required>
                                    <option value="Pre-Test">Pre-Test</option>
                                    <option value="Post-Test">Post-Test</option>
                                    <option value="Real Test">Real Test</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1 fw-semibold">Berkas Sertifikat</label>
                            <input type="file" name="bukti_toefl" class="form-control form-control-sm rounded-3">
                        </div>
                        <button type="submit" name="simpan_toefl" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold py-2">Tambah Riwayat TOEFL</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="<?php echo ($role_logon == 'Admin') ? 'col-12' : 'col-xl-8 col-lg-7'; ?>">
                
                <div class="card live-card border-0 p-4 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold m-0" style="color: var(--navy-theme);">Riwayat IP Semester</h6>
                        <?php if ($role_logon != 'Admin') : ?>
                            <span class="badge bg-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">IPK Kumulatif: <?php echo $ipk_sekarang; ?></span>
                        <?php endif; ?>
                    </div>
                    <hr class="text-muted opacity-25 my-3">
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <?php if ($role_logon == 'Admin') : ?>
                                        <th class="border-0 px-3 py-3">Nama Mahasiswa</th>
                                    <?php endif; ?>
                                    <th class="border-0 py-3 <?php echo ($role_logon != 'Admin') ? 'px-3' : ''; ?>">Semester</th>
                                    <th class="border-0 py-3 text-center">IP Semester</th>
                                    <th class="border-0 py-3 text-center">Bukti KHS</th>
                                    <th class="border-0 py-3 text-center">Status</th>
                                    <?php if ($role_logon == 'Admin') echo "<th class='border-0 py-3 text-center'>Aksi</th>"; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($role_logon == 'Admin') {
                                    $sql_ak = "SELECT akademik.*, users.nama FROM akademik JOIN users ON akademik.id_user = users.id ORDER BY akademik.id DESC";
                                } else {
                                    $sql_ak = "SELECT akademik.*, users.nama FROM akademik JOIN users ON akademik.id_user = users.id WHERE akademik.id_user = '$id_logon' ORDER BY akademik.semester ASC";
                                }
                                
                                $q_ak = mysqli_query($conn, $sql_ak);
                                if(mysqli_num_rows($q_ak) == 0) {
                                    $col_span = ($role_logon == 'Admin') ? 6 : 4;
                                    echo "<tr><td colspan='$col_span' class='text-center text-muted py-4'>Belum ada data IP terdata.</td></tr>";
                                }

                                while($r = mysqli_fetch_assoc($q_ak)):
                                ?>
                                <tr>
                                    <?php if ($role_logon == 'Admin') : ?>
                                        <td class="px-3 fw-semibold" style="color: #334155;"><?php echo htmlspecialchars($r['nama']); ?></td>
                                    <?php endif; ?>
                                    
                                    <td class="<?php echo ($role_logon != 'Admin') ? 'px-3' : ''; ?>">
                                        <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold">Semester <?php echo $r['semester']; ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-primary"><?php echo number_format($r['ip'], 2); ?></td>
                                    <td class="text-center">
                                        <?php if($r['file_verifikasi'] != 'NULL' && !empty($r['file_verifikasi'])): ?>
                                            <a href="uploads/<?php echo str_replace("'","",$r['file_verifikasi']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.75rem;">
                                                <i class="fas fa-eye me-1"></i> File
                                            </a>
                                        <?php else: ?>-<?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($r['status']) && $r['status'] == 'Lulus') : ?>
                                            <span class="badge-status status-lulus"><i class="fas fa-check me-1"></i> Valid</span>
                                        <?php else : ?>
                                            <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <?php if ($role_logon == 'Admin') : ?>
                                    <td class="text-center">
                                        <?php if (!isset($r['status']) || $r['status'] != 'Lulus') : ?>
                                            <a href="akademik.php?aksi=update_ip&id=<?php echo $r['id']; ?>&status=Lulus" class="btn btn-action btn-success shadow-sm">
                                                <i class="fas fa-check me-1"></i> Validkan
                                            </a>
                                        <?php else : ?>
                                            <a href="akademik.php?aksi=update_ip&id=<?php echo $r['id']; ?>&status=Belum Lulus" class="btn btn-action btn-danger shadow-sm">
                                                <i class="fas fa-undo me-1"></i> Batalkan
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card live-card border-0 p-4 rounded-4">
                    <h6 class="fw-bold mb-0" style="color: #198754;">Riwayat Skor TOEFL</h6>
                    <hr class="text-muted opacity-25 my-3">
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <?php if ($role_logon == 'Admin') : ?>
                                        <th class="border-0 px-3 py-3">Nama Mahasiswa</th>
                                    <?php endif; ?>
                                    <th class="border-0 py-3 <?php echo ($role_logon != 'Admin') ? 'px-3' : ''; ?>">Jenis Tes</th>
                                    <th class="border-0 py-3 text-center">Skor</th>
                                    <th class="border-0 py-3 text-center">Sertifikat</th>
                                    <th class="border-0 py-3 text-center">Status</th>
                                    <?php if ($role_logon == 'Admin') echo "<th class='border-0 py-3 text-center'>Aksi</th>"; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($role_logon == 'Admin') {
                                    $sql_tf = "SELECT toefl.*, users.nama FROM toefl JOIN users ON toefl.id_user = users.id ORDER BY toefl.id DESC";
                                } else {
                                    $sql_tf = "SELECT toefl.*, users.nama FROM toefl JOIN users ON toefl.id_user = users.id WHERE toefl.id_user = '$id_logon' ORDER BY toefl.id ASC";
                                }
                                
                                $q_tf = mysqli_query($conn, $sql_tf);
                                if(mysqli_num_rows($q_tf) == 0) {
                                    $col_span = ($role_logon == 'Admin') ? 6 : 4;
                                    echo "<tr><td colspan='$col_span' class='text-center text-muted py-4'>Belum ada data skor TOEFL terdata.</td></tr>";
                                }

                                while($rt = mysqli_fetch_assoc($q_tf)):
                                ?>
                                <tr>
                                    <?php if ($role_logon == 'Admin') : ?>
                                        <td class="px-3 fw-semibold" style="color: #334155;"><?php echo htmlspecialchars($rt['nama']); ?></td>
                                    <?php endif; ?>
                                    
                                    <td class="<?php echo ($role_logon != 'Admin') ? 'px-3' : ''; ?>">
                                        <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold"><?php echo htmlspecialchars($rt['jenis_tes']); ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-success"><?php echo $rt['score']; ?></td>
                                    <td class="text-center">
                                        <?php if($rt['file_sertifikat'] != 'NULL' && !empty($rt['file_sertifikat'])): ?>
                                            <a href="uploads/<?php echo str_replace("'","",$rt['file_sertifikat']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.75rem;">
                                                <i class="fas fa-eye me-1"></i> File
                                            </a>
                                        <?php else: ?>-<?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($rt['status']) && $rt['status'] == 'Lulus') : ?>
                                            <span class="badge-status status-lulus"><i class="fas fa-check me-1"></i> Valid</span>
                                        <?php else : ?>
                                            <span class="badge-status status-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <?php if ($role_logon == 'Admin') : ?>
                                    <td class="text-center">
                                        <?php if (!isset($rt['status']) || $rt['status'] != 'Lulus') : ?>
                                            <a href="akademik.php?aksi=update_toefl&id=<?php echo $rt['id']; ?>&status=Lulus" class="btn btn-action btn-success shadow-sm">
                                                <i class="fas fa-check me-1"></i> Validkan
                                            </a>
                                        <?php else : ?>
                                            <a href="akademik.php?aksi=update_toefl&id=<?php echo $rt['id']; ?>&status=Belum Lulus" class="btn btn-action btn-danger shadow-sm">
                                                <i class="fas fa-undo me-1"></i> Batalkan
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
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