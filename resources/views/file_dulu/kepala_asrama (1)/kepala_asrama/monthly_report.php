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

if(isset($_POST['simpan_laporan'])){

$bulan=mysqli_real_escape_string($conn,$_POST['bulan']);
$tahun=mysqli_real_escape_string($conn,$_POST['tahun']);
$summary=mysqli_real_escape_string($conn,$_POST['summary']);
$asrama=$u['id_asrama'];

if(!empty($summary)){

$query_ins="INSERT INTO laporan_bulanan(id_kepas,id_asrama,bulan,tahun,executive_summary)
VALUES('$id_user','$asrama','$bulan','$tahun','$summary')";

if(mysqli_query($conn,$query_ins)){

$pesan="
<div class='alert alert-success shadow-sm border-0 rounded-4'>
<i class='fas fa-circle-check me-2'></i>
Laporan berhasil disimpan!
</div>";

}else{

$pesan="
<div class='alert alert-danger shadow-sm border-0 rounded-4'>
<i class='fas fa-circle-xmark me-2'></i>
Gagal menyimpan: ".mysqli_error($conn)."
</div>";

}
}
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Monthly Report</title>

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
}

/* CARD */

.custom-card{
background:white;
border-radius:22px;
border:1px solid var(--card-border);
padding:28px;
box-shadow:0 4px 14px rgba(15,23,42,0.04);
margin-bottom:28px;
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
white-space:nowrap;
}

.table td{
padding:16px 12px;
vertical-align:middle;
border-bottom:1px solid #f1f5f9;
font-size:0.92rem;
}

.table tbody tr:hover{
background:#fafcff;
}

.badge-period{
background:rgba(13,110,253,0.10);
color:#0d6efd;
padding:7px 13px;
border-radius:999px;
font-size:0.8rem;
font-weight:600;
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
<i class="fas fa-file-alt me-2"></i>
Monthly Executive Report
</h1>

<div class="page-subtitle">
Buat dan kelola laporan bulanan kepala asrama secara profesional.
</div>

</div>

</div>

<?= $pesan; ?>

<!-- FORM -->

<form action="" method="POST" class="custom-card">

<div class="row g-4">

<div class="col-md-6">

<label class="form-label">
Bulan
</label>

<select name="bulan" class="form-select">

<?php

$bulan_list=[
'Januari',
'Februari',
'Maret',
'April',
'Mei',
'Juni',
'Juli',
'Agustus',
'September',
'Oktober',
'November',
'Desember'
];

foreach($bulan_list as $bln){
echo "<option>$bln</option>";
}

?>

</select>

</div>

<div class="col-md-6">

<label class="form-label">
Tahun
</label>

<input
type="number"
name="tahun"
class="form-control"
value="<?= date('Y') ?>"
required
>

</div>

<div class="col-12">

<label class="form-label">
Executive Summary
</label>

<textarea
name="summary"
class="form-control"
placeholder="Tuliskan pengamatan mendalam mengenai kondisi asrama..."
required
></textarea>

</div>

<div class="col-12 text-end">

<button
type="submit"
name="simpan_laporan"
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
Riwayat Laporan
</h5>

<div style="max-height:500px;overflow-y:auto;">

<table class="table align-middle">

<thead>

<tr>

<th>Periode</th>
<th>Executive Summary</th>
<th>Tanggal</th>

</tr>

</thead>

<tbody>

<?php

$riwayat=mysqli_query($conn,"
SELECT * FROM laporan_bulanan
WHERE id_kepas='$id_user'
ORDER BY id DESC
");

if(mysqli_num_rows($riwayat)>0){

while($r=mysqli_fetch_assoc($riwayat)){

echo "

<tr>

<td width='180'>

<span class='badge-period'>
{$r['bulan']} {$r['tahun']}
</span>

</td>

<td>
{$r['executive_summary']}
</td>

<td width='150' class='text-muted fw-semibold'>
".date('d M Y',strtotime($r['created_at']))."
</td>

</tr>

";
}

}else{

echo "

<tr>

<td colspan='3' class='text-center py-5 text-muted'>

<i class='fas fa-folder-open fs-2 d-block mb-3'></i>

Belum ada riwayat laporan.

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