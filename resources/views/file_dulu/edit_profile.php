<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) { 
    header("Location: login.php"); 
    exit; 
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role']; 

// Ambal data lama user
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query);

if (!$u) {
    die("User tidak ditemukan di database.");
}

// Sinkronisasi path foto untuk sidebar dan preview form
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);
    $prodi = mysqli_real_escape_string($conn, $_POST['prodi']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $nibs = mysqli_real_escape_string($conn, $_POST['nibs']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']); // Kolom NIM baru dipisah
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $angkatan = mysqli_real_escape_string($conn, $_POST['angkatan']);

    $foto_name = $u['foto_profil']; 

    // Cek jika ada file yang diupload
    if (!empty($_FILES['foto']['name'])) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $ekstensi = pathinfo($file_name, PATHINFO_EXTENSION);
        
        $new_foto_name = "profil_" . $id_user . "_" . time() . "." . $ekstensi;
        $target_dir = "assets/img/";
        
        if (move_uploaded_file($file_tmp, $target_dir . $new_foto_name)) {
            $foto_name = $new_foto_name;
        }
    }

    // Update ke database
    $sql = "UPDATE users SET 
            nama = '$nama', 
            universitas = '$universitas', 
            prodi = '$prodi', 
            bio = '$bio',
            nibs = '$nibs',
            nim = '$nim',
            no_telp = '$no_telp',
            angkatan = '$angkatan',
            foto_profil = '$foto_name' 
            WHERE id = '$id_user'";
            
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Profil Berhasil Diupdate!'); window.location='profile.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Warna tema berdasarkan role untuk sinkronisasi aksen visual ringkas
$role_colors = [
    'awardee'       => 'primary',
    'kepala asrama' => 'success',
    'fasilitator'   => 'info',
    'supervisor'    => 'warning',
    'ho'            => 'danger'
];
$theme = $role_colors[$u['role']] ?? 'secondary';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - <?= htmlspecialchars($u['nama']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --navy-theme: #063255; 
            --accent-color: #0284c7;
            --bg-light: #f8fafc;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Navigasi Sidebar Terintegrasi */
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

        /* Pembungkus Konten Utama */
        .main-content { margin-left: 280px; padding: 0 0 35px 0; transition: all 0.3s ease; }
        
        /* Desain Header Profil Melebar Sambung */
        .header-gradient {
            background: linear-gradient(135deg, #063255, #0b426e);
            height: 200px;
            border-radius: 0 0 30px 30px;
        }
        .profile-wrapper { padding: 0 30px; }
        .profile-container { margin-top: -100px; position: relative; z-index: 10; }

        /* Card Form Sesuai Gradasi Abu Premium */
        .card-profile-edit { 
            border: 1px solid rgba(255, 255, 255, 0.7); 
            border-radius: 24px; 
            background: linear-gradient(135deg, #ffffff 0%, #f4f6f9 100%); 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }

        .form-label { font-size: 0.78rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 10px 14px; font-size: 0.92rem; color: #1e293b; }
        .form-control:focus { border-color: var(--accent-color); box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1); }

        /* Penyesuaian Responsif HP */
        @media (max-width: 991.98px) {
            .sidebar { margin-left: -280px; position: fixed; }
            .main-content { margin-left: 0; }
            .profile-wrapper { padding: 0 15px; }
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
        <a href="index.php" class="nav-link-side">
            <i class="fas fa-home"></i> Home
        </a>
        <a href="profile.php" class="nav-link-side active">
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
        <a href="perizinan.php" class="nav-link-side">
            <i class="fas fa-envelope-open-text"></i> Perizinan Asrama
        </a>
        
        <a href="logout.php" class="nav-link-side logout-link" onclick="return confirm('Yakin ingin keluar?')">
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
                    <div class="card card-profile-edit p-4 p-md-5 shadow-sm">
                        
                        <h4 class="fw-bold mb-4 text-dark"><i class="fas fa-user-edit me-2 text-<?= $theme ?>"></i>Edit Profil Anda</h4>
                        
                        <form action="" method="POST" enctype="multipart/form-data">
                            
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4 mb-4 p-4 bg-white bg-opacity-40 rounded-4 border border-white">
                                <img src="<?= $foto_path ?>?t=<?= time() ?>" class="rounded-circle border border-4 border-white shadow" width="110" height="110" style="object-fit: cover;">
                                <div class="text-center text-md-start">
                                    <label class="form-label mb-1">Ganti Foto Profil</label>
                                    <input type="file" name="foto" class="form-control form-control-sm bg-white shadow-sm" style="max-width: 320px;">
                                    <div class="form-text small mt-1">Format: JPG, JPEG, atau PNG. Maksimal ukuran file 2MB.</div>
                                </div>
                            </div>

                            <hr class="opacity-25 my-4">

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control shadow-sm" value="<?= htmlspecialchars($u['nama']) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIBS (Nomor Induk Beasiswa)</label>
                                    <input type="text" name="nibs" class="form-control shadow-sm" value="<?= htmlspecialchars($u['nibs'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                                    <input type="text" name="nim" class="form-control shadow-sm" value="<?= htmlspecialchars($u['nim'] ?? '') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Universitas</label>
                                    <input type="text" name="universitas" class="form-control shadow-sm" value="<?= htmlspecialchars($u['universitas'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Program Studi</label>
                                    <input type="text" name="prodi" class="form-control shadow-sm" value="<?= htmlspecialchars($u['prodi'] ?? '') ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Angkatan Beasiswa</label>
                                    <input type="text" name="angkatan" class="form-control shadow-sm" placeholder="Contoh: Angkatan 9" value="<?= htmlspecialchars($u['angkatan'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon (WhatsApp)</label>
                                    <input type="text" name="no_telp" class="form-control shadow-sm" placeholder="Contoh: 08123456789" value="<?= htmlspecialchars($u['no_telp'] ?? '') ?>">
                                </div>

                                <div class="col-12 mb-4">
                                    <label class="form-label">Bio Singkat</label>
                                    <textarea name="bio" class="form-control shadow-sm" rows="4" placeholder="Tulis bio singkat atau motto hidup kamu di sini..."><?= htmlspecialchars($u['bio'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-end">
                                <a href="profile.php" class="btn btn-light px-4 fw-semibold border rounded-pill shadow-sm">Batal</a>
                                <button type="submit" name="update" class="btn btn-<?= $theme ?> px-4 fw-bold rounded-pill text-white shadow-sm">Simpan Perubahan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>