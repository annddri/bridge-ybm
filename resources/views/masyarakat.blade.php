<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspek Sosial - BRIGHT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #063255; 
            --sidebar-text: rgba(255, 255, 255, 0.85);
            --sidebar-hover: rgba(255, 255, 255, 0.1);
            --navy-theme: #063255; /* Biru Gelap Utama */
            --accent-color: #0284c7; /* Biru Terang / Sky Blue */
            --bg-light: #f4f7fa; 
        }
        
        body { background-color: var(--bg-light); font-family: 'Segoe UI', sans-serif; font-size: 0.85rem; }
        
        /* Sidebar Styles */
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
        .nav-link.active { color: #fff; background-color: rgba(2, 132, 199, 0.15); border-left: 4px solid var(--accent-color); font-weight: 600; }
        .logout-link { color: #ea4335 !important; font-weight: 600; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 25px; padding-top: 15px !important; }
        .logout-link:hover { background-color: rgba(234, 67, 53, 0.1) !important; }

        .main-content { margin-left: 280px; padding: 0; transition: all 0.3s ease; }
        .content-body { padding: 30px; } 

        .tracker-header-box { background: #ffffff; border: 1px solid rgba(6, 50, 85, 0.07); box-shadow: 0 10px 25px rgba(6, 50, 85, 0.05); }
        .header-title { color: var(--navy-theme); font-size: 1.5rem; letter-spacing: 0.5px; }
        .header-subtitle { color: #64748b; font-size: 0.85rem; }
        
        .live-card { background: #ffffff; border: 1px solid rgba(6, 50, 85, 0.07); box-shadow: 0 12px 30px rgba(6, 50, 85, 0.08); }
        
        /* Custom Tab Nav Pills - Sesuai Gambar Portofolio Keaktifan */
        .nav-pills .nav-link { color: #6c757d; font-weight: 600; padding: 8px 18px; border-radius: 8px; font-size: 0.82rem; transition: all 0.2s ease; }
        .nav-pills .nav-link.active { background-color: var(--navy-theme); color: white; }
        
        /* Tombol Simpan - Menggunakan warna Navy YBM */
        .btn-custom-ybm { background-color: var(--navy-theme); color: #fff; border: none; transition: all 0.2s ease; }
        .btn-custom-ybm:hover { background-color: #04243e; color: #fff; transform: translateY(-1px); }
        
        .table > thead { background-color: #f8fafc; color: var(--navy-theme); font-weight: 700; }

        .badge-status { padding: 5px 10px; border-radius: 30px; font-weight: 600; font-size: 0.72rem; display: inline-block; }
        .status-lulus { background-color: #198754; color: white; }
        .status-pending { background-color: #ffc107; color: #212529; }

        .btn-kembali { background-color: #212529; color: #ffffff !important; font-weight: 600; font-size: 0.85rem; padding: 7px 24px; border-radius: 30px; border: none; text-decoration: none; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); }
        .btn-kembali:hover { background-color: #000000; }

        @media (max-width: 991.98px) { .sidebar { width: 100%; height: auto; position: relative; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="{{ $foto_path }}?t={{ time() }}" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6">{{ $u->name }}</h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;">
                {{ $u->role }}
            </small>
        </div>
    </div>

    <div class="mt-3">
        <a href="/dashboard" class="nav-link"><i class="fas fa-home"></i> Home</a>
        <a href="/profile" class="nav-link"><i class="fas fa-user-circle"></i> Profil Saya</a>

        @if ($role_user !== 'mahasiswa')
            <a href="/data-awardee" class="nav-link"><i class="fas fa-users"></i> Data Awardee</a>
        @endif

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; font-size: 0.75rem;">
            Fitur Monitoring
        </div>

        <a href="/amalan" class="nav-link"><i class="fas fa-pray"></i> Spiritual Tracker</a>
        <a href="/tahfidz" class="nav-link"><i class="fas fa-book-quran"></i> Tahfidz Tracker</a>
        <a href="/akademik" class="nav-link"><i class="fas fa-graduation-cap"></i> Akademik</a>
        <a href="/portofolio" class="nav-link"><i class="fas fa-award"></i> Portofolio</a>
        <a href="/masyarakat" class="nav-link active"><i class="fas fa-people-group"></i> Sosial Masyarakat</a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; font-size: 0.75rem;">
            Fitur Asrama
        </div>

        <a href="/inventaris" class="nav-link"><i class="fas fa-boxes-stacked"></i> Inventaris Asrama</a>
        <a href="/keuangan" class="nav-link"><i class="fas fa-wallet"></i> Keuangan Asrama</a>
        <a href="/perizinan" class="nav-link"><i class="fas fa-envelope-open-text"></i> Perizinan Asrama</a>

        <a href="/logout" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>

<div class="main-content">
    <div class="content-body">

        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-people-group fs-3 me-3" style="color: var(--accent-color);"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">Aspek Sosial Kemasyarakatan</h4>
                        <p class="m-0 header-subtitle">
                            Kelola riwayat kunjungan program, social project, dan narasumber pembinaan.
                        </p>
                    </div>
                </div>

                <div>
                    <a href="/dashboard" class="btn-kembali shadow-sm">Kembali</a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($role_user === 'mahasiswa')
            <div class="card live-card border-0 p-4 rounded-4 mb-4">
                <h6 class="fw-bold mb-0" style="color: var(--accent-color);">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Kegiatan Baru
                </h6>

                <hr class="text-muted opacity-25 my-3">

                <form action="{{ route('masyarakat.store') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Jenis Kegiatan</label>
                        <select name="kategori" id="kat_sosial" class="form-select form-select-sm rounded-3" onchange="updateLabel()" required>
                            <option value="kunjungan">📍 Kunjungan Program</option>
                            <option value="social_project">🌱 Social Project</option>
                            <option value="narasumber">🎤 Narasumber Pembinaan</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Waktu</label>
                        <input type="text" name="waktu" class="form-control form-control-sm rounded-3" required>
                    </div>

                    <div class="col-md-3">
                        <label id="label_nama" class="form-label small fw-bold text-muted">Daftar Kunjungan</label>
                        <input type="text" name="nama_kegiatan_materi" class="form-control form-control-sm rounded-3" required>
                    </div>

                    <div class="col-md-2">
                        <label id="label_lokasi" class="form-label small fw-bold text-muted">Lokasi</label>
                        <input type="text" name="lokasi_sasaran" class="form-control form-control-sm rounded-3">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Link Laporan</label>
                        <input type="url" name="link_laporan" class="form-control form-control-sm rounded-3" placeholder="https://...">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted">Keterangan Tambahan</label>
                        <textarea name="keterangan_tambahan" class="form-control form-control-sm rounded-3" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-custom-ybm btn-sm rounded-pill px-4 fw-bold py-2 shadow-sm">
                            Simpan Keaktifan Masyarakat
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="card live-card border-0 p-4 rounded-4">
            <ul class="nav nav-pills mb-4 justify-content-center" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-kunjungan" type="button">
                        Kunjungan
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-project" type="button">
                        Social Project
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-narasumber" type="button">
                        Narasumber
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                @php
                    $kategoriList = [
                        'kunjungan' => 'Daftar Riwayat Kunjungan Program',
                        'social_project' => 'Daftar Riwayat Social Project',
                        'narasumber' => 'Daftar Riwayat Narasumber Pembinaan',
                    ];
                @endphp

                @foreach ($kategoriList as $kategori => $judul)
                    @php
                        $tabId = $kategori === 'social_project' ? 'project' : $kategori;
                        $rows = $data_masyarakat->where('kategori', $kategori);
                    @endphp

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $tabId }}">
                        <h6 class="fw-bold mb-3 text-dark">{{ $judul }}</h6>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr class="small text-muted text-uppercase">
                                        @if ($role_user !== 'mahasiswa')
                                            <th width="15%" class="px-3">Nama Mahasiswa</th>
                                        @endif

                                        <th width="12%" class="{{ $role_user === 'mahasiswa' ? 'px-3' : '' }}">
                                            {{ $kategori === 'social_project' ? 'Tahun' : 'Waktu' }}
                                        </th>

                                        <th width="23%">
                                            @if ($kategori === 'narasumber')
                                                Judul Materi
                                            @elseif ($kategori === 'social_project')
                                                Nama Project
                                            @else
                                                Lokasi
                                            @endif
                                        </th>

                                        <th width="25%">
                                            @if ($kategori === 'kunjungan')
                                                Daftar Kunjungan
                                            @elseif ($kategori === 'narasumber')
                                                Peserta/Jumlah
                                            @else
                                                Deskripsi
                                            @endif
                                        </th>

                                        <th width="10%" class="text-center">Laporan</th>
                                        <th width="10%" class="text-center">Status</th>

                                        @if ($role_user !== 'mahasiswa')
                                            <th width="10%" class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>
                                            @if ($role_user !== 'mahasiswa')
                                                <td class="px-3 fw-semibold text-secondary">
                                                    {{ $row->name }}
                                                </td>
                                            @endif

                                            <td class="small text-muted {{ $role_user === 'mahasiswa' ? 'px-3' : '' }}">
                                                {{ $row->waktu }}
                                            </td>

                                            <td class="fw-bold text-dark">
                                                @if ($kategori === 'kunjungan')
                                                    {{ $row->lokasi_sasaran }}
                                                @else
                                                    {{ $row->nama_kegiatan_materi }}
                                                @endif
                                            </td>

                                            <td>
                                                @if ($kategori === 'kunjungan')
                                                    {{ $row->nama_kegiatan_materi }}
                                                @else
                                                    {{ $row->keterangan_tambahan }}
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($row->link_laporan)
                                                    <a href="{{ $row->link_laporan }}" target="_blank" class="btn btn-sm btn-link text-decoration-none fw-bold">
                                                        <i class="fas fa-external-link-alt"></i> Lihat
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($row->status === 'Lulus')
                                                    <span class="badge-status status-lulus">
                                                        <i class="fas fa-check-circle me-1"></i> Valid
                                                    </span>
                                                @else
                                                    <span class="badge-status status-pending">
                                                        <i class="fas fa-clock me-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>

                                            @if ($role_user !== 'mahasiswa')
                                                <td class="text-center">
                                                    @if ($row->status === 'Belum Lulus')
                                                        <a href="{{ route('masyarakat.status', [$row->id, 'Lulus']) }}"
                                                           class="btn btn-sm btn-success rounded-pill px-2 py-1 fw-bold text-white"
                                                           onclick="return confirm('Validkan kegiatan sosial ini?')">
                                                            <i class="fas fa-check"></i> Validkan
                                                        </a>
                                                    @else
                                                        <a href="{{ route('masyarakat.status', [$row->id, 'Belum Lulus']) }}"
                                                           class="btn btn-sm btn-danger rounded-pill px-2 py-1 fw-bold text-white"
                                                           onclick="return confirm('Batalkan verifikasi data ini?')">
                                                            <i class="fas fa-times"></i> Batalkan
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $role_user !== 'mahasiswa' ? 7 : 5 }}" class="text-center text-muted py-4 small">
                                                Belum ada data di kategori ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>
</div>

<script>
function updateLabel() {
    let kat = document.getElementById('kat_sosial').value;
    let lblNama = document.getElementById('label_nama');
    let lblLokasi = document.getElementById('label_lokasi');

    if (kat === 'kunjungan') {
        lblNama.innerText = "Daftar Kunjungan";
        lblLokasi.innerText = "Lokasi";
    } else if (kat === 'social_project') {
        lblNama.innerText = "Nama Social Project";
        lblLokasi.innerText = "Sasaran";
    } else {
        lblNama.innerText = "Judul Materi";
        lblLokasi.innerText = "Teknis/Peserta";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>