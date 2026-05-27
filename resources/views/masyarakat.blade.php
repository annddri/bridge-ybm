<x-header title="Sosial Masyarakat - Bridge" css="css/masyarakat.css"></x-header>

<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>

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
                    <input type="date" name="waktu" class="form-control form-control-sm rounded-3" required>
                </div>

                <div class="col-md-3">
                    <label id="label_nama" class="form-label small fw-bold text-muted">Daftar Kunjungan</label>
                    <input type="text" name="kunjungan_sospro_materi" class="form-control form-control-sm rounded-3" required>
                </div>

                <div class="col-md-2">
                    <label id="label_lokasi" class="form-label small fw-bold text-muted">Lokasi</label>
                    <input type="text" name="lokasi_sasaran_peserta" class="form-control form-control-sm rounded-3">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Laporan/Dokumentasi</label>
                    <input type="url" name="link_laporan" class="form-control form-control-sm rounded-3" placeholder="https://...">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-custom-ybm btn-sm rounded-pill px-4 fw-bold py-2 shadow-sm">
                        Simpan Keaktifan Masyarakat
                    </button>
                </div>
            </form>
        </div>

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

                                        <th width="12%" class="px-3">
                                            Waktu
                                        </th>

                                        <th width="23%">
                                            @if ($kategori === 'kunjungan')
                                                Daftar Kunjungan
                                            @elseif ($kategori === 'social_project')
                                                Nama Sosial Proyek
                                            @else
                                                Judul Materi
                                            @endif
                                        </th>

                                        <th width="25%">
                                            @if ($kategori === 'kunjungan')
                                                Lokasi
                                            @elseif ($kategori === 'social_project')
                                                Sasaran
                                            @else
                                                Jumlah Peserta
                                            @endif
                                        </th>

                                        <th width="10%" class="text-center">Laporan/Dokumentasi</th>
                                        <th width="10%" class="text-center">Aksi</th>

                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>

                                            <td class="small text-muted px-3">
                                                {{ $row->waktu }}
                                            </td>

                                            <td class="fw-bold text-dark">
                                                {{ $row->kunjungan_sospro_materi }}
                                            </td>

                                            <td>
                                                {{ $row->lokasi_sasaran_peserta }}
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
                                                <form action="{{ route('masyarakat.destroy', $row->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus data sosial masyarakat ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4 small">
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
        lblLokasi.innerText = "Jumlah Peserta";
    }
}
</script>

<x-footer></x-footer>