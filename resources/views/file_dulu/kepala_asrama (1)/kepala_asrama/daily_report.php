<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'kepala_asrama'){
header("Location: ../login.php");
exit;
}

$id_user=$_SESSION['id_user'];
$pesan="";

$query_user=mysqli_query($conn,"SELECT * FROM users WHERE id='$id_user'");
$u=mysqli_fetch_assoc($query_user);

$foto_path="../assets/img/".($u['foto_profil'] ?: 'default.png');

function setActive($page){
return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}

if(isset($_POST['simpan_harian'])){

$tanggal=mysqli_real_escape_string($conn,$_POST['tanggal']);
$teknis=mysqli_real_escape_string($conn,$_POST['teknis']);
$jumlah_hadir=mysqli_real_escape_string($conn,$_POST['jumlah_hadir']);
$hasil=mysqli_real_escape_string($conn,$_POST['hasil']);
$asrama=$u['id_asrama'];

$query_ins="INSERT INTO laporan_harian(id_kepas,id_asrama,tanggal,teknis,jumlah_hadir,hasil_pertemuan)
VALUES('$id_user','$asrama','$tanggal','$teknis','$jumlah_hadir','$hasil')";

if(mysqli_query($conn,$query_ins)){

$pesan="
<div class='alert alert-success shadow-sm border-0 rounded-4'>
<i class='fas fa-circle-check me-2'></i>
Laporan harian berhasil disimpan!
</div>";

}else{

$pesan="
<div class='alert alert-danger shadow-sm border-0 rounded-4'>
<i class='fas fa-circle-xmark me-2'></i>
Gagal menyimpan: ".mysqli_error($conn)."
</div>";

}
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Daily Report</title>

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
color:rgba(255,255,255,0.82);
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

/* CARD */

.custom-card{
background:white;
border-radius:22px;
border:1px solid var(--card-border);
padding:28px;
box-shadow:0 4px 14px rgba(15,23,42,0.04);
}

/* FORM */

.form-label{
font-weight:600;
font-size:0.92rem;
margin-bottom:8px;
}

.form-control,
.form-select{
border-radius:12px;
border:1px solid #dbe3ec;
padding:12px 14px;
font-size:0.92rem;
}

.form-control:focus,
.form-select:focus{
border-color:#0d6efd;
box-shadow:0 0 0 3px rgba(13,110,253,0.12);
}

textarea{
min-height:180px !important;
resize:vertical;
}

/* BUTTON */

.btn-primary{
background:linear-gradient(135deg,#0d6efd,#0b5ed7);
border:none;
border-radius:12px;
padding:11px 22px;
font-weight:600;
box-shadow:0 4px 10px rgba(13,110,253,0.18);
}

.btn-primary:hover{
opacity:0.95;
transform:translateY(-1px);
}

/* TABLE */

.table{
margin-bottom:0;
}

.table thead th{
font-size:0.8rem;
text-transform:uppercase;
color:#64748b;
border-bottom:1px solid #e9eef5;
padding-bottom:14px;
}

.table td{
padding:16px 12px;
vertical-align:middle;
border-bottom:1px solid #f1f5f9;
font-size:0.92rem;
}

.badge-teknis{
padding:6px 12px;
border-radius:999px;
font-size:0.8rem;
font-weight:600;
display:inline-block;
}

.badge-online{
background:rgba(13,110,253,0.12);
color:#0d6efd;
}

.badge-offline{
background:rgba(25,135,84,0.12);
color:#198754;
}

.badge-hybrid{
background:rgba(255,193,7,0.15);
color:#b78103;
}

/* SCROLLBAR */

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

.page-header{
padding:30px;
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

<!-- MAIN CONTENT -->

<div class="main-content">

<!-- HEADER -->

<div class="page-header">

<div class="page-header-content">

<h1 class="page-title">
<i class="fas fa-calendar-day me-2"></i>
Daily Executive Report
</h1>

<div class="page-subtitle">
Dokumentasi kegiatan harian pembinaan awardee dan evaluasi aktivitas asrama.
</div>

<div class="badge-asrama">
<i class="fas fa-building me-2"></i>
<?= htmlspecialchars($u['id_asrama'] ?: '-') ?>
</div>

</div>

</div>

<?= $pesan; ?>

<!-- FORM -->

<form action="" method="POST" class="custom-card mb-4">

<div class="row g-4">

<div class="col-md-4">

<label class="form-label">
Tanggal
</label>

<input
type="date"
name="tanggal"
class="form-control"
value="<?= date('Y-m-d') ?>"
required
>

</div>

<div class="col-md-4">

<label class="form-label">
Teknis
</label>

<select name="teknis" class="form-select">

<option value="Offline">Offline</option>
<option value="Online">Online</option>
<option value="Hybrid">Hybrid</option>

</select>

</div>

<div class="col-md-4">

<label class="form-label">
Jumlah Hadir
</label>

<input
type="number"
name="jumlah_hadir"
class="form-control"
placeholder="0"
required
>

</div>

<div class="col-12">

<label class="form-label">
Hasil Pertemuan
</label>

<textarea
name="hasil"
class="form-control"
placeholder="Tuliskan hasil pertemuan dan evaluasi kegiatan..."
required
></textarea>

</div>

<div class="col-12 text-end">

<button
type="submit"
name="simpan_harian"
class="btn btn-primary"
>
<i class="fas fa-save me-2"></i>
Simpan Laporan
</button>

</div>

</div>

</form>

<!-- TABLE -->

<div class="custom-card">

<h5 class="fw-bold mb-4">
<i class="fas fa-clock-rotate-left me-2"></i>
Riwayat Laporan Harian
</h5>

<div style="max-height:500px;overflow-y:auto;">

<table class="table align-middle">

<thead>

<tr>

<th>Tanggal</th>
<th>Teknis</th>
<th>Jumlah</th>
<th>Hasil Pertemuan</th>

</tr>

</thead>

<tbody>

<?php

$riwayat=mysqli_query($conn,"
SELECT * FROM laporan_harian
WHERE id_kepas='$id_user'
ORDER BY tanggal DESC
");

if(mysqli_num_rows($riwayat)>0){

while($r=mysqli_fetch_assoc($riwayat)){

$badgeClass='badge-online';

if($r['teknis']=='Offline'){
$badgeClass='badge-offline';
}elseif($r['teknis']=='Hybrid'){
$badgeClass='badge-hybrid';
}

echo "

<tr>

<td width='150' class='fw-semibold'>
".date('d M Y',strtotime($r['tanggal']))."
</td>

<td width='130'>

<span class='badge-teknis $badgeClass'>
{$r['teknis']}
</span>

</td>

<td width='100'>
{$r['jumlah_hadir']} Orang
</td>

<td>
{$r['hasil_pertemuan']}
</td>

</tr>

";
}

}else{

echo "

<tr>

<td colspan='4' class='text-center py-5 text-muted'>

<i class='fas fa-folder-open fs-2 d-block mb-3'></i>

Belum ada riwayat laporan harian.

</td>

</tr>

";
}

?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>