<?php
session_start();
include 'config/koneksi.php';

// Cek login
if (!isset($_SESSION['id_user'])) { 
    header("Location: login.php"); 
    exit; 
}

$id_logon = $_SESSION['id_user'];
$role_logon = $_SESSION['role']; 
$pesan = "";

// Ambil data user aktif untuk Sidebar & Role Check
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_logon'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// --- LOGIKA 1: PROSES VERIFIKASI ADMIN (Validkan / Batalkan) ---
if ($role_logon == 'Admin' && isset($_GET['action']) && isset($_GET['id'])) {
    $id_target = intval($_GET['id']);
    $action = $_GET['action'];
    $status_baru = ($action == 'valid') ? 'Lulus' : 'Belum Lulus';
    
    $q_update = "UPDATE keaktifan_kampus SET status = '$status_baru' WHERE id = '$id_target'";
    if (mysqli_query($conn, $q_update)) {
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center'><i class='fas fa-check-circle me-2'></i> Status verifikasi keaktifan berhasil diperbarui!</div>";
    }
}

// --- LOGIKA 2: PROSES SIMPAN DATA (AWARDEE) ---
if (isset($_POST['simpan_keaktifan'])) {
    $kategori = $_POST['kategori'];
    $tgl_thn = mysqli_real_escape_string($conn, $_POST['tanggal_tahun']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_kegiatan']);
    $peny_jab = mysqli_real_escape_string($conn, $_POST['penyelenggara_jabatan']);
    $level = $_POST['level'];
    
    $query = "INSERT INTO keaktifan_kampus (id_user, kategori, tanggal_tahun, nama_kegiatan, penyelenggara_jabatan, level, status) 
              VALUES ('$id_logon', '$kategori', '$tgl_thn', '$nama', '$peny_jab', '$level', 'Belum Lulus')";
    
    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center'>
                    <i class='fas fa-check-circle me-2'></i> <strong>Berhasil!</strong> Data $kategori telah diajukan ke admin.
                  </div>";
    } else {
        $pesan = "<div class='alert alert-danger shadow-sm'>Gagal menyimpan: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Keaktifan - BRIGHT</title>
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
        .nav-link.active { color: #fff; background-color: rgba(13, 110, 253, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }

        .main-content { margin-left: 280px; padding: 0; transition: all 0.3s ease; }
        .content-body { padding: 30px; } 

        .tracker-header-box { background: #ffffff; border: 1px solid rgba(6, 50, 85, 0.07); box-shadow: 0 10px 25px rgba(6, 50, 85, 0.05); }
        .header-title { color: #041f35; font-size: 1.5rem; letter-spacing: 0.5px; }
        .header-subtitle { color: #64748b; font-size: 0.85rem; }
        
        .live-card { background: #ffffff; border: 1px solid rgba(6, 50, 85, 0.07); box-shadow: 0 12px 30px rgba(6, 50, 85, 0.08); }
        .nav-pills .nav-link { color: #6c757d; font-weight: 600; padding: 8px 18px; border-radius: 8px; font-size: 0.82rem; }
        .nav-pills .nav-link.active { background-color: var(--navy-theme); color: white; }
        .table > thead { background-color: #f8fafc; color: var(--navy-theme); font-weight: 700; }

        .badge-status { padding: 5px 10px; border-radius: 30px; font-weight: 600; font-size: 0.72rem; display: inline-block; }
        .status-lulus { background-color: #198754; color: white; }
        .status-pending { background-color: #ffc107; color: #212529; }

        .btn-kembali { background-color: #212529; color: #ffffff !important; font-weight: 600; font-size: 0.85rem; padding: 7px 24px; border-radius: 30px; border: none; text-decoration: none; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); }
        .btn-kembali:hover { background-color: #000000; }

        @media (max-width: 991.98px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } }
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
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profil Saya</a>
        <?php if ($role_logon != 'awardee'): ?>
            <a href="data_awardee.php" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        <?php endif; ?>
        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; font-size: 0.75rem;">Fitur Monitoring</div>
        <a href="amalan.php" class="nav-link"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="tahfidz.php" class="nav-link"><i class="fas fa-book-quran"></i> Tahfidz Tracker</a>
        <a href="akademik.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Akademik</a>
        <a href="keaktifan.php" class="nav-link active"><i class="fas fa-award"></i> Portofolio</a>
        <a href="masyarakat.php" class="nav-link"><i class="fas fa-people-group"></i> Sosial Masyarakat</a>
        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; font-size: 0.75rem;">Fitur Asrama</div>
        <a href="inventaris.php" class="nav-link"><i class="fas fa-boxes-stacked"></i> Inventaris Asrama</a>
        <a href="keuangan.php" class="nav-link"><i class="fas fa-wallet"></i> Keuangan Asrama</a>
        <a href="perizinan.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Perizinan Asrama</a>
        <a href="logout.php" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        
        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-award fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">PORTOFOLIO KEAKTIFAN</h4>
                        <p class="m-0 header-subtitle">Kelola riwayat organisasi, prestasi, dan seminar awardee beasiswa.</p>
                    </div>
                </div>
                <div><a href="index.php" class="btn-kembali shadow-sm">Kembali</a></div>
            </div>
        </div>
        
        <?php echo $pesan; ?>

        <?php if ($role_logon == 'awardee') : ?>
        <div class="card live-card border-0 p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-plus-circle me-1"></i> Tambah Data Baru</h6>
            <hr class="text-muted opacity-25 my-3">
            <form action="" method="POST" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm rounded-3" required>
                        <option value="prestasi">🏆 Prestasi</option>
                        <option value="organisasi">👥 Organisasi</option>
                        <option value="workshop">📚 Workshop/Seminar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Waktu (Tgl/Thn)</label>
                    <input type="text" name="tanggal_tahun" class="form-control form-control-sm rounded-3" placeholder="2025 atau 13/09/2025" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" class="form-control form-control-sm rounded-3" placeholder="Nama Lomba/Org" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Penyelenggara / Jabatan</label>
                    <input type="text" name="penyelenggara_jabatan" class="form-control form-control-sm rounded-3" placeholder="Posisi kamu atau instansi" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Level</label>
                    <select name="level" class="form-select form-select-sm rounded-3">
                        <option>Lokal/Kampus</option>
                        <option>Kota/Kabupaten</option>
                        <option>Provinsi</option>
                        <option>Nasional</option>
                        <option>Internasional</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" name="simpan_keaktifan" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold py-2 shadow-sm" style="background-color: var(--navy-theme); border-color: var(--navy-theme);">Simpan Data Portofolio</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="card live-card border-0 p-4 rounded-4">
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="pills-prestasi-tab" data-bs-toggle="pill" data-bs-target="#pills-prestasi" type="button">🏆 Prestasi</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="pills-organisasi-tab" data-bs-toggle="pill" data-bs-target="#pills-organisasi" type="button">👥 Organisasi</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="pills-workshop-tab" data-bs-toggle="pill" data-bs-target="#pills-workshop" type="button">📚 Seminar/Workshop</button>
                </li>
            </ul>
            
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-prestasi">
                    <h6 class="fw-bold mb-3 text-dark">Daftar Riwayat Prestasi</h6>
                    <?php tampilkanTabel($conn, $id_logon, 'prestasi', $role_logon); ?>
                </div>

                <div class="tab-pane fade" id="pills-organisasi">
                    <h6 class="fw-bold mb-3 text-dark">Daftar Riwayat Organisasi</h6>
                    <?php tampilkanTabel($conn, $id_logon, 'organisasi', $role_logon); ?>
                </div>

                <div class="tab-pane fade" id="pills-workshop">
                    <h6 class="fw-bold mb-3 text-dark">Daftar Riwayat Workshop/Seminar</h6>
                    <?php tampilkanTabel($conn, $id_logon, 'workshop', $role_logon); ?>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
// FUNGSI UTAMA UNTUK MENAMPILKAN TABEL MULTI-ROLE
function tampilkanTabel($conn, $id_user, $kat, $role) {
    echo '<div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr class="small text-muted text-uppercase">';
                    if ($role == 'Admin') {
                        echo '<th width="20%" class="px-3">Nama Mahasiswa</th>';
                    }
    echo           '<th width="12%" class="'.($role != 'Admin' ? 'px-3' : '').'">Waktu</th>
                    <th width="28%">Nama Kegiatan/Organisasi</th>
                    <th width="20%">'.($kat == 'organisasi' ? 'Jabatan' : 'Penyelenggara/Status').'</th>
                    <th width="10%" class="text-center">Level</th>
                    <th width="10%" class="text-center">Status</th>';
                    if ($role == 'Admin') {
                        echo '<th width="10%" class="text-center">Aksi</th>';
                    }
    echo           '</tr>
                </thead>
                <tbody>';
    
    // Logika pemisahan query data berdasarkan hak akses
    if ($role == 'Admin') {
        $sql = "SELECT keaktifan_kampus.*, users.nama FROM keaktifan_kampus 
                JOIN users ON keaktifan_kampus.id_user = users.id 
                WHERE keaktifan_kampus.kategori = '$kat' 
                ORDER BY keaktifan_kampus.id DESC";
    } else {
        $sql = "SELECT keaktifan_kampus.*, users.nama FROM keaktifan_kampus 
                JOIN users ON keaktifan_kampus.id_user = users.id 
                WHERE keaktifan_kampus.id_user = '$id_user' AND keaktifan_kampus.kategori = '$kat' 
                ORDER BY keaktifan_kampus.id DESC";
    }
    
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
                if ($role == 'Admin') {
                    echo '<td class="px-3 fw-semibold text-secondary">'.htmlspecialchars($row['nama']).'</td>';
                }
            echo '<td class="small text-muted '.($role != 'Admin' ? 'px-3' : '').'">'.htmlspecialchars($row['tanggal_tahun']).'</td>
                  <td class="fw-bold text-dark">'.htmlspecialchars($row['nama_kegiatan']).'</td>
                  <td>'.htmlspecialchars($row['penyelenggara_jabatan']).'</td>
                  <td class="text-center"><span class="badge bg-light text-primary border border-primary-subtle rounded-3">'.htmlspecialchars($row['level']).'</span></td>
                  <td class="text-center">';
                    if ($row['status'] == 'Lulus') {
                        echo '<span class="badge-status status-lulus"><i class="fas fa-check-circle me-1"></i> Valid</span>';
                    } else {
                        echo '<span class="badge-status status-pending"><i class="fas fa-clock me-1"></i> Pending</span>';
                    }
            echo '</td>';
                if ($role == 'Admin') {
                    echo '<td class="text-center">';
                    if ($row['status'] == 'Belum Lulus') {
                        echo '<a href="keaktifan.php?action=valid&id='.$row['id'].'" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 fw-bold text-white fs-7" onclick="return confirm(\'Validkan data portofolio ini?\')"><i class="fas fa-check"></i> Validkan</a>';
                    } else {
                        echo '<a href="keaktifan.php?action=cancel&id='.$row['id'].'" class="btn btn-sm btn-danger rounded-pill px-2.5 py-1 fw-bold text-white fs-7" onclick="return confirm(\'Batalkan verifikasi data ini?\')"><i class="fas fa-times"></i> Batalkan</a>';
                    }
                    echo '</td>';
                }
            echo '</tr>';
        }
    } else {
        $colspan = ($role == 'Admin') ? 7 : 5;
        echo '<tr><td colspan="'.$colspan.'" class="text-center text-muted py-4 small">Belum ada data di kategori ini.</td></tr>';
    }
    
    echo '</tbody></table></div>';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>