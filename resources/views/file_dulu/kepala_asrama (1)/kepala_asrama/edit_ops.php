<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] !== 'kepala_asrama') { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
if (isset($_POST['update'])) {
    mysqli_query($conn, "UPDATE dana_operasional SET tanggal='{$_POST['tanggal']}', kategori='{$_POST['kategori']}', nominal='{$_POST['nominal']}', keterangan='{$_POST['keterangan']}' WHERE id_ops='$id'");
    header("Location: ../keuangan.php");
}
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM dana_operasional WHERE id_ops='$id'"));
?>
<!DOCTYPE html>
<html>
<head><title>Edit Transaksi</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm col-md-6">
        <h4 class="mb-4">Edit Transaksi</h4>
        <form method="POST">
            <input type="date" name="tanggal" class="form-control mb-2" value="<?=$data['tanggal']?>" required>
            <input type="text" name="kategori" class="form-control mb-2" value="<?=$data['kategori']?>" required>
            <input type="number" name="nominal" class="form-control mb-2" value="<?=$data['nominal']?>" required>
            <textarea name="keterangan" class="form-control mb-3"><?=$data['keterangan']?></textarea>
            <button type="submit" name="update" class="btn btn-warning w-100">Update Data</button>
        </form>
    </div>
</body>
</html>