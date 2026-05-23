<?php
session_start();
session_destroy(); // Menghapus semua data session
header("Location: login.php"); // Balik ke halaman login
exit;
?>