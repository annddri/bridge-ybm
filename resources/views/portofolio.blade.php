<x-header title="Portofolio - Bridge" css="css/portofolio.css"></x-header>

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
                    <i class="fas fa-award fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">PORTOFOLIO KEAKTIFAN</h4>
                        <p class="m-0 header-subtitle">
                            Kelola riwayat organisasi, prestasi, dan seminar mahasiswa.
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
            <h6 class="fw-bold mb-0 text-primary">
                <i class="fas fa-plus-circle me-1"></i> Tambah Data Baru
            </h6>

            <hr class="text-muted opacity-25 my-3">

            <form action="{{ route('portofolio.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Kategori</label>
                    <select name="kategori" class="form-select form-select-sm rounded-3" required>
                        <option value="prestasi">🏆 Prestasi</option>
                        <option value="organisasi">👥 Organisasi</option>
                        <option value="workshop/seminar">📚 Workshop/Seminar</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Waktu</label>
                    <input type="date"
                            name="tanggal_tahun"
                            class="form-control form-control-sm rounded-3"
                            required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Nama Kegiatan</label>
                    <input type="text"
                            name="nama_kegiatan"
                            class="form-control form-control-sm rounded-3"
                            placeholder="Nama lomba/organisasi"
                            required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Penyelenggara / Jabatan</label>
                    <input type="text"
                            name="penyelenggara_jabatan"
                            class="form-control form-control-sm rounded-3"
                            placeholder="Posisi kamu atau instansi"
                            required>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Level</label>
                    <select name="level" class="form-select form-select-sm rounded-3" required>
                        <option value="Lokal/Kampus">Lokal/Kampus</option>
                        <option value="Kota/Kabupaten">Kota/Kabupaten</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">File Bukti</label>
                    <input type="file"
                            name="file_bukti"
                            class="form-control form-control-sm rounded-3"
                            accept=".pdf,.jpg,.jpeg,.png"
                            required>
                </div>

                <div class="col-12">
                    <button type="submit"
                            class="btn btn-primary btn-sm rounded-pill px-4 fw-bold py-2 shadow-sm"
                            style="background-color: var(--navy-theme); border-color: var(--navy-theme);">
                        Simpan Data Portofolio
                    </button>
                </div>
            </form>
        </div>

        <div class="card live-card border-0 p-4 rounded-4">
            <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active"
                            id="pills-prestasi-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-prestasi"
                            type="button">
                        🏆 Prestasi
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            id="pills-organisasi-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-organisasi"
                            type="button">
                        👥 Organisasi
                    </button>
                </li>

                <li class="nav-item">
                    <button class="nav-link"
                            id="pills-workshop-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-workshop"
                            type="button">
                        📚 Seminar/Workshop
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">

                @php
                    $kategoriList = [
                        'prestasi' => 'Daftar Riwayat Prestasi',
                        'organisasi' => 'Daftar Riwayat Organisasi',
                        'workshop/seminar' => 'Daftar Riwayat Workshop/Seminar',
                    ];
                @endphp

                @foreach ($kategoriList as $kategori => $judul)
                    @php
                        $tabId = $kategori === 'workshop/seminar' ? 'workshop' : $kategori;
                        $rows = $data_portofolio->where('kategori', $kategori);
                    @endphp

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pills-{{ $tabId }}">
                        <h6 class="fw-bold mb-3 text-dark">{{ $judul }}</h6>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr class="small text-muted text-uppercase">

                                        <th width="12%" class="{{ $role_user === 'mahasiswa' ? 'px-3' : '' }}">Waktu</th>
                                        <th width="25%">Nama Kegiatan/Organisasi</th>
                                        <th width="20%">
                                            {{ $kategori === 'organisasi' ? 'Jabatan' : 'Penyelenggara/Status' }}
                                        </th>
                                        <th width="10%" class="text-center">Level</th>
                                        <th width="10%" class="text-center">Bukti</th>
                                        <th width="10%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($rows as $row)
                                        <tr>

                                            <td class="small text-muted px-3">
                                                {{ $row->tanggal_tahun }}
                                            </td>

                                            <td class="fw-bold text-dark">
                                                {{ $row->nama_kegiatan }}
                                            </td>

                                            <td>
                                                {{ $row->penyelenggara_jabatan }}
                                            </td>

                                            <td class="text-center">
                                                <span class="badge bg-light text-primary border border-primary-subtle rounded-3">
                                                    {{ $row->level }}
                                                </span>
                                            </td>

                                            <td class="text-center">
                                                @if ($row->file_bukti)
                                                    <a href="{{ asset('uploads/portofolio/' . $row->file_bukti) }}"
                                                        target="_blank"
                                                        class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                                        style="font-size: 0.75rem;">
                                                        <i class="fas fa-eye me-1"></i> File
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <form action="{{ route('portofolio.destroy', $row->id) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data portofolio ini?')">
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
                                            <td colspan="6"
                                                class="text-center text-muted py-4 small">
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

<x-footer></x-footer>