<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] !== 'kepala_asrama') { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM dana_operasional WHERE id_ops='$id'");
header("Location: ../keuangan.php");
?>