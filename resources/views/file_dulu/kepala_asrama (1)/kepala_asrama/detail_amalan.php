```php
<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] == 'awardee') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id_awardee'])) {
    header("Location: monitoring_amalan.php");
    exit;
}

function setActive($page){
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}

$id_awardee = mysqli_real_escape_string($conn, $_GET['id_awardee']);
$id_user = $_SESSION['id_user'];

$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);

$foto_path = "../assets/img/" . ($u['foto_profil'] ?: 'default.png');

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
        window.location='monitoring_amalan.php';
    </script>
    ";
    exit;
}

$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$list_amalan = [

    'shalat_5_waktu' => [
        'nama' => 'Shalat 5 Waktu',
        'tipe' => 'harian',
        'target' => 5,
        'unit' => '/hari'
    ],

    'shalat_malam' => [
        'nama' => 'Shalat Malam',
        'tipe' => 'bulanan',
        'target' => 10,
        'unit' => '/bln'
    ],

    'dzikir_pagi' => [
        'nama' => 'Dzikir Pagi',
        'tipe' => 'harian',
        'target' => 1,
        'unit' => '/hari'
    ],

    'mendoakan_orang' => [
        'nama' => 'Memaafkan Orang',
        'tipe' => 'harian',
        'target' => 1,
        'unit' => '/hari'
    ],

    'shalat_dhuha' => [
        'nama' => 'Shalat Dhuha',
        'tipe' => 'harian',
        'target' => 1,
        'unit' => '/hari'
    ],

    'membaca_alquran' => [
        'nama' => 'Baca Al-Quran',
        'tipe' => 'harian',
        'target' => 1,
        'unit' => '/hari'
    ],

    'shaum_sunnah' => [
        'nama' => 'Shaum Sunnah',
        'tipe' => 'bulanan',
        'target' => 3,
        'unit' => '/bln'
    ],

    'berinfak' => [
        'nama' => 'Berinfak',
        'tipe' => 'harian',
        'target' => 1,
        'unit' => '/hari'
    ]

];

$data_db = [];

$res = mysqli_query($conn, "
SELECT *
FROM amalan_yaumiyah
WHERE id_user='$id_awardee'
AND MONTH(tanggal)='$bulan'
AND YEAR(tanggal)='$tahun'
");

while ($row = mysqli_fetch_assoc($res)) {

    $d = (int)date('d', strtotime($row['tanggal']));

    foreach ($list_amalan as $key => $val) {
        $data_db[$key][$d] = isset($row[$key]) ? $row[$key] : '';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Monitoring Spiritual</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<style>

:root{
--sidebar-bg:#063255;
--sidebar-text:rgba(255,255,255,0.85);
--sidebar-hover:rgba(255,255,255,0.08);
--accent-color:#0d6efd;
--bg-light:#f4f7fb;
--card-border:#e7edf4;
--text-dark:#1e293b;
--success:#22c55e;
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

/* TOP HEADER */

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

.score-badge{
display:inline-flex;
align-items:center;
gap:8px;
background:rgba(255,255,255,0.1);
border:1px solid rgba(255,255,255,0.1);
padding:10px 16px;
border-radius:999px;
margin-top:14px;
font-weight:700;
backdrop-filter:blur(10px);
}

/* FILTER */

.filter-card{
background:white;
border-radius:22px;
padding:20px;
margin-bottom:28px;
border:1px solid var(--card-border);
box-shadow:0 6px 18px rgba(15,23,42,0.04);
}

/* CHART */

.chart-card{
background:white;
border-radius:24px;
padding:28px;
border:1px solid var(--card-border);
box-shadow:0 8px 25px rgba(15,23,42,0.04);
margin-bottom:28px;
}

.chart-title{
font-size:1rem;
font-weight:700;
margin-bottom:20px;
}

.chart-wrapper{
position:relative;
width:100%;
max-width:700px;
height:400px;
margin:auto;
}

/* TABLE */

.table-wrapper{
background:white;
border-radius:24px;
overflow:hidden;
border:1px solid var(--card-border);
box-shadow:0 8px 25px rgba(15,23,42,0.04);
}

.table-scroll{
overflow-x:auto;
}

.table-amalan{
width:100%;
min-width:1300px;
border-collapse:collapse;
}

.table-amalan thead th{
background:#062b49;
color:white;
font-size:0.78rem;
padding:14px 10px;
text-align:center;
white-space:nowrap;
}

.table-amalan tbody td{
border-bottom:1px solid #f1f5f9;
padding:12px 8px;
text-align:center;
}

.table-amalan tbody tr:hover{
background:#f8fafc;
}

.sticky-col-1{
position:sticky;
left:0;
background:white;
z-index:20;
min-width:220px;
text-align:left !important;
padding-left:18px !important;
font-weight:700;
color:#0f172a;
border-right:1px solid #e2e8f0;
}

.sticky-col-2{
position:sticky;
left:220px;
background:#f8fafc;
z-index:19;
min-width:100px;
border-right:2px solid #e2e8f0;
font-weight:700;
color:#0d6efd;
}

thead .sticky-col-1{
z-index:40;
}

thead .sticky-col-2{
z-index:39;
}

/* BADGE SHALAT */

.shalat-group{
display:flex;
justify-content:center;
gap:4px;
}

.shalat-badge{
width:17px;
height:17px;
border-radius:5px;
background:#e2e8f0;
color:#64748b;
font-size:0.58rem;
display:flex;
align-items:center;
justify-content:center;
font-weight:700;
}

.shalat-active{
background:var(--success);
color:white;
}

.form-check-input{
width:1.1rem;
height:1.1rem;
pointer-events:none;
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

.awardee-name{
font-size:1.4rem;
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

<div class="top-card">

<div class="awardee-info">

<img
src="../assets/img/<?= $data_awardee['foto_profil'] ?: 'default.png' ?>"
class="awardee-photo"
>

<div>

<div class="awardee-name">
<?= htmlspecialchars($data_awardee['nama']) ?>
</div>

<div class="awardee-campus">
<?= htmlspecialchars($data_awardee['universitas']) ?>
</div>

<div class="score-badge">
<i class="fas fa-chart-line"></i>
Rata-rata Bulanan :
<span id="grand-total-pct">0%</span>
</div>

</div>

</div>

</div>

<div class="filter-card">

<form class="row g-3 align-items-center">

<input type="hidden" name="id_awardee" value="<?= $id_awardee ?>">

<div class="col-md-3">

<label class="form-label fw-semibold small">
Bulan
</label>

<select
name="bulan"
class="form-select rounded-4"
onchange="this.form.submit()"
>

<?php for($m=1; $m<=12; $m++): ?>

<option
value="<?= $m ?>"
<?= ($m == $bulan ? 'selected' : '') ?>
>

<?= date('F', mktime(0,0,0,$m,1)) ?>

</option>

<?php endfor; ?>

</select>

</div>

<div class="col-md-3">

<label class="form-label fw-semibold small">
Tahun
</label>

<select
name="tahun"
class="form-select rounded-4"
onchange="this.form.submit()"
>

<?php for($y = 2000; $y <= 2100; $y++): ?>

<option
value="<?= $y ?>"
<?= ($y == $tahun ? 'selected' : '') ?>
>

<?= $y ?>

</option>

<?php endfor; ?>

</select>

</div>

<div class="col-md-3">

<label class="form-label fw-semibold small opacity-0">
Tombol
</label>

<div>

<a
href="monitoring_amalan.php"
class="btn btn-dark rounded-pill px-4 d-inline-flex align-items-center justify-content-center"
style="height:48px;"
>

<i class="fas fa-arrow-left me-2"></i>
Kembali

</a>

</div>

</div>

</form>

</div>

<div class="chart-card">

<div class="chart-title">

<i class="fas fa-chart-pie text-success me-2"></i>
Statistik Spiritual Bulanan

</div>

<div class="chart-wrapper">

<canvas id="spiritualPieChart"></canvas>

</div>

</div>

<div class="table-wrapper">

<div class="table-scroll">

<?php

$js_labels = [];
$js_pcts = [];

foreach($list_amalan as $key => $attr){

$total_input = 0;
$hari_aktif = 0;

for($d=1; $d<=$jumlah_hari; $d++){

$val = isset($data_db[$key][$d])
? $data_db[$key][$d]
: '';

if($val !== ''){
$total_input += (int)$val;
$hari_aktif++;
}

}

$p_amalan = 0;

if($hari_aktif > 0){

$p_amalan = ($attr['tipe'] == 'harian')
? ($total_input / $hari_aktif) / $attr['target'] * 100
: ($total_input / $attr['target']) * 100;

$p_amalan = round(min($p_amalan,100),1);

}

$js_labels[] = $attr['nama'];
$js_pcts[] = $p_amalan;

}

?>

<table class="table-amalan">

<thead>

<tr>

<th class="sticky-col-1">
Aktivitas
</th>

<th class="sticky-col-2">
Target
</th>

<?php
for($d=1; $d<=$jumlah_hari; $d++){
echo "<th>$d</th>";
}
?>

</tr>

</thead>

<tbody>

<?php foreach($list_amalan as $key => $attr): ?>

<tr>

<td class="sticky-col-1">
<?= $attr['nama'] ?>
</td>

<td class="sticky-col-2">

<?= $attr['target'] ?>

<span style="font-size:0.65rem;color:#64748b;">
<?= $attr['unit'] ?>
</span>

</td>

<?php for($d=1; $d<=$jumlah_hari; $d++): ?>

<?php
$val = isset($data_db[$key][$d])
? $data_db[$key][$d]
: '';
?>

<td>

<?php if($key === 'shalat_5_waktu'): ?>

<div class="shalat-group">

<?php
$sh_label = ['S','D','A','M','I'];

for($s=0; $s<5; $s++):

$active = ($val !== '' && $val > $s)
? 'shalat-active'
: '';
?>

<span class="shalat-badge <?= $active ?>">
<?= $sh_label[$s] ?>
</span>

<?php endfor; ?>

</div>

<?php else: ?>

<?php $is_checked = ( $val !== '' && $val !== null && $val != 0 ); ?> <input type="checkbox" class="form-check-input" <?= ($is_checked ? 'checked' : '') ?> disabled >

<?php endif; ?>

</td>

<?php endfor; ?>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

<script>

const labelsData = <?= json_encode($js_labels) ?>;
const originalPcts = <?= json_encode($js_pcts) ?>;

let totalScore = 0;

originalPcts.forEach(p => {
totalScore += p;
});

let grandTotal = totalScore / labelsData.length;

document.getElementById('grand-total-pct').innerText =
grandTotal.toFixed(1) + '%';

const chartDataValues = originalPcts.map(p => p === 0 ? 0.1 : p);

Chart.register(ChartDataLabels);

const ctx = document
.getElementById('spiritualPieChart')
.getContext('2d');

new Chart(ctx, {

type:'doughnut',

data:{
labels:labelsData,

datasets:[{

data:chartDataValues,

backgroundColor:[
'#22c55e',
'#3b82f6',
'#f59e0b',
'#ef4444',
'#8b5cf6',
'#14b8a6',
'#f97316',
'#0ea5e9'
],

borderWidth:3,
borderColor:'#ffffff'

}]
},

options:{

responsive:true,
maintainAspectRatio:false,
cutout:'58%',

plugins:{

legend:{
position:'right',

labels:{
padding:18,
usePointStyle:true,
pointStyle:'circle',

font:{
size:12,
weight:'600'
}

}

},

tooltip:{
callbacks:{

label:function(context){

let realValue =
originalPcts[context.dataIndex];

return ' ' +
context.label +
': ' +
realValue +
'%';

}

}
},

datalabels:{

color:'#fff',

font:{
weight:'700',
size:11
},

formatter:function(value,context){

let realValue =
originalPcts[context.dataIndex];

return realValue + '%';

}

}

}

}

});

</script>

</body>
</html>
```
