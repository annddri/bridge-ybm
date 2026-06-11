<x-header title="Pembinaan - Bridge" css="css/pembinaan.css"></x-header>

<x-sidebar
    :u="$u"
    :role-user="$role_user"
    :foto-path="$foto_path"
></x-sidebar>

<div class="main-content">
    <div class="content-body">

        {{-- ===== Header ===== --}}
        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-chalkboard-teacher fs-3 me-3" style="color: var(--navy-theme);"></i>
                <div>
                    <h4 class="fw-bold m-0 header-title">PEMBINAAN</h4>
                    <p class="m-0 header-subtitle">
                        Catat kegiatan pembinaan: pemateri, materi, dan upload resume kamu.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== Alert Sukses ===== --}}
        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- ===== Alert Error ===== --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3">
                <strong><i class="fas fa-exclamation-circle me-1"></i> Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== Form Tambah Data ===== --}}
        <div class="card live-card border-0 p-4 rounded-4 mb-4">
            <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                <i class="fas fa-plus-circle me-1"></i> Tambah Data Pembinaan
            </h6>

            <hr class="text-muted opacity-25 my-3">

            <form action="{{ route('pembinaan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 align-items-end">

                    {{-- Tanggal --}}
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Tanggal <span class="text-danger">*</span></label>
                        <input type="date"
                                name="tanggal"
                                id="tanggal"
                                class="form-control form-control-sm rounded-3 @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal') }}"
                                required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama Pemateri --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Nama Pemateri <span class="text-danger">*</span></label>
                        <input type="text"
                               name="nama_pemateri"
                               id="nama_pemateri"
                               class="form-control form-control-sm rounded-3 @error('nama_pemateri') is-invalid @enderror"
                               placeholder="Nama pemateri / narasumber"
                               value="{{ old('nama_pemateri') }}"
                               required>
                        @error('nama_pemateri')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Judul Materi --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Judul Materi <span class="text-danger">*</span></label>
                        <input type="text"
                               name="judul_materi"
                               id="judul_materi"
                               class="form-control form-control-sm rounded-3 @error('judul_materi') is-invalid @enderror"
                               placeholder="Topik / judul materi yang disampaikan"
                               value="{{ old('judul_materi') }}"
                               required>
                        @error('judul_materi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Resume --}}
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Upload Resume</label>
                        <input type="file"
                               name="resume"
                               id="resume"
                               class="form-control form-control-sm rounded-3 @error('resume') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png" required>
                        <div class="text-muted" style="font-size:0.72rem; margin-top:3px;">
                            PDF / JPG / PNG · Maks. 5MB 
                        </div>
                        @error('resume')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="col-md-1">
                        <button type="submit"
                                class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2"
                                style="background-color: var(--navy-theme); border-color: var(--navy-theme);">
                            Simpan
                        </button>
                    </div>

                </div>
            </form>
        </div>

        {{-- ===== Tabel Data Pembinaan ===== --}}
        <div class="card live-card border-0 p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                    <i class="fas fa-list me-1"></i> Riwayat Pembinaan
                </h6>
            </div>

            <hr class="text-muted opacity-25 my-3">

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th width="5%"  class="border-0 py-3 px-3">#</th>
                            <th width="12%" class="border-0 py-3">Tanggal</th>
                            <th width="22%" class="border-0 py-3">Nama Pemateri</th>
                            <th width="33%" class="border-0 py-3">Judul Materi</th>
                            <th width="15%" class="border-0 py-3 text-center">Resume</th>
                            <th width="13%" class="border-0 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data_pembinaan as $index => $row)
                            <tr>
                                <td class="px-3 text-muted small">{{ $index + 1 }}</td>

                                <td class="text-muted small">
                                    {{ date('d M Y', strtotime($row->tanggal)) }}
                                </td>

                                <td class="fw-semibold text-dark">
                                    {{ $row->nama_pemateri }}
                                </td>

                                <td>
                                    <span class="judul-cell d-block" title="{{ $row->judul_materi }}">
                                        {{ $row->judul_materi }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($row->resume)
                                        <a href="{{ asset('uploads/pembinaan/' . $row->resume) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-action">
                                            <i class="fas fa-file-alt me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small">Tidak ada</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <form action="{{ route('pembinaan.destroy', $row->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="confirmDelete(event, this, 'Yakin ingin menghapus data pembinaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3 btn-action">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-chalkboard-teacher fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada data pembinaan tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<x-footer></x-footer>
