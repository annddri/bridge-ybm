<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_barang = intval($_GET['id']);
    
    // Proses hapus dari tabel inventaris
    $delete = mysqli_query($conn, "DELETE FROM inventaris WHERE id_barang = '$id_barang'");
    
    if ($delete) {
        echo "<script>alert('Barang berhasil dihapus dari inventaris!'); window.location='inventaris.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang.'); window.location='inventaris.php';</script>";
    }
} else {
    header("Location: inventaris.php");
    exit;
}
?>