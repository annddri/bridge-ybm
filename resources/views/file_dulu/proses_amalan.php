<?php
session_start();
include 'config/koneksi.php';

// Pastikan hanya menerima request POST dan user sudah login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['id_user'])) {
    
    $id_user = $_SESSION['id_user'];
    
    // Sanitasi data input dasar
    $tgl_input = mysqli_real_escape_string($conn, $_POST['tanggal']); // Format: Y-m-d
    $kolom     = $_POST['kolom'];
    $nilai     = (int)$_POST['nilai'];

    // 1. DAFTAR KOLOM YANG DIPERBOLEHKAN (White-list Security)
    // Ini mencegah SQL Injection via nama kolom dan menjaga integritas tabel
    $kolom_diizinkan = [
        'shalat_5_waktu', 
        'shalat_malam', 
        'dzikir_pagi', 
        'mendoakan_orang', 
        'shalat_dhuha', 
        'membaca_alquran', 
        'shaum_sunnah', 
        'berinfak'
    ];

    if (!in_array($kolom, $kolom_diizinkan)) {
        echo json_encode(['status' => 'error', 'msg' => 'Kolom tidak valid atau tidak diizinkan!']);
        exit;
    }

    // 2. VALIDASI RANGE NILAI
    if ($kolom === 'shalat_5_waktu') {
        // Shalat maksimal 5 waktu, minimal 0
        if ($nilai < 0) $nilai = 0;
        if ($nilai > 5) $nilai = 5;
    } else {
        // Amalan biasa (checkbox tunggal) hanya boleh bernilai 0 atau 1
        if ($nilai < 0) $nilai = 0;
        if ($nilai > 1) $nilai = 1;
    }

    // 3. PROSES SIMPAN / UPDATE KE DATABASE
    // Cek apakah sudah ada data di tanggal tersebut untuk user ini
    $cek = mysqli_query($conn, "SELECT id FROM amalan_yaumiyah WHERE id_user='$id_user' AND tanggal='$tgl_input'");
    
    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, lakukan UPDATE
        $query = "UPDATE amalan_yaumiyah SET $kolom = $nilai WHERE id_user='$id_user' AND tanggal='$tgl_input'";
    } else {
        // Jika belum ada, lakukan INSERT
        $query = "INSERT INTO amalan_yaumiyah (id_user, tanggal, $kolom) VALUES ('$id_user', '$tgl_input', $nilai)";
    }

    // Eksekusi query dan kembalikan response JSON
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success', 'nilai_tersimpan' => $nilai]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal menyimpan ke database: ' . mysqli_error($conn)]);
    }

} else {
    // Jika diakses langsung tanpa POST atau belum login
    echo json_encode(['status' => 'error', 'msg' => 'Akses ditolak atau sesi telah berakhir.']);
}