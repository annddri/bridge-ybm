<?php
session_start();
include 'config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// SISTEM KOMUNAL: Hanya role 'awardee' yang boleh menambah kas asrama
if ($role_user != 'awardee') {
    echo "<script>alert('Akses ditolak! Hanya awardee yang dapat mengelola uang kas.'); window.location='keuangan.php';</script>";
    exit;
}

// Ambil data user untuk sidebar
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// Proses insert data saat form disubmit
if (isset($_POST['submit'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jenis_transaksi = mysqli_real_escape_string($conn, $_POST['jenis_transaksi']);
    $nama_awardee = mysqli_real_escape_string($conn, $_POST['nama_awardee']);
    $nominal = floatval($_POST['nominal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // updated_by otomatis mengambil nama awardee yang sedang login/input
    $updated_by = $u['nama'];

    $insert = mysqli_query($conn, "INSERT INTO kas_asrama (tanggal, jenis_transaksi, nama_awardee, nominal, keterangan, updated_by) 
                                   VALUES ('$tanggal', '$jenis_transaksi', '$nama_awardee', '$nominal', '$keterangan', '$updated_by')");

    if ($insert) {
        echo "<script>alert('Transaksi kas berhasil dicatat!'); window.location='keuangan.php';</script>";
    } else {
        echo "<script>alert('Gagal mencatat transaksi. Periksa kembali inputan Anda.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Kas Asrama - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #0b141d; 
            --sidebar-text: #ffffff;
            --sidebar-hover: #1a2a3a;
        }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; }
        .sidebar-brand { text-align: center; padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand-logo { width: 80px; height: 80px; border-radius: 50%; border: 3px solid #2e5a88; margin-bottom: 15px; object-fit: cover; }
        .nav-link-side { color: rgba(255,255,255,0.8); padding: 12px 25px; display: flex; align-items: center; text-decoration: none; }
        
        .main-content { margin-left: 280px; padding: 30px; }
        .form-card { background: white; border-radius: 12px; border: none; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">
        <h5 class="fw-bold m-0 text-white"><?= htmlspecialchars($u['nama']) ?></h5>
        <small class="text-info fw-bold"><?= strtoupper($u['role']) ?></small>
    </div>
    <div class="mt-4">
        <a href="keuangan.php" class="nav-link-side text-white fw-semibold"><i class="fas fa-arrow-left me-3"></i> Batal / Kembali</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h3 class="fw-bold mb-4">Input Transaksi Kas Asrama</h3>
        
        <div class="card form-card shadow-sm">
            <form action="" method="POST">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-dis') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" onchange="toggleNamaField()" required>
                            <option value="Masuk">Masuk (Iuran / Sumbangan)</option>
                            <option value="Keluar">Keluar (Beli Galon, Gas, dll)</option>
                        </select>
                    </div>

                    <div class="col-md-6" id="field_nama">
                        <label class="form-label fw-semibold">Nama Pembayar / Sumber Dana</label>
                        <input type="text" name="nama_awardee" class="form-control" placeholder="Contoh: Ahmad / Kas Hamba Allah">
                    </div>

                    <div class="col-md-6" id="field_tujuan" style="display: none;">
                        <label class="form-label fw-semibold">Tujuan Alokasi Keluar</label>
                        <input type="text" id="tujuan_input" class="form-control" placeholder="Contoh: Toko Sembako Berkah">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nominal Uang (Rp)</label>
                        <input type="number" name="nominal" class="form-control" placeholder="Contoh: 20000" min="1" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Pembayaran kas bulan Mei 2026 atau Pembelian token listrik dapur" required></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" name="submit" class="btn btn-info text-white px-4 rounded-pill fw-semibold">Simpan Catatan Kas</button>
                        <a href="keuangan.php" class="btn btn-light px-4 rounded-pill ms-2">Batal</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Logic dinamis untuk mengubah label form tergantung Jenis Transaksinya
    function toggleNamaField() {
        var jenis = document.getElementById('jenis_transaksi').value;
        var fieldNama = document.getElementById('field_nama');
        var fieldTujuan = document.getElementById('field_tujuan');
        var tujuanInput = document.getElementById('tujuan_input');

        if (jenis === 'Keluar') {
            fieldNama.style.display = 'none';
            fieldTujuan.style.display = 'block';
            // Pindahkan name attribute ke input tujuan agar terkirim ke database
            tujuanInput.setAttribute('name', 'nama_awardee');
            document.getElementsByName('nama_awardee')[0].removeAttribute('name');
        } else {
            fieldNama.style.display = 'block';
            fieldTujuan.style.display = 'none';
            // Kembalikan name attribute ke input nama asli
            fieldNama.querySelector('input').setAttribute('name', 'nama_awardee');
            tujuanInput.removeAttribute('name');
        }
    }
</script>

</body>
</html>