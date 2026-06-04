<x-header title="Akademik - Bridge" css="css/akademik.css"></x-header>

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
                    <i class="fas fa-graduation-cap fs-3 text-primary me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">ACADEMIC PERFORMANCE</h4>
                        <p class="m-0 header-subtitle">
                            Monitoring Nilai IP Semester, IPK Kumulatif, dan Riwayat Capaian TOEFL
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

        <div class="row g-4">
            <div class="col-xl-4 col-lg-5">
                <div class="card live-card border-0 p-4 rounded-4 mb-4">
                    <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">Input IP Semester</h6>
                    <hr class="text-muted opacity-25 my-3">

                    <form action="{{ route('akademik.ip.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label text-muted small mb-1 fw-semibold">Semester</label>
                                <input type="number" name="semester" class="form-control form-control-sm rounded-3" min="1" max="8" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label text-muted small mb-1 fw-semibold">IP Semester</label>
                                <input type="number" step="0.01" name="ip" class="form-control form-control-sm rounded-3" min="0" max="4" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1 fw-semibold">Berkas KHS</label>
                            <input type="file" name="bukti_khs" class="form-control form-control-sm rounded-3" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2" style="background-color: var(--navy-theme); border-color: var(--navy-theme);">
                            Simpan IP
                        </button>
                    </form>
                </div>

                <div class="card live-card border-0 p-4 rounded-4">
                    <h6 class="fw-bold mb-0" style="color: #198754;">Input Riwayat TOEFL</h6>
                    <hr class="text-muted opacity-25 my-3">

                    <form action="{{ route('akademik.toefl.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Skor TOEFL</label>
                                <input type="number" name="score" class="form-control form-control-sm rounded-3" required>
                            </div>

                            <div class="col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Jenis Tes</label>
                                <select name="jenis_tes" class="form-select form-select-sm rounded-3" required>
                                    <option value="Pre-Test">Pre-Test</option>
                                    <option value="Post-Test">Post-Test</option>
                                    <option value="Real Test">Real Test</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1 fw-semibold">Berkas Sertifikat</label>
                            <input type="file" name="bukti_toefl" class="form-control form-control-sm rounded-3" accept=".pdf,.jpg,.jpeg,.png" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-semibold py-2">
                            Tambah Riwayat TOEFL
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="card live-card border-0 p-4 rounded-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold m-0" style="color: var(--navy-theme);">Riwayat IP Semester</h6>

                        <span class="badge bg-primary px-3 py-2 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                            IPK Kumulatif: {{ $ipk_sekarang }}
                        </span>
                    </div>

                    <hr class="text-muted opacity-25 my-3">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <th class="border-0 py-3 {{ $role_user === 'mahasiswa' ? 'px-3' : '' }}">Semester</th>
                                    <th class="border-0 py-3 text-center">IP Semester</th>
                                    <th class="border-0 py-3 text-center">Bukti KHS</th>
                                    <th class="border-0 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($data_akademik as $r)
                                    <tr>
                                        <td class="px-3">
                                            <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold">
                                                Semester {{ $r->semester }}
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold text-primary">
                                            {{ number_format($r->ip, 2) }}
                                        </td>

                                        <td class="text-center">
                                            @if ($r->file_verifikasi)
                                                <a href="{{ asset('uploads/akademik/' . $r->file_verifikasi) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.75rem;">
                                                    <i class="fas fa-eye me-1"></i> File
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <form action="{{ route('akademik.ip.destroy', $r->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="confirmDelete(event, this, 'Yakin ingin menghapus data IP ini?')">
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
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada data IP terdata.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card live-card border-0 p-4 rounded-4">
                    <h6 class="fw-bold mb-0" style="color: #198754;">Riwayat Skor TOEFL</h6>
                    <hr class="text-muted opacity-25 my-3">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead>
                                <tr>
                                    <th class="border-0 py-3 px-3">Jenis Tes</th>
                                    <th class="border-0 py-3 text-center">Skor</th>
                                    <th class="border-0 py-3 text-center">Sertifikat</th>
                                    <th class="border-0 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($data_toefl as $rt)
                                    <tr>

                                        <td class="px-3">
                                            <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold">
                                                {{ $rt->jenis_tes }}
                                            </span>
                                        </td>

                                        <td class="text-center fw-bold text-success">{{ $rt->score }}</td>

                                        <td class="text-center">
                                            @if ($rt->file_sertifikat)
                                                <a href="{{ asset('uploads/toefl/' . $rt->file_sertifikat) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.75rem;">
                                                    <i class="fas fa-eye me-1"></i> File
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('akademik.toefl.destroy', $rt->id) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="confirmDelete(event, this, 'Yakin ingin menghapus data TOEFL ini?')">
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
                                        <td colspan="4" class="text-center text-muted py-4">
                                            Belum ada data skor TOEFL terdata.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-footer></x-footer>