<?php
session_start();
include 'config/koneksi.php';
if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit; }

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// 1. TAHUN & BULAN DINAMIS
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');

// 2. JUMLAH HARI OTOMATIS SESUAI KALENDER
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambal data user untuk foto profil di sidebar
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);

$foto_path = "assets/img/" . ($u['foto_profil'] ?: 'default.png');

// 3. KONFIGURASI TARGET (Sesuai Rumus Spreadsheet Asli)
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

// Load data dari database khusus Awardee
$data_db = [];
if ($role_user == 'awardee') {
    $res = mysqli_query($conn, "SELECT * FROM amalan_yaumiyah WHERE id_user='$id_user' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
    while($row = mysqli_fetch_assoc($res)){
        $d = (int)date('d', strtotime($row['tanggal']));
        foreach($list_amalan as $key => $val) { $data_db[$key][$d] = isset($row[$key]) ? $row[$key] : ''; }
    }
}

// Fungsi pembantu hitung total persentase bulanan (supaya tidak error jika fungsinya belum terdefinisi di config)
if (!function_exists('hitungTotalPersentaseBulanan')) {
    function hitungTotalPersentaseBulanan($conn, $id_user, $bulan, $tahun) {
        // Logika internal atau kembalikan nilai 0 sementara jika dihitung via Ajax
        return 0; 
    }
}

// Konfigurasi Aksen Tema Berdasarkan Role
$role_colors = [
    'awardee'       => 'primary',
    'kepala asrama' => 'success',
    'fasilitator'   => 'info',
    'supervisor'    => 'warning',
    'ho'            => 'danger'
];
$theme = $role_colors[$role_user] ?? 'secondary';
$accent_color = ($role_user == 'awardee') ? '#0d6efd' : (($role_user == 'kepala asrama') ? '#198754' : '#063255');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spiritual Tracker - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --accent-color: <?= $accent_color ?>;
            --bg-light: #f8fafc;
            --navy-theme: #063255;
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Navigasi Sidebar Terintegrasi Premium */
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
        .nav-link.active { color: #fff; background-color: rgba(13, 110, 253, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }

        /* Layout Konten Utama */
        .main-content { margin-left: 280px; padding: 35px 30px; transition: all 0.3s ease; }
        .card-custom { border: 1px solid rgba(6, 50, 85, 0.08); border-radius: 20px; background: #ffffff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important; padding: 30px; }
        
        .header-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            border: 1px solid #e2e8f0 !important;
        }

        .select-bulan { min-width: 140px; }
        .select-tahun { min-width: 95px; }

        .badge-progress-total {
            background-color: var(--navy-theme) !important;
            color: #ffffff !important;
            font-weight: 600;
        }
        
        /* Modifikasi Kontainer Tabel Spreadsheet-Style */
        .table-container {
            max-height: 72vh;
            overflow: auto;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.04);
            background: white;
        }

        .table-amalan { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-amalan thead th {
            position: sticky;
            top: 0;
            background: var(--navy-theme) !important;
            color: white;
            z-index: 30;
            padding: 14px 8px;
            border: 1px solid #041f35;
            font-weight: 600;
        }

        /* Kolom Sticky Aktivitas & Target */
        .sticky-col-1 {
            position: sticky;
            left: 0;
            background-color: white !important;
            z-index: 20;
            min-width: 240px;
            border-right: 1px solid #eef2f5 !important;
            box-shadow: 3px 0 6px rgba(0,0,0,0.02);
            padding-left: 15px !important;
        }

        .sticky-col-2 {
            position: sticky;
            left: 240px;
            background-color: #f8fafc !important;
            z-index: 19;
            min-width: 90px;
            border-right: 2px solid #e2e8f0 !important;
            text-align: center;
        }

        thead th.sticky-col-1 { z-index: 40; left: 0; }
        thead th.sticky-col-2 { z-index: 39; left: 240px; }

        .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; transition: all 0.2s; }
        .form-check-input:checked { background-color: var(--accent-color); border-color: var(--accent-color); }
        
        /* Desain Aesthetic Ceklis Shalat Horizontal */
        .shalat-badge-container { cursor: pointer; user-select: none; margin: 0 1px; }
        .shalat-char-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 16px; height: 16px; font-size: 0.58rem; font-weight: 700; border-radius: 4px;
            background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; transition: all 0.15s ease;
        }
        .shalat-badge-container input:checked + .shalat-char-badge {
            background-color: var(--accent-color); color: #ffffff; border-color: var(--accent-color);
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.35);
        }

        .percent-badge { background: #e2f0d9; color: #385723; padding: 6px 12px; border-radius: 20px; font-weight: bold; }
        .alert-saved { position: fixed; bottom: 25px; right: 25px; display: none; z-index: 9999; border-radius: 10px; }

        @media (max-width: 991.98px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; padding: 20px; }
            .sidebar-brand { display: flex; align-items: center; text-align: left; padding: 15px; }
            .brand-logo { width: 45px; height: 45px; margin-bottom: 0; margin-right: 15px; }
            .logout-link { border-top: none; margin-top: 0; padding-top: 11px !important; }
        }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6"><?= htmlspecialchars($u['nama']) ?></h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;"><?= htmlspecialchars($u['role']) ?></small>
        </div>
    </div>

    <div class="mt-3">
        <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="profile.php" class="nav-link"><i class="fas fa-user-circle"></i> Profil Saya</a>
        
        <?php if ($role_user != 'awardee'): ?>
        <a href="data_awardee.php" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        <?php endif; ?>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">Fitur Monitoring</div>
        <a href="amalan.php" class="nav-link active"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="tahfidz.php" class="nav-link"><i class="fas fa-book-quran"></i> Tahfidz Tracker</a>
        <a href="akademik.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Akademik</a>
        <a href="keaktifan.php" class="nav-link"><i class="fas fa-award"></i> Portofolio</a>
        <a href="masyarakat.php" class="nav-link"><i class="fas fa-people-group"></i> Sosial Masyarakat</a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">Fitur Asrama</div>
        <a href="inventaris.php" class="nav-link"><i class="fas fa-boxes-stacked"></i> Inventaris Asrama</a>
        <a href="keuangan.php" class="nav-link"><i class="fas fa-wallet"></i> Keuangan Asrama</a>
        <a href="perizinan.php" class="nav-link"><i class="fas fa-envelope-open-text"></i> Perizinan Asrama</a>
        
        <a href="logout.php" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        
        <?php if ($role_user == 'awardee'): ?>
            <div class="card header-card shadow-sm border-0 mb-4 rounded-4">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center p-4">
                    <div>
                        <h4 class="fw-bold m-0 text-uppercase" style="color: var(--navy-theme); letter-spacing: 0.5px;"><i class="fas fa-pray me-2 text-primary"></i>Spiritual Tracker</h4>
                        <p class="text-muted small m-0 mt-1">Input Amalan Yaumiyah Bulanan Berbasis Spreadsheet</p>
                        
                        <div class="mt-2">
                            <span class="badge badge-progress-total p-2 rounded-3 fs-6 shadow-sm">
                                <i class="fas fa-chart-line me-1 text-info"></i> Total Progress Bulan Ini: 
                                <span id="grand-total-pct">0.0%</span>
                            </span>
                        </div>
                    </div>
                    <form class="d-flex gap-2 mt-3 mt-md-0">
                        <select name="bulan" class="form-select form-select-sm select-bulan shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?= $m ?>" <?= ($m==$bulan?'selected':'') ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="tahun" class="form-select form-select-sm select-tahun shadow-sm rounded-pill px-3" onchange="this.form.submit()">
                            <?php for($y = $tahun - 2; $y <= $tahun + 2; $y++): ?>
                                <option value="<?= $y ?>" <?= ($y == $tahun ? 'selected' : '') ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <a href="index.php" class="btn btn-dark btn-sm px-4 rounded-pill shadow-sm fw-semibold">Kembali</a>
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
                            <td class="sticky-col-2 fw-bold text-primary">
                                <?= $attr['target'] ?><span style="font-size: 0.6rem; color: #999;"><?= $attr['unit'] ?></span>
                            </td>
                            <?php 
                            $total_input = 0; $hari_aktif = 0;
                            for($d=1; $d<=$jumlah_hari; $d++): 
                                $val = isset($data_db[$key][$d]) ? $data_db[$key][$d] : '';
                                if($val !== '') { $total_input += (int)$val; $hari_aktif++; }
                                $tgl_full = "$tahun-$bulan-".str_pad($d, 2, "0", STR_PAD_LEFT);
                            ?>
                                <td class="p-1 text-center border-end border-light">
                                    <?php if ($key === 'shalat_5_waktu'): ?>
                                        <div class="d-flex justify-content-center align-items-center gap-1 px-1" style="min-width: 95px;">
                                            <?php 
                                            $sh_label = ['S','D','A','M','I'];
                                            $sh_full_name = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
                                            for($s=0; $s<5; $s++):
                                                $isChecked = ($val !== '' && $val > $s) ? 'checked' : '';
                                            ?>
                                                <label class="shalat-badge-container" title="<?= $sh_full_name[$s] ?>">
                                                    <input type="checkbox" class="shalat-mini-check d-none" 
                                                           data-tgl="<?= $tgl_full ?>" data-kolom="<?= $key ?>" 
                                                           data-tipe="<?= $attr['tipe'] ?>" data-target="<?= $attr['target'] ?>"
                                                           <?= $isChecked ?>>
                                                    <span class="shalat-char-badge"><?= $sh_label[$s] ?></span>
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input amalan-check" 
                                                   data-tgl="<?= $tgl_full ?>" data-kolom="<?= $key ?>" 
                                                   data-tipe="<?= $attr['tipe'] ?>" data-target="<?= $attr['target'] ?>"
                                                   value="1" <?= ($val == 1 ? 'checked' : '') ?>>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                            
                            <td class="text-center fw-bold bg-light" style="min-width: 80px;">
                                <span class="percent-badge" id="pct-<?= $key ?>">
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

        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold text-dark m-0">Monitoring Spiritual Awardee</h3>
                    <p class="text-muted m-0 small">Rekapitulasi berkala mutaba'ah yaumi ibadah harian mahasiswa asrama.</p>
                </div>
                <div>
                    <span class="badge bg-success rounded-pill px-3 py-2 text-uppercase fw-bold" style="font-size: 0.72rem;">
                        Akses: <?= $role_user ?>
                    </span>
                </div>
            </div>

            <div class="card card-custom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <h6 class="fw-bold text-dark m-0"><i class="fas fa-clipboard-list me-2 text-success"></i>Daftar Jemaah Jurnal Amalan</h6>
                    <div>
                        <input type="text" id="searchAwardee" class="form-control form-control-sm shadow-sm" placeholder="🔍 Cari nama awardee..." style="width: 240px; border-radius: 8px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0" id="tabelAmalan">
                        <thead>
                            <tr class="text-uppercase text-white" style="font-size: 0.75rem; background-color: var(--navy-theme);">
                                <th style="padding: 12px 10px;">No</th>
                                <th>Nama Lengkap</th>
                                <th>Kampus / Universitas</th>
                                <th class="text-center">Angkatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query_awardee = mysqli_query($conn, "SELECT id, nama, universitas, angkatan FROM users WHERE role = 'awardee' ORDER BY nama ASC");
                            $no = 1;
                            if (mysqli_num_rows($query_awardee) > 0):
                                while ($row = mysqli_fetch_assoc($query_awardee)):
                            ?>
                            <tr>
                                <td><span class="text-secondary fw-bold"><?= $no++ ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                    <small class="text-muted">ID: #<?= $row['id'] ?></small>
                                </td>
                                <td><span class="fw-semibold text-secondary"><?= htmlspecialchars($row['universitas'] ?: '-') ?></span></td>
                                <td class="text-center"><span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-bold"><?= htmlspecialchars($row['angkatan'] ?: '-') ?></span></td>
                                <td class="text-center">
                                    <a href="detail_amalan.php?id_awardee=<?= $row['id'] ?>" class="btn btn-success btn-sm rounded-pill fw-bold px-3">
                                        <i class="fas fa-eye me-1"></i> Lihat Rekap
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data Awardee.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <div id="notif" class="alert alert-success alert-saved shadow-lg py-2 px-4 border-0 text-white bg-success">
            <i class="fas fa-check-circle me-2"></i> Data amalan berhasil diperbarui!
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    <?php if ($role_user == 'awardee'): ?>
    // Hitung grand total awal pas halaman dibuka pertama kali
    hitungGrandTotalAwal();

    // 1. UPDATE AMALAN YAUMIYAH BIASA VIA AJAX
    $('.amalan-check').on('change', function() {
        const input = $(this);
        const tgl = input.data('tgl');
        const kolom = input.data('kolom');
        const nilai = input.is(':checked') ? 1 : 0;
        kirimData(tgl, kolom, nilai);
    });

    // 2. UPDATE SHALAT 5 WAKTU HORIZONTAL VIA AJAX
    $('.shalat-mini-check').on('change', function() {
        const input = $(this);
        const tgl = input.data('tgl');
        const kolom = input.data('kolom');
        const group = input.closest('.d-flex').find('.shalat-mini-check');
        
        let totalCeklis = 0;
        group.each(function() { if($(this).is(':checked')) totalCeklis++; });
        group.each(function(index) {
            if (index < totalCeklis) { $(this).prop('checked', true); } 
            else { $(this).prop('checked', false); }
        });

        kirimData(tgl, kolom, totalCeklis);
    });

    function kirimData(tgl, kolom, nilai) {
        $.ajax({
            url: 'proses_amalan.php',
            type: 'POST',
            data: { tanggal: tgl, kolom: kolom, nilai: nilai },
            success: function() {
                $('#notif').fadeIn().delay(800).fadeOut();
                updatePercentage(kolom);
            }
        });
    }

    function updatePercentage(kolom) {
        let total = 0; let count = 0; let target = 0; let tipe = '';
        if(kolom === 'shalat_5_waktu') {
            $('.shalat-mini-check').each(function() {
                target = parseInt($(this).data('target'));
                tipe = $(this).data('tipe');
            });
            $('.table-amalan tbody tr:first-child .d-flex').each(function() {
                count++;
                let subTotal = 0;
                $(this).find('.shalat-mini-check').each(function() { if($(this).is(':checked')) subTotal++; });
                total += subTotal;
            });
        } else {
            $(`.amalan-check[data-kolom="${kolom}"]`).each(function() {
                count++;
                if($(this).is(':checked')) total += 1;
                target = parseInt($(this).data('target'));
                tipe = $(this).data('tipe');
            });
        }
        if(count > 0) {
            let pct = (tipe === 'harian') ? (total / count) / target * 100 : (total / target) * 100;
            $(`#pct-${kolom}`).text(Math.min(pct, 100).toFixed(1) + '%');
        }
        recalcGrandTotal();
    }

    function recalcGrandTotal() {
        let sumAllpct = 0;
        $('.percent-badge').each(function() { sumAllpct += parseFloat($(this).text()) || 0; });
        let grandTotal = sumAllpct / 8;
        $('#grand-total-pct').text(grandTotal.toFixed(1) + '%');
    }

    function hitungGrandTotalAwal() { recalcGrandTotal(); }
    <?php else: ?>
    // FILTER LIVE SEARCH SISI KEPALA ASRAMA
    document.getElementById('searchAwardee').addEventListener('keyup', function(){
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll('#tabelAmalan tbody tr');
        rows.forEach(row => {
            let nama = row.cells[1] ? row.cells[1].innerText : '';
            if(nama.toUpperCase().indexOf(filter) > -1) { row.style.style.display = ""; } 
            else { row.style.display = "none"; }
        });
    });
    <?php endif; ?>
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>