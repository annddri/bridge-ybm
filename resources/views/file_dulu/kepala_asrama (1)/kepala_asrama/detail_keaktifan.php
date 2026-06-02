```php
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

function setActive($page){
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}

$id_awardee = mysqli_real_escape_string($conn, $_GET['id_awardee']);

$id_user = $_SESSION['id_user'];

$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);

$foto_path = "../assets/img/" . (!empty($u['foto_profil']) ? $u['foto_profil'] : 'default.png');

$query_awardee = mysqli_query($conn, "
SELECT nama, universitas, foto_profil
FROM users
WHERE id = '$id_awardee'
AND role = 'awardee'
");

$data_awardee = mysqli_fetch_assoc($query_awardee);

if (!$data_awardee) {
    echo "
    <script>
        alert('Data awardee tidak ditemukan!');
        window.location='data_awardee.php';
    </script>
    ";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
Monitoring Portofolio
</title>

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

*{
margin:0;
padding:0;
box-sizing:border-box;
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

/* HERO */

.top-card{
background:linear-gradient(135deg,#062b49 0%, #0f4c81 100%);
border-radius:28px;
padding:35px;
color:white;
position:relative;
overflow:hidden;
margin-bottom:30px;
}

.top-card::before{
content:'';
position:absolute;
width:320px;
height:320px;
border-radius:50%;
background:rgba(255,255,255,0.05);
right:-100px;
top:-140px;
}

.awardee-info{
display:flex;
align-items:center;
gap:20px;
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
margin-bottom:6px;
}

.awardee-campus{
opacity:0.85;
font-size:0.95rem;
}

/* CARD */

.card-custom{
background:white;
border-radius:24px;
padding:28px;
border:1px solid var(--card-border);
box-shadow:0 8px 25px rgba(15,23,42,0.04);
margin-bottom:28px;
}

/* TABLE */

.table-custom{
width:100%;
border-collapse:collapse;
}

.table-custom thead th{ background:#062b49; color:white; padding:14px; font-size:0.85rem; text-align:center; vertical-align:middle; font-weight:700; }

.table-custom tbody td{ vertical-align:middle; }

.table-custom tbody td{
padding:14px;
border-bottom:1px solid #f1f5f9;
font-size:0.9rem;
}

.table-custom tbody tr:hover{
background:#f8fafc;
}

.badge-level{
background:#eff6ff;
color:#2563eb;
padding:6px 12px;
border-radius:999px;
font-size:0.75rem;
font-weight:700;
}

.badge-status{
padding:6px 12px;
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

.section-title{
font-size:1rem;
font-weight:800;
margin-bottom:18px;
color:#0f172a;
}

.empty-state{
text-align:center;
padding:40px 20px;
color:#94a3b8;
font-size:0.9rem;
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

.awardee-info{
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

<div class="sidebar shadow">

<div class="sidebar-brand">

<img
src="<?= $foto_path ?>?t=<?= time() ?>"
alt="Profile"
class="brand-logo shadow"
>

<h6 class="fw-bold mb-0 text-white">
<?= htmlspecialchars($u['nama'] ?? 'User') ?>
</h6>

<small
class="text-info text-uppercase fw-bold"
style="font-size:0.7rem;letter-spacing:1px;"
>
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

<!-- HERO -->

<div class="top-card">

<div class="awardee-info">

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

<div class="mb-4"> <a href="data_awardee.php" class="btn btn-dark rounded-pill px-4 py-2" > <i class="fas fa-arrow-left me-2"></i> Kembali </a> </div> <?php $kategori_list = [
'prestasi' => '🏆 Prestasi',
'organisasi' => '👥 Organisasi',
'workshop' => '📚 Seminar / Workshop'
];

foreach($kategori_list as $key => $judul):

?>

<div class="card-custom">

<div class="section-title">
<?= $judul ?>
</div>

<div class="table-responsive">

<table class="table-custom">

<thead>

<tr>
<th>Waktu</th>
<th>Nama Kegiatan</th>
<th>Penyelenggara / Jabatan</th>
<th>Level</th>

</tr>

</thead>

<tbody>

<?php

$query = mysqli_query($conn, "
SELECT *
FROM keaktifan_kampus
WHERE id_user = '$id_awardee'
AND kategori = '$key'
ORDER BY id DESC
");

if(mysqli_num_rows($query) > 0):

while($row = mysqli_fetch_assoc($query)):

?>

<tr>

<td>
<?= htmlspecialchars($row['tanggal_tahun']) ?>
</td>

<td>
<strong>
<?= htmlspecialchars($row['nama_kegiatan']) ?>
</strong>
</td>

<td>
<?= htmlspecialchars($row['penyelenggara_jabatan']) ?>
</td>

<td>

<span class="badge-level">
<?= htmlspecialchars($row['level']) ?>
</span>

</td>

</tr>

<?php
endwhile;

else:
?>

<tr>

<td colspan="4">

<div class="empty-state">
Belum ada data <?= strtolower($judul) ?>
</div>

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
```
