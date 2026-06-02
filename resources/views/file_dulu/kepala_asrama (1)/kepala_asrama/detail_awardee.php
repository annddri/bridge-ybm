<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role']!=='kepala_asrama'){
header("Location: ../login.php");
exit;
}

$id_kepas=$_SESSION['id_user'];

$u=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM users
WHERE id='$id_kepas'
"));

$foto_path="../assets/img/".($u['foto_profil'] ?: 'default.png');

$id_awardee=$_GET['id'];

$data=mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM users
WHERE id='$id_awardee'
"));

function setActive($page){
return basename($_SERVER['PHP_SELF'])==$page ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Monitoring Awardee</title>

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
--ybm-blue:#2F64BC;
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

/* MAIN */

.main-content{
margin-left:280px;
padding:35px;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:28px;
}

.page-title{
font-size:1.7rem;
font-weight:700;
margin:0;
color:#0f172a;
}

/* CARD */

.monitor-card{
background:#fff;
border-radius:22px;
border:1px solid var(--card-border);
box-shadow:0 8px 25px rgba(15,23,42,0.05);
overflow:hidden;
}

/* HEADER PROFILE */

.awardee-banner{
background:linear-gradient(135deg,#063255 0%,#0b4c7a 100%);
padding:35px;
position:relative;
overflow:hidden;
}

.awardee-banner::before{
content:'';
position:absolute;
width:280px;
height:280px;
border-radius:50%;
background:rgba(255,255,255,0.05);
top:-120px;
right:-100px;
}

.awardee-profile{
display:flex;
align-items:center;
gap:22px;
position:relative;
z-index:2;
}

.awardee-img{
width:95px;
height:95px;
border-radius:22px;
object-fit:cover;
border:4px solid rgba(255,255,255,0.15);
box-shadow:0 8px 18px rgba(0,0,0,0.18);
}

.awardee-name{
font-size:1.55rem;
font-weight:700;
color:#fff;
margin-bottom:4px;
}

.awardee-desc{
color:rgba(255,255,255,0.75);
font-size:0.92rem;
margin-bottom:10px;
}

.badge-info{
display:inline-flex;
align-items:center;
gap:8px;
padding:8px 14px;
background:rgba(255,255,255,0.12);
border-radius:999px;
font-size:0.82rem;
font-weight:600;
color:#fff;
}

/* CONTENT */

.monitor-body{
padding:35px;
}

.section-title{
font-size:1.1rem;
font-weight:700;
margin-bottom:22px;
color:#0f172a;
}

/* MENU GRID */

.menu-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
gap:20px;
}

.menu-item{
background:#fff;
border:1px solid #e8eef5;
border-radius:20px;
padding:28px 22px;
text-align:center;
text-decoration:none;
transition:all 0.25s ease;
position:relative;
overflow:hidden;
}

.menu-item::before{
content:'';
position:absolute;
top:0;
left:0;
width:100%;
height:4px;
background:linear-gradient(90deg,#2F64BC,#39c6f4);
opacity:0;
transition:0.3s;
}

.menu-item:hover{
transform:translateY(-5px);
border-color:#d5e3f1;
box-shadow:0 15px 30px rgba(47,100,188,0.10);
}

.menu-item:hover::before{
opacity:1;
}

.icon-wrap{
width:68px;
height:68px;
margin:0 auto 16px;
border-radius:20px;
background:linear-gradient(135deg,#edf4ff,#f4fbff);
display:flex;
align-items:center;
justify-content:center;
font-size:1.5rem;
color:#2F64BC;
}

.menu-title{
font-size:1rem;
font-weight:700;
color:#1e293b;
margin-bottom:5px;
}

.menu-subtitle{
font-size:0.82rem;
color:#64748b;
}

/* BUTTON */

.btn-back{
border-radius:10px;
padding:10px 18px;
font-size:0.88rem;
font-weight:600;
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

.awardee-profile{
flex-direction:column;
align-items:flex-start;
}

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar shadow">

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

<a href="dashboard_kepas.php" class="nav-link <?= setActive('dashboard_kepas.php') ?>">
<i class="fas fa-home"></i>
Home
</a>

<a href="profile_kepas.php" class="nav-link <?= setActive('profile_kepas.php') ?>">
<i class="fas fa-user"></i>
Profil Saya
</a>

<a href="data_awardee.php" class="nav-link active">
<i class="fas fa-users"></i>
Data Awardee
</a>

<a href="monthly_report.php" class="nav-link <?= setActive('monthly_report.php') ?>">
<i class="fas fa-file-alt"></i>
Laporan Bulanan
</a>

<a href="daily_report.php" class="nav-link <?= setActive('daily_report.php') ?>">
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

<div class="topbar">

<h1 class="page-title">
Monitoring Awardee
</h1>

<a href="data_awardee.php" class="btn btn-outline-secondary btn-back">
<i class="fas fa-arrow-left me-2"></i>
Kembali
</a>

</div>

<div class="monitor-card">

<!-- HEADER -->

<div class="awardee-banner">

<div class="awardee-profile">

<img
src="../assets/img/<?= $data['foto_profil'] ?: 'default.png' ?>"
class="awardee-img"
alt="Awardee"
>

<div>

<div class="awardee-name">
<?= htmlspecialchars($data['nama']) ?>
</div>

<div class="awardee-desc">
<?= htmlspecialchars($data['universitas']) ?> • Angkatan <?= htmlspecialchars($data['angkatan']) ?>
</div>

<div class="badge-info">
<i class="fas fa-user-graduate"></i>
Awardee YBM BRILiaN
</div>

</div>

</div>

</div>

<!-- BODY -->

<div class="monitor-body">

<h5 class="section-title">
<i class="fas fa-layer-group me-2"></i>
Menu Monitoring
</h5>

<div class="menu-grid">

<?php

$menu=[
[
'nama'=>'Spiritual',
'icon'=>'fa-pray',
'desc'=>'Monitoring ibadah dan amalan',
'link'=>'detail_amalan.php'
],
[
'nama'=>'Tahfidz',
'icon'=>'fa-book-quran',
'desc'=>'Monitoring hafalan Al-Qur\'an',
'link'=>'detail_tahfidz.php'
],
[
'nama'=>'Akademik',
'icon'=>'fa-graduation-cap',
'desc'=>'Monitoring perkembangan akademik',
'link'=>'detail_akademik.php'
],
[
'nama'=>'Portofolio',
'icon'=>'fa-award',
'desc'=>'Monitoring pencapaian dan prestasi',
'link'=>'detail_keaktifan.php'
],
[
'nama'=>'Sosial',
'icon'=>'fa-people-group',
'desc'=>'Monitoring kegiatan sosial',
'link'=>'detail_sosial
.php'
]
];

foreach($menu as $m):

?>

<a
href="<?= $m['link'] ?>?id_awardee=<?= $id_awardee ?>"
class="menu-item"
>

<div class="icon-wrap">
<i class="fas <?= $m['icon'] ?>"></i>
</div>

<div class="menu-title">
<?= $m['nama'] ?>
</div>

<div class="menu-subtitle">
<?= $m['desc'] ?>
</div>

</a>

<?php endforeach; ?>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>