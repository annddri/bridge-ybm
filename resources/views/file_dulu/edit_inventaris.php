<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user']) || !isset($_GET['id'])) {
    header("Location: inventaris.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

$id_barang = intval($_GET['id']);
$get_barang = mysqli_query($conn, "SELECT * FROM inventaris WHERE id_barang = '$id_barang'");
$b = mysqli_fetch_assoc($get_barang);

if (!$b) { header("Location: inventaris.php"); exit; }

if (isset($_POST['update'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $jumlah = intval($_POST['jumlah']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kondisi = $_POST['kondisi'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $updated_by = $u['nama'];

    $update = mysqli_query($conn, "UPDATE inventaris SET 
        nama_barang='$nama_barang', kode_barang='$kode_barang', jumlah='$jumlah', 
        lokasi='$lokasi', kondisi='$kondisi', keterangan='$keterangan', updated_by='$updated_by' 
        WHERE id_barang='$id_barang'");

    if ($update) {
        echo "<script>alert('Data inventaris berhasil diubah!'); window.location='inventaris.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventaris - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0b141d; --sidebar-text: #ffffff; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; }
        .brand-logo { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #2e5a88; margin-bottom: 15px; object-fit: cover; }
        .main-content { margin-left: 280px; padding: 30px; }
        .form-card { background: white; border-radius: 12px; border: none; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="text-center p-3 border-bottom border-secondary">
        <img src="<?= $foto_path ?>" class="brand-logo shadow">
        <h5 class="fw-bold m-0 text-white"><?= htmlspecialchars($u['nama']) ?></h5>
        <small class="text-info fw-bold"><?= strtoupper($u['role']) ?></small>
    </div>
    <div class="mt-4">
        <a href="inventaris.php" class="nav-link text-white px-4 text-decoration-none"><i class="fas fa-arrow-left me-3"></i> Batal</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4">Edit Data Barang</h3>
        
        <div class="card form-card shadow-sm">
            <form action="" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Inventaris</label>
                        <input type="text" name="kode_barang" class="form-control" value="<?= htmlspecialchars($b['kode_barang']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($b['nama_barang']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jumlah Unit</label>
                        <input type="number" name="jumlah" class="form-control" value="<?= $b['jumlah'] ?>" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Lokasi Penempatan</label>
                        <input type="text" name="lokasi" class="form-control" value="<?= htmlspecialchars($b['lokasi']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kondisi</label>
                        <select name="kondisi" class="form-select">
                            <option value="Baik" <?= $b['kondisi'] == 'Baik' ? 'selected' : '' ?>>Baik</option>
                            <option value="Rusak Ringan" <?= $b['kondisi'] == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                            <option value="Rusak Berat" <?= $b['kondisi'] == 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan / Catatan Perubahan</label>
                        <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($b['keterangan']) ?></textarea>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" name="update" class="btn btn-warning text-white px-4 rounded-pill fw-semibold">Simpan Perubahan</button>
                        <a href="inventaris.php" class="btn btn-light px-4 rounded-pill ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
