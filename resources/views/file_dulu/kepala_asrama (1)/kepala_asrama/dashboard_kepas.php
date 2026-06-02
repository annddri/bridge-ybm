<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'kepala_asrama'){
header("Location: ../login.php");
exit;
}

$id_user=$_SESSION['id_user'];

$query_user=mysqli_query($conn,"SELECT * FROM users WHERE id='$id_user'");
$u=mysqli_fetch_assoc($query_user);

$foto_path="../assets/img/".($u['foto_profil'] ?: 'default.png');

function setActive($page){
return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}

/* TOTAL AWARDEE */

$total_awardee=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM users
WHERE role='awardee'
AND id_asrama='".$u['id_asrama']."'
"))['total'];

/* TOTAL DAILY REPORT */

$total_daily=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM laporan_harian
WHERE id_kepas='$id_user'
"))['total'];

/* TOTAL MONTHLY REPORT */

$total_monthly=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT COUNT(*) as total
FROM laporan_bulanan
WHERE id_kepas='$id_user'
"))['total'];

/* =========================
   LEADERBOARD SPIRITUAL
========================= */

$bulan = date('m');
$tahun = date('Y');

$list_amalan = [
'shalat_5_waktu'  => ['tipe' => 'harian', 'target' => 5],
'shalat_malam'    => ['tipe' => 'bulanan', 'target' => 10],
'dzikir_pagi'     => ['tipe' => 'harian', 'target' => 1],
'mendoakan_orang' => ['tipe' => 'harian', 'target' => 1],
'shalat_dhuha'    => ['tipe' => 'harian', 'target' => 1],
'membaca_alquran' => ['tipe' => 'harian', 'target' => 1],
'shaum_sunnah'    => ['tipe' => 'bulanan', 'target' => 3],
'berinfak'        => ['tipe' => 'harian', 'target' => 1],
];

$rank_list = [];

$query_awardee = mysqli_query($conn,"
SELECT id,nama,universitas,angkatan
FROM users
WHERE role='awardee'
AND id_asrama='".$u['id_asrama']."'
");

while ($aw = mysqli_fetch_assoc($query_awardee)) {

$id_aw = $aw['id'];

$res = mysqli_query($conn,"
SELECT *
FROM amalan_yaumiyah
WHERE id_user='$id_aw'
AND MONTH(tanggal)='$bulan'
AND YEAR(tanggal)='$tahun'
");

$total_input_amalan = [];
$hari_aktif_amalan = [];

foreach($list_amalan as $key => $v){
$total_input_amalan[$key] = 0;
$hari_aktif_amalan[$key] = 0;
}

while($row = mysqli_fetch_assoc($res)){

foreach($list_amalan as $key => $attr){

if(isset($row[$key]) && $row[$key] !== ''){

$total_input_amalan[$key] += (int)$row[$key];
$hari_aktif_amalan[$key]++;

}

}

}

$sum_pct = 0;

foreach($list_amalan as $key => $attr){

if($hari_aktif_amalan[$key] > 0){

$pct = ($attr['tipe'] == 'harian')

? ($total_input_amalan[$key] / $hari_aktif_amalan[$key]) / $attr['target'] * 100

: ($total_input_amalan[$key] / $attr['target']) * 100;

$sum_pct += min($pct,100);

}

}

$skor_akhir = ($sum_pct > 0) ? ($sum_pct / 8) : 0;

$rank_list[] = [
'id' => $aw['id'],
'nama' => $aw['nama'],
'universitas' => $aw['universitas'],
'angkatan' => $aw['angkatan'],
'skor' => round($skor_akhir,1)
];

}

usort($rank_list,function($a,$b){
return $b['skor'] <=> $a['skor'];
});

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Kepala Asrama</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

:root{
--sidebar-bg:#063255;
--sidebar-text:rgba(255,255,255,0.85);
--sidebar-hover:rgba(255,255,255,0.08);
--accent-color:#0d6efd;
--bg-light:#f4f7fb;
--card-border:#e7edf4;
--text-dark:#1e293b;
}

body{
background:var(--bg-light);
font-family:'Segoe UI',sans-serif;
color:var(--text-dark);
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
padding-top:10px;
z-index:1000;
overflow-y:auto;
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
transition:all 0.2s ease;
font-size:0.92rem;
text-decoration:none;
border-left:4px solid transparent;
}

.nav-link i{
width:24px;
margin-right:12px;
font-size:1.05rem;
opacity:0.8;
}

.nav-link:hover{
color:#fff;
background-color:var(--sidebar-hover);
padding-left:28px;
}

.nav-link.active{
color:#fff;
background-color:rgba(13,110,253,0.15);
border-left:4px solid var(--accent-color);
font-weight:600;
}

.logout-link{
color:#ff5d73 !important;
margin-top:25px;
}

/* MAIN CONTENT */

.main-content{
margin-left:280px;
padding:35px;
}

/* WELCOME */

.welcome-card{
background:linear-gradient(135deg,#063255,#0b4b7a);
border-radius:28px;
padding:38px;
color:white;
margin-bottom:30px;
position:relative;
overflow:hidden;
}

.welcome-card::before{
content:'';
position:absolute;
width:320px;
height:320px;
border-radius:50%;
background:rgba(255,255,255,0.05);
right:-100px;
top:-140px;
}

.welcome-card h2{
font-weight:800;
margin-bottom:10px;
position:relative;
z-index:2;
}

.welcome-card p{
opacity:0.9;
margin:0;
font-size:0.95rem;
position:relative;
z-index:2;
}

/* TITLE */

.page-title{
font-size:1.8rem;
font-weight:700;
margin-bottom:8px;
color:#0f172a;
}

.page-subtitle{
color:#64748b;
margin-bottom:30px;
font-size:0.95rem;
}

/* CARD */

.dashboard-card{
background:white;
border-radius:22px;
border:1px solid var(--card-border);
padding:25px;
box-shadow:0 8px 24px rgba(15,23,42,0.04);
transition:0.2s;
height:100%;
}

.dashboard-card:hover{
transform:translateY(-3px);
}

.card-icon{
width:58px;
height:58px;
border-radius:16px;
display:flex;
align-items:center;
justify-content:center;
font-size:1.4rem;
margin-bottom:18px;
}

.bg-primary-soft{
background:rgba(13,110,253,0.12);
color:#0d6efd;
}

.bg-success-soft{
background:rgba(25,135,84,0.12);
color:#198754;
}

.bg-warning-soft{
background:rgba(255,193,7,0.15);
color:#b78103;
}

.dashboard-card h3{
font-size:2rem;
font-weight:800;
margin-bottom:5px;
}

.dashboard-card p{
margin:0;
color:#64748b;
font-size:0.92rem;
}

/* LEADERBOARD */

.leaderboard-card{
background:white;
border-radius:24px;
padding:28px;
border:1px solid var(--card-border);
box-shadow:0 8px 24px rgba(15,23,42,0.04);
margin-top:35px;
}

.rank-item{
display:flex;
align-items:center;
justify-content:space-between;
padding:18px 0;
border-bottom:1px solid #eef2f7;
}

.rank-item:last-child{
border-bottom:none;
padding-bottom:0;
}

.rank-left{
display:flex;
align-items:center;
gap:16px;
}

.rank-number{
width:42px;
height:42px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-weight:800;
font-size:0.95rem;
}

.rank-1{
background:#ffd700;
color:#000;
}

.rank-2{
background:#c0c0c0;
color:#000;
}

.rank-3{
background:#cd7f32;
color:#fff;
}

.rank-default{
background:#eff6ff;
color:#2563eb;
}

.rank-name{
font-weight:700;
font-size:0.95rem;
margin-bottom:2px;
}

.rank-campus{
font-size:0.82rem;
color:#64748b;
}

.rank-score{
text-align:right;
}

.rank-score h6{
margin:0;
font-weight:800;
color:#198754;
font-size:1rem;
}

.progress{
height:6px;
border-radius:999px;
overflow:hidden;
}

/* ACTIVITY */

.activity-card{
background:white;
border-radius:24px;
border:1px solid var(--card-border);
padding:28px;
box-shadow:0 8px 24px rgba(15,23,42,0.04);
margin-top:35px;
}

.activity-item{
display:flex;
align-items:flex-start;
padding:16px 0;
border-bottom:1px solid #eef2f7;
}

.activity-item:last-child{
border-bottom:none;
padding-bottom:0;
}

.activity-icon{
width:44px;
height:44px;
border-radius:14px;
display:flex;
align-items:center;
justify-content:center;
margin-right:16px;
font-size:1rem;
background:rgba(13,110,253,0.10);
color:#0d6efd;
}

.activity-text h6{
margin-bottom:5px;
font-weight:700;
font-size:0.95rem;
}

.activity-text small{
color:#64748b;
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

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar shadow">
<<<
<div class="sidebar-brand">

<img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">

<h6 class="fw-bold mb-0 text-white">
<?= htmlspecialchars($u['nama'] ?? 'User') ?>
</h6>

<small class="text-info text-uppercase fw-bold" style="font-size:0.7rem;letter-spacing:1px;">
Kepala Asrama
</small>

</div>

<nav class="mt-3">

<a href="dashboard_kepas.php" class="nav-link active">
<i class="fas fa-home"></i>
Home
</a>

<a href="profile_kepas.php" class="nav-link">
<i class="fas fa-user"></i>
Profil Saya
</a>

<a href="data_awardee.php" class="nav-link">
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

<!-- MAIN CONTENT -->

<div class="main-content">

<!-- WELCOME -->

<div class="welcome-card">

<h2>
Selamat Datang, <?= htmlspecialchars($u['nama']) ?> 👋
</h2>

<p>
Kelola aktivitas asrama, laporan harian, laporan bulanan, dan monitoring perkembangan awardee dengan lebih mudah.
</p>

</div>

<!-- TITLE -->

<h1 class="page-title">
Dashboard Kepala Asrama
</h1>

<p class="page-subtitle">
Overview aktivitas dan statistik asrama Anda.
</p>

<!-- STATS -->

<div class="row g-4">

<div class="col-md-4">

<div class="dashboard-card">

<div class="card-icon bg-primary-soft">
<i class="fas fa-users"></i>
</div>

<h3><?= $total_awardee ?></h3>

<p>Total Awardee</p>

</div>

</div>

<div class="col-md-4">

<div class="dashboard-card">

<div class="card-icon bg-success-soft">
<i class="fas fa-calendar-day"></i>
</div>

<h3><?= $total_daily ?></h3>

<p>Total Laporan Harian</p>

</div>

</div>

<div class="col-md-4">

<div class="dashboard-card">

<div class="card-icon bg-warning-soft">
<i class="fas fa-file-alt"></i>
</div>

<h3><?= $total_monthly ?></h3>

<p>Total Laporan Bulanan</p>

</div>

</div>

</div>

<!-- LEADERBOARD -->

<div class="leaderboard-card">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h5 class="fw-bold mb-1">
<i class="fas fa-trophy text-warning me-2"></i>
Leaderboard Spiritual
</h5>

<small class="text-muted">
Peringkat mutaba'ah amalan awardee bulan ini
</small>

</div>

<a href="monitoring_amalan.php" class="btn btn-primary rounded-pill px-4 fw-semibold">
Lihat Semua
</a>

</div>

<?php
$top_rank = array_slice($rank_list,0,5);

if(count($top_rank) > 0):

$no = 1;

foreach($top_rank as $row):

$rank_class = 'rank-default';

if($no == 1) $rank_class = 'rank-1';
if($no == 2) $rank_class = 'rank-2';
if($no == 3) $rank_class = 'rank-3';
?>

<div class="rank-item">

<div class="rank-left">

<div class="rank-number <?= $rank_class ?>">
<?= $no ?>
</div>

<div>

<div class="rank-name">
<?= htmlspecialchars($row['nama']) ?>
</div>

<div class="rank-campus">
<?= htmlspecialchars($row['universitas'] ?: '-') ?>
</div>

</div>

</div>

<div class="rank-score">

<h6>
<?= $row['skor'] ?>%
</h6>

<div class="progress mt-2" style="width:120px;">
<div class="progress-bar bg-success" style="width:<?= $row['skor'] ?>%"></div>
</div>

</div>

</div>

<?php
$no++;
endforeach;

else:
?>

<div class="text-center text-muted py-4">
Belum ada data spiritual tracker.
</div>

<?php endif; ?>

</div>

<!-- ACTIVITY -->

<div class="activity-card">

<h5 class="fw-bold mb-4">
<i class="fas fa-bolt me-2"></i>
Aktivitas Cepat
</h5>

<div class="activity-item">

<div class="activity-icon">
<i class="fas fa-calendar-day"></i>
</div>

<div class="activity-text">

<h6>Buat Laporan Harian</h6>

<small>
Catat hasil kegiatan dan pertemuan harian awardee.
</small>

</div>

</div>

<div class="activity-item">

<div class="activity-icon">
<i class="fas fa-file-alt"></i>
</div>

<div class="activity-text">

<h6>Buat Laporan Bulanan</h6>

<small>
Input executive summary perkembangan asrama setiap bulan.
</small>

</div>

</div>

<div class="activity-item">

<div class="activity-icon">
<i class="fas fa-users"></i>
</div>

<div class="activity-text">

<h6>Kelola Data Awardee</h6>

<small>
Lihat dan pantau data seluruh awardee asrama.
</small>

</div>

</div>

</div>

</div>

</body>

</html>