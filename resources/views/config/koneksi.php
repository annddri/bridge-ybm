<?php
// 1. Identitas database kamu
$host = "localhost";
$user = "root";          // Default XAMPP adalah root
$pass = "";              // Default XAMPP kosong
$db   = "bridgedatabase"; // Nama database kamu yang benar

// 2. Perintah untuk menyambungkan
$conn = mysqli_connect($host, $user, $pass, $db);

// 3. Cek apakah koneksi berhasil atau gagal
if (!$conn) {
    die("Aduh, koneksinya putus nih: " . mysqli_connect_error());
}

// 4. FUNGSI HITUNG TOTAL PERSENTASE BULANAN (GLOBAL)
function hitungTotalPersentaseBulanan($conn, $id_user, $bulan, $tahun) {
    // Definisikan list amalan beserta targetnya seperti di amalan.php
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

    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    
    // Ambil semua data amalan user di bulan & tahun tersebut
    $data_db = [];
    $res = mysqli_query($conn, "SELECT * FROM amalan_yaumiyah WHERE id_user='$id_user' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
    while($row = mysqli_fetch_assoc($res)){
        $d = (int)date('d', strtotime($row['tanggal']));
        foreach($list_amalan as $key => $val) {
            $data_db[$key][$d] = $row[$key];
        }
    }

    // Hitung persentase per item, lalu kumpulkan untuk dirata-rata
    $total_persen_semua_amalan = 0;
    $jumlah_jenis_amalan = count($list_amalan); // Hasilnya: 8

    foreach($list_amalan as $key => $attr) {
        $total_input_amalan = 0;
        $hari_aktif = 0;

        for($d = 1; $d <= $jumlah_hari; $d++) {
            $val = isset($data_db[$key][$d]) ? $data_db[$key][$d] : '';
            if($val !== '') {
                $total_input_amalan += (int)$val;
                $hari_aktif++;
            }
        }

        if ($hari_aktif > 0) {
            if ($attr['tipe'] == 'harian') {
                $p = ($total_input_amalan / $hari_aktif) / $attr['target'] * 100;
            } else {
                $p = ($total_input_amalan / $attr['target']) * 100;
            }
            $total_persen_semua_amalan += min($p, 100);
        }
    }

    // Hitung rata-rata dari 8 amalan
    $grand_total = $total_persen_semua_amalan / $jumlah_jenis_amalan;
    return round($grand_total, 1);
}
// Akhir dari file koneksi.php
?>