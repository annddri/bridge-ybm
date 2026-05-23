<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['id_user'])) { header("Location: login.php"); exit; }

$id_logon = $_SESSION['id_user'];
$role_logon = $_SESSION['role'];
$pesan = "";

// PROSES SIMPAN RESUME
if (isset($_POST['simpan_resume'])) {
    $tgl = $_POST['tanggal'];
    $tema = mysqli_real_escape_string($conn, $_POST['tema']);
    $narsum = mysqli_real_escape_string($conn, $_POST['narasumber']);
    $link = mysqli_real_escape_string($conn, $_POST['link_external']);
    $file_db = "NULL";

    if (!empty($_FILES['file_resume']['name'])) {
        $nama_f = "resume_".time().".".pathinfo($_FILES['file_resume']['name'], PATHINFO_EXTENSION);
        if(move_uploaded_file($_FILES['file_resume']['tmp_name'], "uploads/".$nama_f)) {
            $file_db = "'$nama_f'";
        }
    }

    $query = "INSERT INTO resume_pembinaan (id_user, tanggal, tema, narasumber, file_resume, link_external) 
              VALUES ('$id_logon', '$tgl', '$tema', '$narsum', $file_db, '$link')";
    
    if(mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success shadow-sm'>Resume '$tema' berhasil disimpan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resume Pembinaan - BRIDGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">Resume Pembinaan S/H Skills</h4>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>

    <?php echo $pesan; ?>

    <div class="row">
        <?php if ($role_logon == 'awardee') : ?>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4">
                <h6 class="fw-bold mb-3">Tambah Aktivitas</h6>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Tema Pembinaan</label>
                        <input type="text" name="tema" class="form-control" placeholder="Contoh: Public Speaking" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Narasumber</label>
                        <input type="text" name="narasumber" class="form-control" placeholder="Nama Ust/Pemateri" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Link Resume (Drive/Doc)</label>
                        <input type="url" name="link_external" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Atau Upload File</label>
                        <input type="file" name="file_resume" class="form-control">
                    </div>
                    <button type="submit" name="simpan_resume" class="btn btn-primary w-100 fw-bold shadow-sm">Simpan Resume</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?php echo ($role_logon == 'awardee') ? 'col-md-8' : 'col-md-12'; ?>">
            <div class="card border-0 shadow-sm p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr class="small text-muted text-uppercase">
                                <th>Tgl/Bln/Th</th>
                                <th>Tema Pembinaan</th>
                                <th>Narasumber</th>
                                <th>Link Resume</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php
                            $sql = ($role_logon == 'awardee') 
                                   ? "SELECT * FROM resume_pembinaan WHERE id_user = '$id_logon' ORDER BY tanggal DESC"
                                   : "SELECT resume_pembinaan.*, users.nama FROM resume_pembinaan JOIN users ON resume_pembinaan.id_user = users.id ORDER BY tanggal DESC";
                            
                            $q = mysqli_query($conn, $sql);
                            while($r = mysqli_fetch_assoc($q)) :
                            ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                                <td class="fw-bold"><?php echo $r['tema']; ?></td>
                                <td><?php echo $r['narasumber']; ?></td>
                                <td>
                                    <?php if(!empty($r['link_external'])): ?>
                                        <a href="<?php echo $r['link_external']; ?>" target="_blank" class="text-decoration-none me-2">🌐 Link</a>
                                    <?php endif; ?>
                                    
                                    <?php if($r['file_resume'] != 'NULL' && !empty($r['file_resume'])): ?>
                                        <a href="uploads/<?php echo str_replace("'","",$r['file_resume']); ?>" target="_blank" class="text-decoration-none text-danger">📄 File</a>
                                    <?php endif; ?>

                                    <?php if(empty($r['link_external']) && ($r['file_resume'] == 'NULL' || empty($r['file_resume']))): ?>
                                        <span class="text-muted small">Tidak ada link</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>