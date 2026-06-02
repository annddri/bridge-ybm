<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] !== 'kepala_asrama') { header("Location: ../login.php"); exit; }

if (isset($_POST['simpan'])) {
    $tgl = $_POST['tanggal'];
    $kat = $_POST['kategori'];
    $jns = $_POST['jenis'];
    $nom = $_POST['nominal'];
    $ket = $_POST['keterangan'];
    $usr = $_SESSION['nama'];

    mysqli_query($conn, "INSERT INTO dana_operasional (tanggal, kategori, jenis_transaksi, nominal, keterangan, updated_by) VALUES ('$tgl', '$kat', '$jns', '$nom', '$ket', '$usr')");
    header("Location: ../keuangan.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Tambah Transaksi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm col-md-6">
        <h4 class="mb-4">Tambah Dana Operasional</h4>
        <form method="POST">
            <input type="date" name="tanggal" class="form-control mb-2" required>
            <input type="text" name="kategori" placeholder="Kategori/Alokasi" class="form-control mb-2" required>
            <select name="jenis" class="form-select mb-2"><option value="Masuk">Masuk</option><option value="Keluar">Keluar</option></select>
            <input type="number" name="nominal" placeholder="Nominal" class="form-control mb-2" required>
            <textarea name="keterangan" placeholder="Keterangan" class="form-control mb-3"></textarea>
            <button type="submit" name="simpan" class="btn btn-primary w-100">Simpan Data</button>
        </form>
    </div>
</body>
</html>