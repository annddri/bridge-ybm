<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// Ambil data user untuk sidebar
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'"));
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// Ambil ID Voting dari URL (Jika tidak ada, ambil yang terbaru yang aktif)
$id_v = isset($_GET['id']) ? $_GET['id'] : '';
if ($id_v == '') {
    $q_active = mysqli_query($conn, "SELECT id_voting FROM voting_topik WHERE status = 'Aktif' ORDER BY id_voting DESC LIMIT 1");
    $active = mysqli_fetch_assoc($q_active);
    $id_v = $active['id_voting'] ?? 0;
}

// 1. PROSES SUBMIT VOTE
if (isset($_POST['submit_vote'])) {
    $id_opsi = $_POST['pilihan'];
    
    // Cek apakah sudah pernah vote untuk topik ini
    $cek_vote = mysqli_query($conn, "SELECT * FROM voting_hasil WHERE id_voting = '$id_v' AND id_user = '$id_user'");
    if (mysqli_num_rows($cek_vote) == 0) {
        mysqli_query($conn, "INSERT INTO voting_hasil (id_voting, id_user, id_opsi) VALUES ('$id_v', '$id_user', '$id_opsi')");
        echo "<script>alert('Terima kasih! Suara Anda telah direkam.'); window.location='musyawarah.php?id=$id_v';</script>";
    } else {
        echo "<script>alert('Anda sudah memberikan suara sebelumnya!');</script>";
    }
}

// Ambil data topik voting
$voting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM voting_topik WHERE id_voting = '$id_v'"));

// Cek apakah user login sudah vote
$user_sudah_vote = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM voting_hasil WHERE id_voting = '$id_v' AND id_user = '$id_user'")) > 0;

// Hitung total suara masuk untuk persentase
$total_suara = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM voting_hasil WHERE id_voting = '$id_v'"))['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Musyawarah Digital - Bright Asrama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #0b141d; --sidebar-text: #ffffff; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 280px; height: 100vh; position: fixed; top: 0; left: 0; background-color: var(--sidebar-bg); color: var(--sidebar-text); padding-top: 20px; }
        .main-content { margin-left: 280px; padding: 30px; }
        .vote-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .progress { height: 25px; border-radius: 50px; background-color: #e9ecef; }
        .progress-bar { border-radius: 50px; font-weight: bold; }
        .option-box { border: 2px solid #eee; border-radius: 12px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: 0.3s; }
        .option-box:hover { border-color: #3b82f6; background-color: #f0f7ff; }
        .option-box input:checked + label { color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="text-center p-3 border-bottom border-secondary">
        <img src="<?= $foto_path ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover;">
        <h5 class="fw-bold mt-2 text-white"><?= htmlspecialchars($u['nama']) ?></h5>
        <small class="text-info"><?= strtoupper($u['role']) ?></small>
    </div>
    <div class="mt-4">
        <a href="index.php" class="nav-link text-white px-4 text-decoration-none"><i class="fas fa-arrow-left me-3"></i> Dashboard</a>
    </div>
</div>

<div class="main-content">
    <div class="container">
        <?php if ($voting): ?>
            <div class="text-center mb-5">
                <h2 class="fw-bold">Musyawarah Digital</h2>
                <p class="text-muted">Suara Anda menentukan masa depan asrama!</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card vote-card p-4 p-md-5">
                        <div class="mb-4">
                            <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">Topik Aktif</span>
                            <h3 class="fw-bold"><?= $voting['judul_voting'] ?></h3>
                            <p class="text-muted"><?= $voting['deskripsi'] ?></p>
                        </div>

                        <hr class="mb-4">

                        <?php if (!$user_sudah_vote): ?>
                            <form action="" method="POST">
                                <h6 class="fw-bold mb-3 text-primary">Pilih Opsi:</h6>
                                <?php
                                $opsi = mysqli_query($conn, "SELECT * FROM voting_opsi WHERE id_voting = '$id_v'");
                                while($o = mysqli_fetch_assoc($opsi)):
                                ?>
                                    <div class="option-box">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="pilihan" id="opsi_<?= $o['id_opsi'] ?>" value="<?= $o['id_opsi'] ?>" required>
                                            <label class="form-check-label w-100" for="opsi_<?= $o['id_opsi'] ?>">
                                                <?= $o['nama_opsi'] ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                                <button type="submit" name="submit_vote" class="btn btn-primary w-100 rounded-pill py-3 mt-4 fw-bold">Kirim Suara Saya</button>
                            </form>
                        <?php else: ?>
                            <h6 class="fw-bold mb-4 text-success"><i class="fas fa-check-circle me-2"></i> Hasil Voting Sementara:</h6>
                            <?php
                            $opsi_hasil = mysqli_query($conn, "SELECT * FROM voting_opsi WHERE id_voting = '$id_v'");
                            while($oh = mysqli_fetch_assoc($opsi_hasil)):
                                $id_opsi_h = $oh['id_opsi'];
                                $jml_suara = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as jml FROM voting_hasil WHERE id_opsi = '$id_opsi_h'"))['jml'];
                                $persen = ($total_suara > 0) ? round(($jml_suara / $total_suara) * 100) : 0;
                            ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold"><?= $oh['nama_opsi'] ?></span>
                                        <span class="fw-bold"><?= $persen ?>% (<?= $jml_suara ?> Suara)</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?= $persen ?>%"></div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <div class="alert alert-info rounded-pill text-center py-2 mt-4">
                                <small>Anda sudah berpartisipasi dalam musyawarah ini.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center py-5 rounded-4 shadow-sm">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h4 class="fw-bold">Opps! Topik Musyawarah Tidak Ditemukan.</h4>
                <p>Silakan hubungi Kepala Asrama untuk membuka topik musyawarah baru.</p>
                <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-2">Kembali ke Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>