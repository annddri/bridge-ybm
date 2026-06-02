<?php
session_start();
// Naik satu level ke direktori utama untuk memanggil koneksi database
include '../config/koneksi.php';

// PERBAIKAN PROTEKSI: Hanya menendang user yang belum login sama sekali.
if (!isset($_SESSION['id_user'])) { 
    header("Location: ../login.php"); 
    exit; 
}

$id_user = $_SESSION['id_user'];
$role_user = $_SESSION['role'];

// Ambil data user login untuk sidebar
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id_user'");
$u = mysqli_fetch_assoc($query_user);
$foto_path = "../assets/img/" . ($u['foto_profil'] ?: 'default.png');

// Filter Bulan & Tahun Berjalan untuk Ranking
$bulan = isset($_GET['bulan']) ? (int)$_GET['bulan'] : (int)date('m');
$tahun = isset($_GET['tahun']) ? (int)$_GET['tahun'] : (int)date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Definisi target amalan untuk perhitungan skor
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

// 1. PROSES HITUNG RANKING AWARDEE
$rank_list = [];
$query_awardee = mysqli_query($conn, "SELECT id, nama, universitas, angkatan FROM users WHERE role = 'awardee'");

while ($aw = mysqli_fetch_assoc($query_awardee)) {
    $id_aw = $aw['id'];
    
    // Ambil semua track record amalan awardee ini di bulan & tahun terpilih
    $res = mysqli_query($conn, "SELECT * FROM amalan_yaumiyah WHERE id_user='$id_aw' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
    
    $total_input_amalan = [];
    $hari_aktif_amalan = [];
    foreach($list_amalan as $key => $v) { 
        $total_input_amalan[$key] = 0; 
        $hari_aktif_amalan[$key] = 0; 
    }

    while($row = mysqli_fetch_assoc($res)) {
        foreach($list_amalan as $key => $attr) {
            if (isset($row[$key]) && $row[$key] !== '') {
                $total_input_amalan[$key] += (int)$row[$key];
                $hari_aktif_amalan[$key]++;
            }
        }
    }

    // Hitung rata-rata persentase total seluruh amalan
    $sum_pct = 0;
    foreach($list_amalan as $key => $attr) {
        if ($hari_aktif_amalan[$key] > 0) {
            $pct = ($attr['tipe'] == 'harian') 
                ? ($total_input_amalan[$key] / $hari_aktif_amalan[$key]) / $attr['target'] * 100 
                : ($total_input_amalan[$key] / $attr['target']) * 100;
            $sum_pct += min($pct, 100);
        }
    }
    
    // Rata-rata skor akhir (dari 8 amalan)
    $skor_akhir = ($sum_pct > 0) ? ($sum_pct / 8) : 0;

    $rank_list[] = [
        'id' => $aw['id'],
        'nama' => $aw['nama'],
        'universitas' => $aw['universitas'],
        'angkatan' => $aw['angkatan'],
        'skor' => round($skor_akhir, 1)
    ];
}

// Urutkan array berdasarkan skor tertinggi ke terendah
usort($rank_list, function($a, $b) {
    return $b['skor'] <=> $a['skor'];
});

$accent_color = '#198754'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard Spiritual - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --sidebar-bg: #063255; 
            --navy-dark: #041f35;
            --accent-color: <?= $accent_color ?>;
            --bg-light: #f8fafc;
            --navy-theme: #063255;
        }
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.9rem; }
        
        /* SIDEBAR SINKRON 100% SAMA */
        .sidebar { 
            width: 280px; 
            height: 100vh; 
            position: fixed; top: 0; left: 0; 
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--navy-dark) 100%); 
            color: #fff; 
            z-index: 1000; 
            overflow-y: auto; 
        }
        .sidebar-brand { text-align: center; padding: 30px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .brand-logo { width: 75px; height: 75px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.2); margin-bottom: 12px; object-fit: cover; }
        
        .nav-link { 
            color: rgba(255, 255, 255, 0.85); 
            padding: 13px 25px; 
            display: flex; 
            align-items: center; 
            text-decoration: none; 
            transition: all 0.2s ease; 
            font-size: 0.95rem;          
            font-weight: 500;            
        }
        .nav-link i { width: 28px; margin-right: 12px; font-size: 1.1rem; }
        .nav-link:hover, .nav-link.active { 
            color: #fff; 
            background: rgba(255, 255, 255, 0.1); 
            border-left: 4px solid #0d6efd; 
        }
        .sidebar-heading {
            padding: 20px 25px 8px 25px;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
        }

        .main-content { margin-left: 280px; padding: 40px; }
        .card-custom { border: 1px solid rgba(6, 50, 85, 0.08); border-radius: 20px; background: #ffffff; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important; padding: 30px; }
        
        .rank-1 { background-color: #ffd700 !important; color: #000 !important; }
        .rank-2 { background-color: #c0c0c0 !important; color: #000 !important; }
        .rank-3 { background-color: #cd7f32 !important; color: #fff !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand">
        <img src="<?= $foto_path ?>?t=<?= time() ?>" class="brand-logo shadow" onerror="this.src='https://cdn-icons-png.flaticon.com/512/149/149071.png'">
        <h5 class="fw-bold mb-1 text-white" style="font-size: 1.1rem;"><?= htmlspecialchars($u['nama']) ?></h5>
        <small class="text-info fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Kepala Asrama</small>
    </div>
    
    <div class="mt-3">
        <a href="dashboard_kepas.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="profile_kepas.php" class="nav-link"><i class="fas fa-user-circle"></i> Profil Saya</a>
        <a href="data_awardee.php" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        <a href="monthly_report.php" class="nav-link"><i class="fas fa-file-alt"></i> Laporan Bulanan</a>
        <a href="daily_report.php" class="nav-link"><i class="fas fa-calendar-day"></i> Laporan Harian</a>
        
        <div class="sidebar-heading">Fitur Monitoring</div>
        <a href="monitoring_amalan.php" class="nav-link active"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="#" class="nav-link"><i class="fas fa-book-quran"></i> Tahfidz Tracker</a>
        <a href="#" class="nav-link"><i class="fas fa-graduation-cap"></i> Akademik</a>
        <a href="#" class="nav-link"><i class="fas fa-award"></i> Portofolio</a>
        <a href="#" class="nav-link"><i class="fas fa-people-group"></i> Sosial Masyarakat</a>
        
        <a href="../logout.php" class="nav-link text-danger mt-4" style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px;" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark m-0">Leaderboard Spiritual Awardee</h3>
                <p class="text-muted m-0 small">Peringkat mutaba'ah harian berdasarkan akumulasi total amalan bulan berjalan.</p>
            </div>
            
            <form class="d-flex gap-2" method="GET" action="">
                <select name="bulan" class="form-select form-select-sm rounded-pill shadow-sm px-3" onchange="this.form.submit()">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($m==$bulan?'selected':'') ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                    <?php endfor; ?>
                </select>
                <select name="tahun" class="form-select form-select-sm rounded-pill shadow-sm px-3" onchange="this.form.submit()">
                    <?php for($y = date('Y')-2; $y <= date('Y')+2; $y++): ?>
                        <option value="<?= $y ?>" <?= ($y == $tahun ? 'selected' : '') ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>

        <div class="card card-custom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <h6 class="fw-bold text-dark m-0"><i class="fas fa-trophy me-2 text-warning"></i>Peringkat Jurnal Amalan Bulan Ini</h6>
                <div>
                    <input type="text" id="searchAwardee" class="form-control form-control-sm shadow-sm" placeholder="🔍 Cari nama awardee..." style="width: 240px; border-radius: 8px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0" id="tabelAmalan">
                    <thead>
                        <tr class="text-uppercase text-white" style="font-size: 0.75rem; background-color: var(--navy-theme);">
                            <th style="padding: 12px 10px; width: 80px;" class="text-center">Rank</th>
                            <th>Nama Lengkap</th>
                            <th>Kampus / Universitas</th>
                            <th class="text-center">Angkatan</th>
                            <th class="text-center">Total Progress</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if (count($rank_list) > 0):
                            foreach ($rank_list as $row):
                                $rank_badge = "bg-light text-dark";
                                if($no == 1) $rank_badge = "rank-1 fw-bold";
                                if($no == 2) $rank_badge = "rank-2 fw-bold";
                                if($no == 3) $rank_badge = "rank-3 text-white fw-bold";
                        ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge p-2 rounded-circle shadow-sm <?= $rank_badge ?>" style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;">
                                    <?= $no++ ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                <small class="text-muted">ID: #<?= $row['id'] ?></small>
                            </td>
                            <td><span class="fw-semibold text-secondary"><?= htmlspecialchars($row['universitas'] ?: '-') ?></span></td>
                            <td class="text-center"><span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-bold"><?= htmlspecialchars($row['angkatan'] ?: '-') ?></span></td>
                            <td class="text-center">
                                <div class="fw-bold text-success" style="font-size: 1rem;"><?= $row['skor'] ?>%</div>
                                <div class="progress mt-1" style="height: 5px; width: 100px; margin: 0 auto;">
                                    <div class="progress-bar bg-success" style="width: <?= $row['skor'] ?>%"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="detail_amalan.php?id_awardee=<?= $row['id'] ?>&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn btn-success btn-sm rounded-pill fw-bold px-3">
                                    <i class="fas fa-chart-pie me-1"></i> Lihat Rekap
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data Awardee.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
<script>
$(document).ready(function() {
    // Live Search nama awardee
    document.getElementById('searchAwardee').addEventListener('keyup', function(){
        let filter = this.value.toUpperCase();
        let rows = document.querySelectorAll('#tabelAmalan tbody tr');
        rows.forEach(row => {
            let nama = row.cells[1] ? row.cells[1].innerText : '';
            if(nama.toUpperCase().indexOf(filter) > -1) { 
                row.style.display = ""; 
            } else { 
                row.style.display = "none"; 
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>