```php
<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role']!=='kepala_asrama'){
header("Location: ../login.php");
exit;
}

function setActive($page){
return(basename($_SERVER['PHP_SELF'])==$page)?'active':'';
}

$id_user=$_SESSION['id_user'];

$query_user=mysqli_query($conn,"SELECT * FROM users WHERE id='$id_user'");
$u=mysqli_fetch_assoc($query_user);

$id_asrama_binaan=$u['id_asrama'];
$foto_path="../assets/img/".($u['foto_profil'] ?: 'default.png');

if(!empty($id_asrama_binaan)){

$keyword=str_replace(['Asrama_','Asrama ','asrama_'],'',$id_asrama_binaan);

$query_awardee=mysqli_query($conn,"
SELECT * FROM users
WHERE role='awardee'
AND(id_asrama LIKE '%$keyword%' OR universitas='$keyword')
ORDER BY nama ASC
");

}else{

$query_awardee=false;

}
?>

<!DOCTYPE html>
<<<<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Data Awardee</title>

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

.page-header{
background:linear-gradient(135deg,#063255,#0b4b7a);
border-radius:24px;
padding:35px;
position:relative;
overflow:hidden;
margin-bottom:30px;
}

.page-header::before{
content:'';
position:absolute;
width:320px;
height:320px;
background:rgba(255,255,255,0.04);
border-radius:50%;
top:-120px;
right:-120px;
}

.page-header-content{
position:relative;
z-index:2;
}

.page-title{
font-size:2rem;
font-weight:700;
color:white;
margin-bottom:10px;
}

.page-subtitle{
color:rgba(255,255,255,0.8);
font-size:0.95rem;
margin-bottom:18px;
}

.badge-asrama{
display:inline-flex;
align-items:center;
padding:10px 16px;
border-radius:999px;
background:rgba(13,110,253,0.18);
color:white;
font-size:0.85rem;
font-weight:600;
}

/* TABLE CARD */

.table-card{
background:white;
border-radius:22px;
border:1px solid var(--card-border);
box-shadow:0 4px 14px rgba(15,23,42,0.04);
overflow:hidden;
}

.table-top{
padding:24px 28px;
border-bottom:1px solid #eef2f7;
display:flex;
justify-content:space-between;
align-items:center;
flex-wrap:wrap;
gap:15px;
}

.table-title{
font-size:1.1rem;
font-weight:700;
margin:0;
color:#0f172a;
}

.table-desc{
font-size:0.9rem;
color:#64748b;
margin-top:4px;
}

/* TABLE */

.table{
margin-bottom:0;
}

.table thead th{
font-size:0.78rem;
text-transform:uppercase;
letter-spacing:0.5px;
color:#64748b;
padding:18px 20px;
background:#f8fafc;
border-bottom:1px solid #e9eef5;
white-space:nowrap;
}

.table tbody td{
padding:18px 20px;
vertical-align:middle;
border-bottom:1px solid #f1f5f9;
font-size:0.92rem;
}

.table tbody tr:hover{
background:#fafcff;
}

/* PROFILE */

.awardee-profile{
display:flex;
align-items:center;
gap:14px;
}

.avatar-member{
width:52px;
height:52px;
border-radius:50%;
object-fit:cover;
border:3px solid #fff;
box-shadow:0 3px 10px rgba(0,0,0,0.08);
background:#f1f5f9;
}

.awardee-name{
font-weight:700;
color:#0f172a;
margin-bottom:2px;
}

.awardee-email{
font-size:0.82rem;
color:#64748b;
}

/* BADGE */

.badge-campus{
padding:8px 14px;
border-radius:999px;
font-size:0.8rem;
font-weight:600;
background:#f8fafc;
border:1px solid #dbe3ec;
color:#334155;
display:inline-block;
}

/* BUTTON */

.btn-monitor{
background:linear-gradient(135deg,#0d6efd,#0b5ed7);
border:none;
color:white;
border-radius:10px;
padding:9px 16px;
font-size:0.85rem;
font-weight:600;
box-shadow:0 4px 10px rgba(13,110,253,0.15);
transition:0.2s;
text-decoration:none;
display:inline-flex;
align-items:center;
}

.btn-monitor:hover{
transform:translateY(-1px);
color:white;
}

/* EMPTY */

.empty-state{
padding:70px 20px;
text-align:center;
color:#94a3b8;
}

.empty-state i{
font-size:3rem;
margin-bottom:18px;
}

/* SCROLL */

::-webkit-scrollbar{
width:7px;
}

::-webkit-scrollbar-thumb{
background:#cbd5e1;
border-radius:10px;
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

.page-title{
font-size:1.6rem;
}

.table-top{
padding:20px;
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

<!-- HEADER -->

<div class="page-header">

<div class="page-header-content">

<h1 class="page-title">
<i class="fas fa-users me-2"></i>
Data Awardee
</h1>

<div class="page-subtitle">
Monitoring dan pengelolaan mahasiswa binaan kepala asrama.
</div>

<div class="badge-asrama">
<i class="fas fa-building me-2"></i>
<?= htmlspecialchars($id_asrama_binaan ?: '-') ?>
</div>

</div>

</div>

<!-- TABLE CARD -->

<div class="table-card">

<div class="table-top">

<div>

<h5 class="table-title">
Daftar Mahasiswa Binaan
</h5>

<div class="table-desc">
Seluruh data awardee yang berada dalam binaan asrama.
</div>

</div>

</div>

<div class="table-responsive">

<table class="table align-middle">

<thead>

<tr>

<th width="80">No</th>
<th>Profil Awardee</th>
<th>Universitas</th>
<th width="180">Aksi</th>

</tr>

</thead>

<tbody>

<?php

if($query_awardee && mysqli_num_rows($query_awardee)>0){

$no=1;

while($row=mysqli_fetch_assoc($query_awardee)){

?>

<tr>

<td class="fw-semibold">
<?= $no++ ?>
</td>

<td>

<div class="awardee-profile">

<img
src="../assets/img/<?= $row['foto_profil'] ?: 'default.png' ?>"
class="avatar-member"
>

<div>

<div class="awardee-name">
<?= htmlspecialchars($row['nama']) ?>
</div>

<div class="awardee-email">
<?= htmlspecialchars($row['email']) ?>
</div>

</div>

</div>

</td>

<td>

<span class="badge-campus">
<?= htmlspecialchars($row['universitas']) ?>
</span>

</td>

<td>

<a
href="detail_awardee.php?id=<?= $row['id'] ?>"
class="btn-monitor"
>

<i class="fas fa-eye me-2"></i>
Monitoring

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="4">

<div class="empty-state">

<i class="fas fa-folder-open"></i>

<div class="fw-semibold mb-2">
Data awardee tidak ditemukan
</div>

<div style="font-size:0.9rem;">
Belum ada mahasiswa binaan yang terdaftar pada asrama ini.
</div>

</div>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>
```
