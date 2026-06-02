```php id="c2n8vx"
<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'kepala_asrama'){
header("Location: ../login.php");
exit;
}

function setActive($page){
return(basename($_SERVER['PHP_SELF'])==$page)?'active':'';
}

$id_user=$_SESSION['id_user'];

$query_user=mysqli_query($conn,"SELECT * FROM users WHERE id='$id_user'");
$u=mysqli_fetch_assoc($query_user);

$foto_path="../assets/img/".($u['foto_profil'] ?: 'default.png');
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Profil Kepala Asrama</title>

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

/* MAIN */

.main-content{
margin-left:280px;
padding:35px;
}

/* HEADER */

.profile-header{
background:linear-gradient(135deg,#063255,#0b4b7a);
border-radius:24px;
padding:45px;
position:relative;
margin-bottom:110px;
min-height:260px;
display:flex;
align-items:flex-start;
justify-content:space-between;
overflow:visible;
}

.profile-header::before{
content:'';
position:absolute;
width:320px;
height:320px;
background:rgba(255,255,255,0.04);
border-radius:50%;
top:-120px;
right:-120px;
}

.profile-left{
position:relative;
z-index:2;
}

.profile-name{
color:white;
font-size:2.4rem;
font-weight:700;
margin-bottom:10px;
}

.profile-role{
color:rgba(255,255,255,0.85);
font-size:1rem;
margin-bottom:20px;
}

.profile-badge{
display:inline-flex;
align-items:center;
padding:10px 16px;
border-radius:999px;
background:rgba(13,110,253,0.18);
color:white;
font-size:0.85rem;
font-weight:600;
}

.profile-avatar{
position:absolute;
bottom:-75px;
left:45px;
width:150px;
height:150px;
border-radius:50%;
object-fit:cover;
border:6px solid white;
background:white;
box-shadow:0 10px 30px rgba(0,0,0,0.18);
z-index:5;
}

/* PROFILE CARD */

.profile-card{
background:white;
border-radius:22px;
border:1px solid var(--card-border);
padding:110px 35px 35px 35px;
box-shadow:0 4px 14px rgba(15,23,42,0.04);
}

.section-title{
font-size:1.1rem;
font-weight:700;
margin-bottom:25px;
color:#0f172a;
}

.info-box{
background:#f8fafc;
border:1px solid #e9eef5;
border-radius:16px;
padding:20px;
height:100%;
transition:0.2s;
}

.info-box:hover{
transform:translateY(-2px);
}

.info-label{
font-size:0.75rem;
font-weight:700;
color:#64748b;
text-transform:uppercase;
letter-spacing:0.6px;
margin-bottom:8px;
}

.info-value{
font-size:1rem;
font-weight:600;
color:#1e293b;
word-break:break-word;
}

/* BUTTON */

.btn-custom-primary{
background:linear-gradient(135deg,#0d6efd,#0b5ed7);
border:none;
border-radius:12px;
padding:11px 22px;
font-weight:600;
color:white;
box-shadow:0 4px 10px rgba(13,110,253,0.18);
transition:0.2s;
}

.btn-custom-primary:hover{
transform:translateY(-1px);
color:white;
}

.btn-custom-outline{
border:1px solid #dbe3ec;
border-radius:12px;
padding:11px 22px;
font-weight:600;
background:white;
color:#1e293b;
transition:0.2s;
}

.btn-custom-outline:hover{
background:#f8fafc;
}

/* RESPONSIVE */
<<<<
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

.profile-header{
padding:30px;
min-height:300px;
}

.profile-avatar{
left:50%;
transform:translateX(-50%);
bottom:-70px;
}

.profile-left{
width:100%;
text-align:center;
}

.profile-card{
padding-top:100px;
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

<a href="data_awardee.php" class="nav-link <?= setActive('data_awardee.php') ?>">
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

<a href="../inventaris.php" class="nav-link <?= setActive('inventaris.php') ?>">
<i class="fas fa-boxes-stacked"></i>
Inventaris
</a>

<a href="../keuangan.php" class="nav-link <?= setActive('keuangan.php') ?>">
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

<div class="profile-header">

<div class="profile-left">

<h1 class="profile-name">
<?= htmlspecialchars($u['nama']) ?>
</h1>

<div class="profile-role">
Kepala Asrama YBM BRILiaN
</div>

<div class="profile-badge">
<i class="fas fa-shield-heart me-2"></i>
Active Account
</div>

</div>

<img src="<?= $foto_path ?>?t=<?= time() ?>" class="profile-avatar">

</div>

<div class="profile-card">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

<div>

<h4 class="section-title mb-1">
Informasi Profil
</h4>

<p class="text-muted mb-0" style="font-size:0.92rem;">
Informasi pribadi dan akademik kepala asrama.
</p>

</div>

<div class="d-flex gap-2">

<button class="btn btn-custom-primary">
<i class="fas fa-pen me-2"></i>
Edit Profil
</button>

<a href="dashboard_kepas.php" class="btn btn-custom-outline">
<i class="fas fa-arrow-left me-2"></i>
Dashboard
</a>

</div>

</div>

<div class="row g-4">

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Nama Lengkap
</div>

<div class="info-value">
<?= htmlspecialchars($u['nama'] ?: '-') ?>
</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Email
</div>

<div class="info-value">
<?= htmlspecialchars($u['email'] ?: '-') ?>
</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Regional Office
</div>

<div class="info-value">
<?= htmlspecialchars($u['ro'] ?: '-') ?>
</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Batch / Angkatan
</div>

<div class="info-value">
<?= htmlspecialchars($u['angkatan'] ?: '-') ?>
</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Universitas
</div>

<div class="info-value">
<?= htmlspecialchars($u['universitas'] ?: '-') ?>
</div>

</div>

</div>

<div class="col-md-6">

<div class="info-box">

<div class="info-label">
Nomor Telepon
</div>

<div class="info-value">
<?= htmlspecialchars($u['no_telp'] ?: '-') ?>
</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>
```
