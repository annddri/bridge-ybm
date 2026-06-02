```php
<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] == 'awardee') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query_user = mysqli_query($conn, "
SELECT *
FROM users
WHERE id = '$id_user'
");

$u = mysqli_fetch_assoc($query_user);

$foto_path = "../assets/img/" . (!empty($u['foto_profil']) ? $u['foto_profil'] : 'default.png');

/* =========================
   UPDATE VALIDASI
========================= */

if(isset($_POST['update_validasi'])){

    $id_tahfidz = mysqli_real_escape_string($conn,$_POST['id_tahfidz']);
    $validasi = mysqli_real_escape_string($conn,$_POST['validasi']);

    mysqli_query($conn,"
    UPDATE tahfidz
    SET status='$validasi'
    WHERE id='$id_tahfidz'
    ");
}

/* =========================
   GET DATA AWARDEE
========================= */

if (!isset($_GET['id_awardee'])) {
    header("Location: data_awardee.php");
    exit;
}

$id_awardee = mysqli_real_escape_string($conn,$_GET['id_awardee']);

$query_awardee = mysqli_query($conn,"
SELECT *
FROM users
WHERE id = '$id_awardee'
AND role='awardee'
");

$data_awardee = mysqli_fetch_assoc($query_awardee);

if(!$data_awardee){
    header("Location:data_awardee.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
Monitoring Tahfidz
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

:root{
    --sidebar-bg:#063255;
    --sidebar-bg2:#041f35;
    --sidebar-text:rgba(255,255,255,0.85);
    --sidebar-hover:rgba(255,255,255,0.08);
    --accent:#0d6efd;
    --bg:#f4f7fb;
    --border:#e7edf4;
    --text:#0f172a;
}

body{
    background:var(--bg);
    font-family:'Segoe UI',sans-serif;
    color:var(--text);
}

/* SIDEBAR */

.sidebar{
    width:280px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#063255 0%,#041f35 100%);
    color:var(--sidebar-text);
    z-index:1000;
    border-right:1px solid rgba(255,255,255,0.05);
}

.sidebar-header{
    text-align:center;
    padding:28px 20px;
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.sidebar-profile{
    width:78px;
    height:78px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid rgba(255,255,255,0.15);
    margin-bottom:14px;
}

.sidebar-name{
    color:#fff;
    font-weight:700;
    font-size:1rem;
}

.sidebar-role{
    color:#38bdf8;
    font-size:0.72rem;
    text-transform:uppercase;
    letter-spacing:1px;
    font-weight:700;
}

.sidebar-menu{
    padding:14px 0;
}

.nav-link{
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px 25px;
    color:var(--sidebar-text);
    text-decoration:none;
    border-left:4px solid transparent;
    transition:0.2s;
    font-size:0.92rem;
    font-weight:600;
}

.nav-link i{
    width:20px;
    text-align:center;
}

.nav-link:hover{
    background:var(--sidebar-hover);
    color:#fff;
    padding-left:30px;
}

.nav-link.active{
    background:rgba(13,110,253,0.15);
    border-left:4px solid var(--accent);
    color:#fff;
}

.logout-link{
    color:#ff6b6b !important;
    margin-top:20px;
}

/* MAIN */

.main-content{
    margin-left:280px;
    padding:35px;
}

/* BUTTON BACK */

.top-action{
    display:flex;
    justify-content:flex-end;
    margin-bottom:18px;
}

.btn-kembali{
    background:#111827;
    color:#fff !important;
    text-decoration:none;
    padding:10px 20px;
    border-radius:999px;
    font-size:0.85rem;
    font-weight:600;
    transition:0.2s;
}

.btn-kembali:hover{
    background:#000;
    transform:translateY(-2px);
}

/* HERO */

.hero-card{
    background:linear-gradient(135deg,#062b49 0%,#0f4c81 100%);
    border-radius:28px;
    padding:35px;
    color:white;
    margin-bottom:30px;
    overflow:hidden;
    position:relative;
}

.hero-card::before{
    content:'';
    position:absolute;
    width:300px;
    height:300px;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    right:-100px;
    top:-120px;
}

.awardee-box{
    display:flex;
    align-items:center;
    gap:20px;
    position:relative;
    z-index:2;
}

.awardee-photo{
    width:90px;
    height:90px;
    border-radius:24px;
    object-fit:cover;
    border:4px solid rgba(255,255,255,0.15);
}

.awardee-name{
    font-size:1.8rem;
    font-weight:800;
}

.awardee-campus{
    opacity:0.9;
    margin-top:5px;
}

/* CARD */

.card-custom{
    background:#fff;
    border-radius:24px;
    padding:28px;
    border:1px solid var(--border);
    box-shadow:0 8px 25px rgba(15,23,42,0.04);
}

/* TABLE */

.table-custom{
    width:100%;
    border-collapse:collapse;
}

.table-custom thead th{
    background:#062b49;
    color:#fff;
    padding:15px;
    font-size:0.85rem;
    text-align:center;
}

.table-custom tbody td{
    padding:15px;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    font-size:0.9rem;
}

.table-custom tbody tr:hover{
    background:#f8fafc;
}

.badge-surah{
    background:#eff6ff;
    color:#2563eb;
    padding:7px 14px;
    border-radius:999px;
    font-size:0.76rem;
    font-weight:700;
}

/* STATUS SELECT */

.status-select{
    border:none;
    padding:8px 15px;
    border-radius:999px;
    font-size:0.76rem;
    font-weight:700;
    outline:none;
    cursor:pointer;
}

.status-tuntas{
    background:#dcfce7;
    color:#166534;
}

.status-belum{
    background:#fef3c7;
    color:#92400e;
}

.status-tidak{
    background:#fee2e2;
    color:#991b1b;
}

.empty-state{
    text-align:center;
    padding:40px 20px;
    color:#94a3b8;
}

/* RESPONSIVE */

@media(max-width:992px){

    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main-content{
        margin-left:0;
        padding:20px;
    }

    .awardee-box{
        flex-direction:column;
        align-items:flex-start;
    }

    .table-responsive{
        overflow-x:auto;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="sidebar-header">

        <img
        src="<?= $foto_path ?>?t=<?= time() ?>"
        class="sidebar-profile"
        >

        <div class="sidebar-name">
            <?= htmlspecialchars($u['nama']) ?>
        </div>

        <div class="sidebar-role">
            Kepala Asrama
        </div>

    </div>

    <div class="sidebar-menu">

        <a href="dashboard_kepas.php" class="nav-link">
            <i class="fas fa-home"></i>
            Home
        </a>

        <a href="profile_kepas.php" class="nav-link">
            <i class="fas fa-user"></i>
            Profil Saya
        </a>

        <a href="data_awardee.php" class="nav-link active">
            <i class="fas fa-users"></i>
            Data Awardee
        </a>

        <a href="monthly_report.php" class="nav-link">
            <i class="fas fa-file-alt"></i>
            Laporan Bulanan
        </a>

        <a href="daily_report.php" class="nav-link">
            <i class="fas fa-calendar-day"></i>
            Laporan Harian
        </a>

        <a href="../inventaris.php" class="nav-link">
            <i class="fas fa-boxes-stacked"></i>
            Inventaris
        </a>

        <a href="../keuangan.php" class="nav-link">
            <i class="fas fa-wallet"></i>
            Keuangan
        </a>

        <a href="../logout.php" class="nav-link logout-link">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>

    </div>

</div>

<!-- MAIN -->

<div class="main-content">

    <!-- BUTTON -->

    <div class="top-action">

        <a href="data_awardee.php" class="btn-kembali">
            <i class="fas fa-arrow-left me-2"></i>
            Kembali
        </a>

    </div>

    <!-- HERO -->

    <div class="hero-card">

        <div class="awardee-box">

            <img
            src="../assets/img/<?= !empty($data_awardee['foto_profil']) ? $data_awardee['foto_profil'] : 'default.png' ?>"
            class="awardee-photo"
            >

            <div>

                <div class="awardee-name">
                    <?= htmlspecialchars($data_awardee['nama']) ?>
                </div>

                <div class="awardee-campus">
                    <?= htmlspecialchars($data_awardee['universitas']) ?>
                </div>

            </div>

        </div>

    </div>

    <!-- TABLE -->

    <div class="card-custom">

        <div class="table-responsive">

            <table class="table-custom">

                <thead>

                    <tr>

                        <th>
                            Surah / Materi
                        </th>

                        <th>
                            Tanggal Tes
                        </th>

                        <th>
                            Bukti
                        </th>

                        <th>
                            Validasi Kepala Asrama
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php

                    $query_tahfidz = mysqli_query($conn,"
                    SELECT *
                    FROM tahfidz
                    WHERE id_user='$id_awardee'
                    ORDER BY id DESC
                    ");

                    if(mysqli_num_rows($query_tahfidz) > 0):

                        while($row = mysqli_fetch_assoc($query_tahfidz)):

                    ?>

                    <tr>

                        <td>

                            <span class="badge-surah">
                                <?= htmlspecialchars($row['nama_surah']) ?>
                            </span>

                        </td>

                        <td class="text-center">

                            <?= date('d M Y', strtotime($row['tanggal_tes'])) ?>

                        </td>

                        <td class="text-center">

                            <a
                            href="../uploads/<?= $row['file_verifikasi'] ?>"
                            target="_blank"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            >

                                <i class="fas fa-eye me-1"></i>
                                File

                            </a>

                        </td>

                        <td class="text-center">

                            <form method="POST">

                                <input
                                type="hidden"
                                name="id_tahfidz"
                                value="<?= $row['id'] ?>"
                                >

                                <select
                                name="validasi"
                                class="status-select
                                <?=
                                    ($row['status'] == 'Tuntas') ? 'status-tuntas' :
                                    (($row['status'] == 'Tidak Tuntas') ? 'status-tidak' : 'status-belum')
                                ?>"
                                onchange="this.form.submit()"
                                >

                                    <option
                                    value="Belum Tuntas"
                                    <?= ($row['status'] == 'Belum Tuntas') ? 'selected' : '' ?>
                                    >
                                        Belum Tuntas
                                    </option>

                                    <option
                                    value="Tuntas"
                                    <?= ($row['status'] == 'Tuntas') ? 'selected' : '' ?>
                                    >
                                        Tuntas
                                    </option>

                                    <option
                                    value="Tidak Tuntas"
                                    <?= ($row['status'] == 'Tidak Tuntas') ? 'selected' : '' ?>
                                    >
                                        Tidak Tuntas
                                    </option>

                                </select>

                                <input
                                type="hidden"
                                name="update_validasi"
                                value="1"
                                >

                            </form>

                        </td>

                    </tr>

                    <?php
                        endwhile;

                    else:
                    ?>

                    <tr>

                        <td colspan="4">

                            <div class="empty-state">
                                Belum ada data tahfidz
                            </div>

                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>
```
