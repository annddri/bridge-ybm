<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] == 'awardee') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id_awardee'])) {
    header("Location: data_awardee.php");
    exit;
}

$id_awardee = mysqli_real_escape_string($conn, $_GET['id_awardee']);
$id_user = $_SESSION['id_user'];

$query_user = mysqli_query($conn, "
SELECT *
FROM users
WHERE id = '$id_user'
");

$u = mysqli_fetch_assoc($query_user);

$foto_path = "../assets/img/" . (!empty($u['foto_profil']) ? $u['foto_profil'] : 'default.png');

$query_awardee = mysqli_query($conn, "
SELECT *
FROM users
WHERE id = '$id_awardee'
AND role = 'awardee'
");

$data_awardee = mysqli_fetch_assoc($query_awardee);

if (!$data_awardee) {
    echo "
    <script>
        alert('Awardee tidak ditemukan!');
        window.location='data_awardee.php';
    </script>
    ";
    exit;
}

/* =========================
   HITUNG IPK
========================= */

$query_ipk = mysqli_query($conn, "
SELECT AVG(ip) as ipk_total
FROM akademik
WHERE id_user = '$id_awardee'
");

$data_ipk = mysqli_fetch_assoc($query_ipk);

$ipk = $data_ipk['ipk_total'] ? number_format($data_ipk['ipk_total'], 2) : '0.00';

/* =========================
   TOEFL TERTINGGI
========================= */

$query_toefl = mysqli_query($conn, "
SELECT MAX(score) as skor_toefl
FROM toefl
WHERE id_user = '$id_awardee'
");

$data_toefl = mysqli_fetch_assoc($query_toefl);

$toefl = $data_toefl['skor_toefl'] ?? '-';

/* =========================
   TOTAL SEMESTER
========================= */

$query_semester = mysqli_query($conn, "
SELECT COUNT(*) as total_semester
FROM akademik
WHERE id_user = '$id_awardee'
");

$data_semester = mysqli_fetch_assoc($query_semester);

$total_semester = $data_semester['total_semester'];

/* =========================
   STATUS AKADEMIK
========================= */

$status_akademik = "Baik";

if ($ipk >= 3.75) {
    $status_akademik = "Sangat Baik";
} elseif ($ipk < 3.00) {
    $status_akademik = "Perlu Perhatian";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
Monitoring Akademik
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

:root{
    --sidebar-bg:#063255;
    --sidebar-bg-2:#041f35;
    --sidebar-text:rgba(255,255,255,0.82);
    --sidebar-hover:rgba(255,255,255,0.08);
    --accent:#0d6efd;
    --bg:#f4f7fb;
    --border:#e2e8f0;
    --text:#0f172a;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
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

.sidebar-brand{
    text-align:center;
    padding:25px 20px;
    border-bottom:1px solid rgba(255,255,255,0.08);
}

.brand-logo{
    width:75px;
    height:75px;
    border-radius:50%;
    border:3px solid rgba(255,255,255,0.2);
    margin-bottom:12px;
    object-fit:cover;
}

.nav-link{
    color:var(--sidebar-text);
    padding:11px 25px;
    display:flex;
    align-items:center;
    transition:0.2s;
    font-size:0.92rem;
    text-decoration:none;
    border-left:4px solid transparent;
}

.nav-link i{
    width:24px;
    margin-right:12px;
}

.nav-link:hover{
    color:#fff;
    background:var(--sidebar-hover);
    padding-left:28px;
}

.nav-link.active{
    color:#fff;
    background:rgba(13,110,253,0.15);
    border-left:4px solid var(--accent);
    font-weight:600;
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

/* HERO */

.hero-card{
    background:linear-gradient(135deg,#062b49 0%, #0f4c81 100%);
    border-radius:26px;
    padding:35px;
    color:white;
    margin-bottom:30px;
    position:relative;
    overflow:hidden;
}

.hero-card::before{
    content:'';
    position:absolute;
    width:320px;
    height:320px;
    border-radius:50%;
    background:rgba(255,255,255,0.05);
    top:-130px;
    right:-120px;
}

.awardee-box{
    display:flex;
    align-items:center;
    gap:22px;
    position:relative;
    z-index:2;
}

.awardee-photo{
    width:90px;
    height:90px;
    border-radius:22px;
    object-fit:cover;
    border:4px solid rgba(255,255,255,0.15);
}

.awardee-name{
    font-size:1.8rem;
    font-weight:800;
    margin-bottom:5px;
}

.awardee-campus{
    opacity:0.9;
}

/* CARD */

.card-custom{
    background:white;
    border-radius:22px;
    border:1px solid var(--border);
    padding:28px;
    box-shadow:0 8px 25px rgba(15,23,42,0.04);
    margin-bottom:28px;
}

/* STATS */

.stat-card{
    background:white;
    border-radius:22px;
    border:1px solid var(--border);
    padding:25px;
    box-shadow:0 8px 25px rgba(15,23,42,0.04);
    height:100%;
}

.stat-icon{
    width:55px;
    height:55px;
    border-radius:16px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.3rem;
    margin-bottom:18px;
}

.bg-soft-primary{
    background:rgba(13,110,253,0.12);
    color:#0d6efd;
}

.bg-soft-success{
    background:rgba(25,135,84,0.12);
    color:#198754;
}

.bg-soft-warning{
    background:rgba(255,193,7,0.15);
    color:#b78103;
}

.bg-soft-info{
    background:rgba(13,202,240,0.15);
    color:#0891b2;
}

.stat-card h3{
    font-size:1.8rem;
    font-weight:800;
    margin-bottom:4px;
}

.stat-card p{
    margin:0;
    color:#64748b;
    font-size:0.9rem;
}

/* TABLE */

.table-custom{
    width:100%;
    border-collapse:collapse;
}

.table-custom thead th{
    background:#062b49;
    color:white;
    padding:14px;
    font-size:0.86rem;
    text-align:center;
}

.table-custom tbody td{
    padding:14px;
    border-bottom:1px solid #f1f5f9;
    font-size:0.9rem;
    vertical-align:middle;
    text-align:center;
}

.table-custom tbody tr:hover{
    background:#f8fafc;
}

.badge-status{
    padding:6px 14px;
    border-radius:999px;
    font-size:0.75rem;
    font-weight:700;
}

.status-valid{
    background:#dcfce7;
    color:#166534;
}

.status-pending{
    background:#fef3c7;
    color:#92400e;
}

.btn-action{
    padding:6px 14px;
    border-radius:999px;
    font-size:0.75rem;
    font-weight:700;
    text-decoration:none;
}

.section-title{
    font-size:1rem;
    font-weight:800;
    margin-bottom:18px;
}

.btn-kembali{
    background:#111827;
    color:white;
    padding:10px 24px;
    border-radius:999px;
    text-decoration:none;
    font-weight:600;
    font-size:0.85rem;
}

.btn-kembali:hover{
    background:black;
    color:white;
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

    .btn-back{
    background:#0f172a;
    color:#fff;
    padding:10px 22px;
    border-radius:999px;
    text-decoration:none;
    font-size:0.88rem;
    font-weight:600;
    transition:0.2s ease;
    display:inline-flex;
    align-items:center;
    box-shadow:0 6px 14px rgba(15,23,42,0.12);
}

.btn-back:hover{
    background:#020617;
    color:#fff;
    transform:translateY(-1px);
}
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="sidebar-brand">

        <img
        src="<?= $foto_path ?>?t=<?= time() ?>"
        class="brand-logo"
        >

        <h6 class="fw-bold mb-0 text-white">
            <?= htmlspecialchars($u['nama']) ?>
        </h6>

        <small class="text-info text-uppercase fw-bold" style="font-size:0.7rem;letter-spacing:1px;">
            Kepala Asrama
        </small>

    </div>

    <nav class="mt-3">

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

    </nav>

</div>

<!-- MAIN -->

<div class="main-content">

    <div class="d-flex justify-content-end mb-3">

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
    <!-- STATS -->

    <div class="row g-4 mb-4">

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon bg-soft-primary">
                    <i class="fas fa-layer-group"></i>
                </div>

                <h3>
                    <?= $total_semester ?>
                </h3>

                <p>
                    Total Semester
                </p>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon bg-soft-success">
                    <i class="fas fa-graduation-cap"></i>
                </div>

                <h3>
                    <?= $ipk ?>
                </h3>

                <p>
                    IPK Kumulatif
                </p>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon bg-soft-warning">
                    <i class="fas fa-language"></i>
                </div>

                <h3>
                    <?= $toefl ?>
                </h3>

                <p>
                    TOEFL Tertinggi
                </p>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="stat-card">

                <div class="stat-icon bg-soft-info">
                    <i class="fas fa-chart-line"></i>
                </div>

                <h3 style="font-size:1.2rem;">
                    <?= $status_akademik ?>
                </h3>

                <p>
                    Status Akademik
                </p>

            </div>

        </div>

    </div>

    <!-- GRAFIK -->

    <div class="card-custom">

        <div class="section-title">
            Grafik Perkembangan IP
        </div>

        <canvas id="ipChart" height="90"></canvas>

    </div>

    <!-- RIWAYAT IP -->

    <div class="card-custom">

        <div class="section-title">
            Riwayat IP Semester
        </div>

        <div class="table-responsive">

            <table class="table-custom">

                <thead>

                    <tr>
                        <th>Semester</th>
                        <th>IP Semester</th>
                        <th>Bukti KHS</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php

                    $query_akademik = mysqli_query($conn, "
                    SELECT *
                    FROM akademik
                    WHERE id_user = '$id_awardee'
                    ORDER BY semester ASC
                    ");

                    if(mysqli_num_rows($query_akademik) > 0):

                    while($row = mysqli_fetch_assoc($query_akademik)):

                    ?>

                    <tr>

                        <td>
                            Semester <?= $row['semester'] ?>
                        </td>

                        <td class="fw-bold text-primary">
                            <?= number_format($row['ip'],2) ?>
                        </td>

                        <td>

                            <?php if(!empty($row['file_verifikasi'])): ?>

                                <a
                                href="../uploads/<?= $row['file_verifikasi'] ?>"
                                target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                >
                                    <i class="fas fa-eye me-1"></i>
                                    File
                                </a>

                            <?php else: ?>

                                -

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if($row['status'] == 'Lulus'): ?>

                                <span class="badge-status status-valid">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Valid
                                </span>

                            <?php else: ?>

                                <span class="badge-status status-pending">
                                    <i class="fas fa-clock me-1"></i>
                                    Pending
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endwhile; else: ?>

                    <tr>

                        <td colspan="4">

                            Belum ada data akademik

                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- TOEFL -->

    <div class="card-custom">

        <div class="section-title">
            Riwayat TOEFL
        </div>

        <div class="table-responsive">

            <table class="table-custom">

                <thead>

                    <tr>
                        <th>Jenis Tes</th>
                        <th>Score</th>
                        <th>Sertifikat</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    <?php

                    $query_toefl_table = mysqli_query($conn, "
                    SELECT *
                    FROM toefl
                    WHERE id_user = '$id_awardee'
                    ORDER BY id DESC
                    ");

                    if(mysqli_num_rows($query_toefl_table) > 0):

                    while($row = mysqli_fetch_assoc($query_toefl_table)):

                    ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($row['jenis_tes']) ?>
                        </td>

                        <td class="fw-bold text-success">
                            <?= $row['score'] ?>
                        </td>

                        <td>

                            <?php if(!empty($row['file_sertifikat'])): ?>

                                <a
                                href="../uploads/<?= $row['file_sertifikat'] ?>"
                                target="_blank"
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                >
                                    <i class="fas fa-eye me-1"></i>
                                    File
                                </a>

                            <?php else: ?>

                                -

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if($row['status'] == 'Lulus'): ?>

                                <span class="badge-status status-valid">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Valid
                                </span>

                            <?php else: ?>

                                <span class="badge-status status-pending">
                                    <i class="fas fa-clock me-1"></i>
                                    Pending
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                    <?php endwhile; else: ?>

                    <tr>

                        <td colspan="4">

                            Belum ada data TOEFL

                        </td>

                    </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>

const ctx = document.getElementById('ipChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: [

            <?php

            $query_chart = mysqli_query($conn, "
            SELECT *
            FROM akademik
            WHERE id_user = '$id_awardee'
            ORDER BY semester ASC
            ");

            while($c = mysqli_fetch_assoc($query_chart)){
                echo "'Semester ".$c['semester']."',";
            }

            ?>

        ],

        datasets: [{
            label: 'IP Semester',
            data: [

                <?php

                $query_chart2 = mysqli_query($conn, "
                SELECT *
                FROM akademik
                WHERE id_user = '$id_awardee'
                ORDER BY semester ASC
                ");

                while($c2 = mysqli_fetch_assoc($query_chart2)){
                    echo $c2['ip'].",";
                }

                ?>

            ],
            tension:0.4,
            fill:true,
            borderWidth:3
        }]
    },

    options:{
        responsive:true,
        plugins:{
            legend:{
                display:false
            }
        }
    }
});

</script>

</body>
</html>