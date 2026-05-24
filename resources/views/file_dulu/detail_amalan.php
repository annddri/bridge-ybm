<?php
session_start();
include 'config/koneksi.php';

// Pastikan yang login BUKAN awardee (hanya admin/pengurus yang boleh mengintip rekap)
if (!isset($_SESSION['id_user']) || $_SESSION['role'] == 'awardee') { 
    header("Location: index.php"); 
    exit; 
}

// Tangkap ID Awardee yang mau dilihat rekapnya
if (!isset($_GET['id_awardee'])) {
    header("Location: amalan.php");
    exit;
}

$id_awardee = (int)$_GET['id_awardee'];

// 1. TAHUN & BULAN DINAMIS
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

// 2. JUMLAH HARI OTOMATIS SESUAI KALENDER
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil data profil Kepala Asrama/Admin yang sedang login untuk sidebar
$id_admin = $_SESSION['id_user'];
$query_admin = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_admin'");
$u = mysqli_fetch_assoc($query_admin);
$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// Ambil data target Awardee yang sedang diintip
$query_target = mysqli_query($conn, "SELECT nama, role FROM users WHERE id = '$id_awardee' AND role = 'awardee'");
if (mysqli_num_rows($query_target) == 0) {
    echo "<script>alert('Data Awardee tidak ditemukan!'); window.location='amalan.php';</script>";
    exit;
}
$target_user = mysqli_fetch_assoc($query_target);

// 3. KONFIGURASI TARGET (Sama dengan rumus asli amalan.php)
$list_amalan = [
    'shalat_5_waktu'  => ['nama' => 'Shalat Berjamaah 5 Waktu', 'tipe' => 'harian', 'target' => 5, 'unit' => '/hari'],
    'shalat_malam'    => ['nama' => 'Shalat Malam/Qiyamul Lail', 'tipe' => 'bulanan', 'target' => 10, 'unit' => '/bln'],
    'dzikir_pagi'     => ['nama' => 'Dzikir Pagi', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
    'mendoakan_orang' => ['nama' => 'Mendoakan/memaafkan orang', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
    'shalat_dhuha'    => ['nama' => 'Shalat Dhuha', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
    'membaca_alquran' => ['nama' => 'Membaca Al-Quran', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
    'shaum_sunnah'    => ['nama' => 'Shaum Sunnah', 'tipe' => 'bulanan', 'target' => 3, 'unit' => '/bln'],
    'berinfak'        => ['nama' => 'Berinfak', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
];

// Load data amalan milik awardee dari database
$data_db = [];
$res = mysqli_query($conn, "SELECT * FROM amalan_yaumiyah WHERE id_user='$id_awardee' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
while($row = mysqli_fetch_assoc($res)){
    $d = (int)date('d', strtotime($row['tanggal']));
    foreach($list_amalan as $key => $val) { $data_db[$key][$d] = isset($row[$key]) ? $row[$key] : ''; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekap Spiritual - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --accent-color: #198754; /* Hijau Emerald khas Admin Monitoring */
            --bg-light: #f8fafc;
            --navy-theme: #063255;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Sidebar Navigasi */
        .sidebar { 
            width: 280px; height: 100vh; position: fixed; top: 0; left: 0; 
            background: linear-gradient(180deg, #063255 0%, #041f35 100%); 
            color: var(--sidebar-text); padding-top: 10px; z-index: 1000; overflow-y: auto; 
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sidebar-brand { text-align: center; padding: 25px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .brand-logo { width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, 0.2); margin-bottom: 12px; object-fit: cover; }
        
        .nav-link { color: var(--sidebar-text); padding: 11px 25px; display: flex; align-items: center; transition: all 0.2s ease; font-size: 0.92rem; text-decoration: none; border-left: 4px solid transparent; }
        .nav-link i { width: 24px; margin-right: 12px; font-size: 1.05rem; opacity: 0.8; }
        .nav-link:hover { color: #fff; background-color: var(--sidebar-hover); padding-left: 28px; }
        .nav-link.active { color: #fff; background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }

        /* Layout Main Konten */
        .main-content { margin-left: 280px; padding: 35px 30px; transition: all 0.3s ease; }
        
        .header-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            border: 1px solid #e2e8f0 !important;
        }

        .select-bulan { min-width: 140px; }
        .select-tahun { min-width: 95px; }
        
        /* Kontainer Tabel Spreadsheet Kunci */
        .table-container {
            max-height: 72vh;
            overflow: auto;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            background: white;
        }

        .table-amalan { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-amalan thead th {
            position: sticky; top: 0; background: var(--navy-theme) !important; color: white;
            z-index: 30; padding: 14px 8px; border: 1px solid #041f35; font-weight: 600;
        }

        .sticky-col-1 {
            position: sticky; left: 0; background-color: white !important; z-index: 20;
            min-width: 240px; border-right: 1px solid #eef2f5 !important; box-shadow: 3px 0 6px rgba(0,0,0,0.02);
            padding-left: 15px !important;
        }
        .sticky-col-2 {
            position: sticky; left: 240px; background-color: #f8fafc !important; z-index: 19;
            min-width: 90px; border-right: 2px solid #e2e8f0 !important; text-align: center;
        }

        thead th.sticky-col-1 { z-index: 40; left: 0; }
        thead th.sticky-col-2 { z-index: 39; left: 240px; }

        /* Input mode read-only style */
        .form-check-input:disabled { opacity: 0.85; border-color: #cbd5e1; cursor: not-allowed; }
        .form-check-input:checked { background-color: #198754 !important; border-color: #198754 !important; }

        /* Style Shalat Badge Disabled */
        .shalat-char-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 16px; height: 16px; font-size: 0.58rem; font-weight: 700; border-radius: 4px;
            background-color: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0;
        }
        .bg-shalat-checked {
            background-color: #198754 !important; color: white !important; border-color: #198754 !important;
        }

        .percent-badge { background: #d1e7dd; color: #0f5132; padding: 6px 12px; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6"><?= htmlspecialchars($u['nama']) ?></h5>
            <small class="text-success fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;"><?= htmlspecialchars($u['role']) ?></small>
        </div>
    </div>
    <div class="mt-3">
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="data_awardee.php" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px;">Fitur Monitoring</div>
        <a href="amalan.php" class="nav-link active"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="logout.php" class="nav-link logout-link"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="card header-card shadow-sm border-0 mb-4 rounded-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center p-4">
                <div>
                    <span class="badge bg-secondary mb-2 text-uppercase font-monospace">Mode Peninjauan (Read-Only)</span>
                    <h4 class="fw-bold m-0 text-uppercase" style="color: var(--navy-theme);">
                        <i class="fas fa-user-check me-2 text-success"></i>Rekap: <?= htmlspecialchars($target_user['nama']) ?>
                    </h4>
                    <p class="text-muted small m-0 mt-1">Melihat Log Spreadsheet Ibadah Yaumiyah Bulanan Awardee</p>
                </div>
                
                <form class="d-flex gap-2 mt-3 mt-md-0" method="GET">
                    <input type="hidden" name="id_awardee" value="<?= $id_awardee ?>">
                    <select name="bulan" class="form-select form-select-sm select-bulan shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($m==$bulan?'selected':'') ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="tahun" class="form-select form-select-sm select-tahun shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                        <?php for($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>" <?= ($y == $tahun ? 'selected' : '') ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <a href="amalan.php" class="btn btn-dark btn-sm px-4 rounded-pill shadow-sm fw-semibold">Kembali</a>
                </form>
            </div>
        </div>

        <div class="table-container border">
            <table class="table table-amalan align-middle m-0">
                <thead>
                    <tr>
                        <th class="sticky-col-1">Aktivitas</th>
                        <th class="sticky-col-2">Target</th>
                        <?php for($d=1; $d<=$jumlah_hari; $d++) echo "<th class='text-center'>$d</th>"; ?>
                        <th class="text-center">Total %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list_amalan as $key => $attr): ?>
                    <tr>
                        <td class="sticky-col-1 fw-bold text-dark"><?= $attr['nama'] ?></td>
                        <td class="sticky-col-2 fw-bold text-success">
                            <?= $attr['target'] ?><span style="font-size: 0.6rem; color: #999;"><?= $attr['unit'] ?></span>
                        </td>
                        <?php 
                        $total_input = 0; $hari_aktif = 0;
                        for($d=1; $d<=$jumlah_hari; $d++): 
                            $val = isset($data_db[$key][$d]) ? $data_db[$key][$d] : '';
                            if($val !== '') { $total_input += (int)$val; $hari_aktif++; }
                        ?>
                            <td class="p-1 text-center border-end border-light">
                                <?php if ($key === 'shalat_5_waktu'): ?>
                                    <div class="d-flex justify-content-center align-items-center gap-1 px-1" style="min-width: 95px;">
                                        <?php 
                                        $sh_label = ['S','D','A','M','I'];
                                        for($s=0; $s<5; $s++):
                                            $isShalatChecked = ($val !== '' && $val > $s) ? 'bg-shalat-checked' : '';
                                        ?>
                                            <span class="shalat-char-badge <?= $isShalatChecked ?>"><?= $sh_label[$s] ?></span>
                                        <?php endfor; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="d-flex justify-content-center">
                                        <input type="checkbox" class="form-check-input" disabled <?= ($val == 1 ? 'checked' : '') ?>>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                        
                        <td class="text-center fw-bold bg-light" style="min-width: 80px;">
                            <span class="percent-badge">
                                <?php 
                                if ($hari_aktif > 0) {
                                    $p = ($attr['tipe'] == 'harian') ? ($total_input / $hari_aktif) / $attr['target'] * 100 : ($total_input / $attr['target']) * 100;
                                    echo round(min($p, 100), 1) . "%";
                                } else echo "0%";
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>