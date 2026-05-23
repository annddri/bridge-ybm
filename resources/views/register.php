<?php
session_start();
include 'config/koneksi.php';

$pesan = "";

if (isset($_POST['register'])) {
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = $_POST['password'];
    $universitas = mysqli_real_escape_string($conn, $_POST['universitas']);
    $angkatan    = !empty($_POST['angkatan']) ? (int)$_POST['angkatan'] : NULL;
    $ro          = mysqli_real_escape_string($conn, $_POST['ro']);
    
    // Tangkap pilihan pendaftaran & kode rahasia
    $daftar_sebagai = $_POST['daftar_sebagai']; 
    $kode_admin     = $_POST['kode_admin'];

    // 1. Tentukan Role Berdasarkan Validasi Kode Rahasia
    $role_final = 'awardee'; // Default awal sebagai awardee
    $id_asrama_final = NULL;

    if ($daftar_sebagai != 'awardee') {
        // Cek apakah kode rahasia admin cocok
        if ($kode_admin === 'BRIGHT_BANDUNG_2026') {
            $role_final = $daftar_sebagai; // Bisa jadi 'fasilitator' atau 'kepala asrama'
            
            // Jika dia kepala asrama, kita plot otomatis asramanya berdasarkan pilihan kampus
            if ($role_final == 'kepala asrama') {
                $id_asrama_final = "Asrama_" . $universitas; 
            }
        } else {
            $pesan = "<div class='alert alert-danger text-center py-2 fs-7 rounded-3' role='alert'>Kode Otentikasi Admin Salah! Gagal mendaftar.</div>";
        }
    }

    // Jika tidak ada error kode admin, lanjutkan proses simpan
    if (empty($pesan)) {
        // 2. Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        
        if (mysqli_num_rows($cek_email) > 0) {
            $pesan = "<div class='alert alert-danger text-center py-2 fs-7 rounded-3' role='alert'>Email sudah terdaftar!</div>";
        } else {
            // 3. Hash password
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // 4. Masukkan data ke database dengan role_final hasil filter di atas
            $query_registrasi = "INSERT INTO users (nama, email, password, role, ro, universitas, angkatan, id_asrama, foto_profil) 
                                 VALUES ('$nama', '$email', '$password_hashed', '$role_final', '$ro', '$universitas', '$angkatan', '$id_asrama_final', 'default.png')";
            
            if (mysqli_query($conn, $query_registrasi)) {
                $pesan = "<div class='alert alert-success text-center py-2 fs-7 rounded-3' role='alert'>Registrasi Akun <strong>$role_final</strong> Berhasil! Silakan <a href='login.php' class='fw-bold text-decoration-none'>Login</a></div>";
            } else {
                $pesan = "<div class='alert alert-danger text-center py-2 fs-7 rounded-3' role='alert'>Gagal mendaftar, terjadi kesalahan sistem database.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Bright Scholarship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #063255 0%, #0a426e 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 30px 0;
        }
        .register-card { 
            max-width: 480px; 
            width: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(240, 244, 248, 0.85) 100%);
            backdrop-filter: blur(10px);
            border-radius: 24px; 
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        .form-control, .form-select {
            border-radius: 12px; padding: 10px 15px; border: 1px solid #dee2e6;
            background-color: rgba(248, 249, 250, 0.8); font-size: 0.85rem;
        }
        .btn-custom-primary {
            background-color: #0d6efd; border: none; border-radius: 12px; padding: 12px; transition: all 0.2s ease;
        }
        .btn-custom-primary:hover { background-color: #0b5ed7; transform: translateY(-1px); box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3); }
        .fs-7 { font-size: 0.85rem; }
        .hidden-field { display: none; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center p-3">
    <div class="card register-card p-4 p-md-5 shadow-lg">
        
        <div class="text-center mb-3">
            <img src="assets/img/New Logo YBM Secondary.png" alt="YBM BRILiaN Logo" class="img-fluid mb-2" style="max-height: 48px; object-fit: contain;">
            <h4 class="fw-bold m-0" style="color: #063255;">Pendaftaran Portal</h4>
            <p class="text-secondary small m-0">Registrasi Akun Awardee & Staff Bright Scholarship</p>
        </div>
        
        <?php echo $pesan; ?>

        <form action="" method="POST">
            <div class="mb-2">
                <label class="form-label fw-semibold text-secondary fs-7 mb-1">Daftar Sebagai</label>
                <select name="daftar_sebagai" id="daftar_sebagai" class="form-select" onchange="toggleRoleForm()" required>
                    <option value="awardee">Awardee (Mahasiswa Penerima Beasiswa)</option>
                    <option value="fasilitator">Fasilitator Pendidikan (Admin Kampus)</option>
                    <option value="kepala asrama">Kepala Asrama (Pengurus Dormitory)</option>
                </select>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold text-secondary fs-7 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="mb-2">
                <label class="form-label fw-semibold text-secondary fs-7 mb-1">Email Aktif</label>
                <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
            </div>
            
            <div class="mb-2">
                <label class="form-label fw-semibold text-secondary fs-7 mb-1">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Buat kata sandi" required>
            </div>

            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label fw-semibold text-secondary fs-7 mb-1">Regional (RO)</label>
                    <select name="ro" class="form-select" required>
                        <option value="Bandung">Bandung</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                    </select>
                </div>
                <div class="col-6" id="box_angkatan">
                    <label class="form-label fw-semibold text-secondary fs-7 mb-1">Angkatan (Batch)</label>
                    <input type="number" name="angkatan" id="input_angkatan" class="form-control" placeholder="10" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary fs-7 mb-1" id="label_kampus">Kampus Mitra Universitas</label>
                <select name="universitas" class="form-select" required>
                    <option value="UPI">Universitas Pendidikan Indonesia (UPI)</option>
                    <option value="ITB">Institut Teknologi Bandung (ITB)</option>
                    <option value="UNPAD">Universitas Padjadjaran (UNPAD)</option>
                </select>
            </div>

            <div class="mb-4 hidden-field" id="box_kode_admin">
                <label class="form-label fw-bold text-danger fs-7 mb-1">🔑 Masukkan Kode Validasi Staff Admin</label>
                <input type="password" name="kode_admin" class="form-control border-danger" placeholder="Hanya untuk Staff Internal RO">
            </div>
            
            <button type="submit" name="register" class="btn btn-custom-primary w-100 fw-bold text-white shadow-sm">Daftar Sekarang</button>
        </form>
        
        <p class="text-center mt-4 mb-0 text-secondary fs-7">
            Sudah memiliki akun? <a href="login.php" class="text-decoration-none fw-bold" style="color: #0d6efd;">Masuk di sini</a>
        </p>
    </div>
</div>

<script>
function toggleRoleForm() {
    let role = document.getElementById('daftar_sebagai').value;
    let boxKodeAdmin = document.getElementById('box_kode_admin');
    let boxAngkatan = document.getElementById('box_angkatan');
    let inputAngkatan = document.getElementById('input_angkatan');
    let labelKampus = document.getElementById('label_kampus');

    if (role === 'awardee') {
        boxKodeAdmin.style.display = 'none';
        boxAngkatan.style.display = 'block';
        inputAngkatan.required = true;
        labelKampus.innerText = "Kampus Mitra Universitas";
    } else if (role === 'fasilitator') {
        boxKodeAdmin.style.display = 'block';
        boxAngkatan.style.display = 'block'; // Fasilitator terikat batch kampus
        inputAngkatan.required = true;
        labelKampus.innerText = "Wilayah Penugasan Kampus";
    } else if (role === 'kepala asrama') {
        boxKodeAdmin.style.display = 'block';
        boxAngkatan.style.display = 'none'; // Kepala asrama tidak terikat batch angkatan
        inputAngkatan.required = false;
        labelKampus.innerText = "Lokasi Gedung Asrama Kampus";
    }
}
</script>
</body>
</html>