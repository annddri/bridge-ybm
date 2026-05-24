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
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// Eksekusi jika form disubmit
if (isset($_POST['submit'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $jumlah = intval($_POST['jumlah']);
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $kondisi = $_POST['kondisi'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $updated_by = $u['nama'];

    $insert = mysqli_query($conn, "INSERT INTO inventaris (nama_barang, kode_barang, jumlah, lokasi, kondisi, keterangan, updated_by) 
                                   VALUES ('$nama_barang', '$kode_barang', '$jumlah', '$lokasi', '$kondisi', '$keterangan', '$updated_by')");

    if ($insert) {
        echo "<script>alert('Barang berhasil ditambahkan!'); window.location='inventaris.php';</script>";
    } else {
        echo "<script>alert('Gagal! Periksa kembali data (Kode barang tidak boleh kembar).');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Inventaris - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0b141d; --sidebar-text: #ffffff; --sidebar-hover: #1a2a3a; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; }
        .brand-logo { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #2e5a88; margin-bottom: 15px; object-fit: cover; }
        .nav-link { color: rgba(255,255,255,0.8); padding: 12px 25px; display: flex; align-items: center; text-decoration: none; }
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
        <a href="inventaris.php" class="nav-link text-white"><i class="fas fa-arrow-left me-3"></i> Kembali</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4">Tambah Barang Inventaris</h3>
        
        <div class="card form-card shadow-sm">
            <form action="" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Inventaris (Harus Unik)</label>
                        <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: BRIGHT-MC-02" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Kipas Angin Cosmos" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jumlah Unit</label>
                        <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Lokasi Penempatan</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Ruang Tamu / Dapur" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Kondisi Awal</label>
                        <select name="kondisi" class="form-select">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan / Catatan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Sumbangan dari siapa atau deskripsi spesifik barang..."></textarea>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" name="submit" class="btn btn-primary px-4 rounded-pill">Simpan Barang</button>
                        <a href="inventaris.php" class="btn btn-light px-4 rounded-pill ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>