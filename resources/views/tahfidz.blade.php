<x-header title="Tahfidz Tracker - Bridge" css="css/tahfidz.css"></x-header>

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
                    <i class="fas fa-book-quran fs-3 text-success me-3"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">TAHFIDZ TRACKER</h4>
                        <p class="m-0 header-subtitle">
                            Input Setoran Kuantitas Hafalan Baru (Ziyadah) & Murojaah
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
            <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                Input Setoran Baru
            </h6>

            <hr class="text-muted opacity-25 my-3">

            <form action="{{ route('tahfidz.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-semibold small mb-1">
                            Nama Surah / Materi
                        </label>
                        <input type="text"
                                name="nama_surah"
                                class="form-control form-control-sm rounded-3"
                                placeholder="Contoh: Al-Mulk"
                                required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted fw-semibold small mb-1">
                            Tanggal Tes
                        </label>
                        <input type="date"
                                name="tanggal_tes"
                                class="form-control form-control-sm rounded-3"
                                required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label text-muted fw-semibold small mb-1">
                            Bukti Verifikasi PDF/Gambar
                        </label>
                        <input type="file"
                                name="file_verifikasi"
                                class="form-control form-control-sm rounded-3"
                                accept=".pdf,.jpg,.jpeg,.png"
                                required>
                    </div>

                    <div class="col-md-2">
                        <button type="submit"
                                class="btn btn-primary btn-sm w-100 rounded-pill fw-semibold py-2"
                                style="background-color: var(--navy-theme); border-color: var(--navy-theme);">
                            <i class="fas fa-paper-plane me-1"></i> Kirim
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card live-card border-0 p-4 rounded-4">
            <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                Daftar Capaian Tahfidz
            </h6>

            <hr class="text-muted opacity-25 my-3">

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr>

                            <th class="border-0 px-3 py-3">
                                Surah / Materi
                            </th>

                            <th class="border-0 py-3">Tanggal</th>
                            <th class="border-0 py-3 text-center">Bukti</th>
                            <th class="border-0 py-3 text-center">Aksi</th>

                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data_tahfidz as $row)
                            <tr>

                                <td class="px-3">
                                    <span class="badge bg-light text-dark border px-2 py-1.5 rounded-3 fw-semibold">
                                        {{ $row->nama_surah }}
                                    </span>
                                </td>

                                <td class="text-muted">
                                    {{ date('d M Y', strtotime($row->tanggal_tes)) }}
                                </td>

                                <td class="text-center">
                                    @if ($row->file_verifikasi)
                                        <a href="{{ asset('uploads/tahfidz/' . $row->file_verifikasi) }}"
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
                                    <form action="{{ route('tahfidz.destroy', $row->id) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="confirmDelete(event, this, 'Yakin ingin menghapus data tahfidz ini?')">
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
                                <td colspan="4"
                                    class="text-center text-muted py-4">
                                    Belum ada data setoran hafalan terdata.
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