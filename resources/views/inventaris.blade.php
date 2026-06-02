<x-header title="Inventaris Asrama - Bridge" css="css/inventaris.css"></x-header>

@if ($role_user == "kepas")
<x-sidebarKepas 
    :u="$u" 
    :role-user="$role_user" 
    :fotoPath="$foto_path" 
></x-sidebarKepas>
@else
<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>
@endif

<div class="main-content">
    <div class="container-fluid">

        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-boxes-stacked fs-3 me-3" style="color: var(--accent-color);"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">Inventaris Asrama</h4>
                        <p class="m-0 header-subtitle">
                            Kelola data logistik dan fasilitas bersama secara komunal.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm">
                <div class="fw-bold mb-1">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    Data belum valid
                </div>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- FORM TAMBAH BARANG --}}
        <div class="card table-card shadow-sm mb-4">
            <div class="mb-3">
                <h6 class="fw-bold m-0 text-dark">Tambah Barang Inventaris</h6>
                <small class="text-muted">Isi data barang yang ingin ditambahkan ke inventaris.</small>
            </div>

            <form action="{{ route('inventaris.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kode Inventaris</label>
                        <input
                            type="text"
                            name="kode_barang"
                            class="form-control"
                            value="{{ old('kode_barang') }}"
                            placeholder="Contoh: BRIGHT-MC-02"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Barang</label>
                        <input
                            type="text"
                            name="nama_barang"
                            class="form-control"
                            value="{{ old('nama_barang') }}"
                            placeholder="Contoh: Kipas Angin Cosmos"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Jumlah Unit</label>
                        <input
                            type="number"
                            name="jumlah"
                            class="form-control"
                            value="{{ old('jumlah', 1) }}"
                            min="1"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kondisi</label>
                        <select name="kondisi" class="form-select" required>
                            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Contoh: Rusak ringan, lecet di bagian badan kipas."
                        >{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-ybm rounded-pill px-4 fw-bold">
                            <i class="fas fa-plus me-2"></i>
                            Simpan Barang
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL INVENTARIS --}}
        <div class="card table-card shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold m-0 text-dark">Daftar Barang Komunal</h6>
                    <small class="text-muted">Daftar seluruh barang inventaris asrama.</small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Kondisi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data_inventaris as $row)
                            @php
                                if ($row->kondisi == 'Baik') {
                                    $badge = 'bg-success bg-opacity-10 text-success';
                                } elseif ($row->kondisi == 'Rusak Ringan') {
                                    $badge = 'bg-warning bg-opacity-10 text-warning';
                                } else {
                                    $badge = 'bg-danger bg-opacity-10 text-danger';
                                }
                            @endphp

                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>

                                <td class="fw-bold text-secondary">
                                    {{ $row->kode_barang }}
                                </td>

                                <td class="fw-bold text-dark">
                                    {{ $row->nama_barang }}
                                </td>

                                <td>
                                    {{ $row->jumlah }} unit
                                </td>

                                <td>
                                    <span class="badge {{ $badge }} px-2 py-1 rounded-pill fw-bold">
                                        {{ $row->kondisi }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button
                                            type="button"
                                            class="btn btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailInventaris{{ $row->id_barang }}"
                                            title="Detail"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-outline-warning"
                                            onclick="toggleEdit({{ $row->id_barang }})"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        @if ($role_user === 'kepas')
                                            <form
                                                action="{{ route('inventaris.destroy', $row->id_barang) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus barang ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                class="btn btn-outline-danger"
                                                disabled
                                                title="Hanya kepala asrama yang bisa menghapus"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- FORM EDIT INLINE --}}
                            <tr id="edit-{{ $row->id_barang }}" class="d-none">
                                <td colspan="6">
                                    <div class="edit-box p-3 rounded-4 bg-light border">
                                        <form action="{{ route('inventaris.update', $row->id_barang) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Kode Inventaris</label>
                                                    <input
                                                        type="text"
                                                        name="kode_barang"
                                                        class="form-control"
                                                        value="{{ $row->kode_barang }}"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nama Barang</label>
                                                    <input
                                                        type="text"
                                                        name="nama_barang"
                                                        class="form-control"
                                                        value="{{ $row->nama_barang }}"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Jumlah Unit</label>
                                                    <input
                                                        type="number"
                                                        name="jumlah"
                                                        class="form-control"
                                                        value="{{ $row->jumlah }}"
                                                        min="1"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Kondisi</label>
                                                    <select name="kondisi" class="form-select" required>
                                                        <option value="Baik" {{ $row->kondisi == 'Baik' ? 'selected' : '' }}>Baik</option>
                                                        <option value="Rusak Ringan" {{ $row->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                        <option value="Rusak Berat" {{ $row->kondisi == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                                    </select>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label fw-semibold">Keterangan</label>
                                                    <textarea
                                                        name="keterangan"
                                                        class="form-control"
                                                        rows="3"
                                                    >{{ $row->keterangan }}</textarea>
                                                </div>

                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-warning text-white rounded-pill px-4 fw-bold">
                                                        <i class="fas fa-save me-2"></i>
                                                        Simpan Perubahan
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="btn btn-light rounded-pill px-4 ms-2"
                                                        onclick="toggleEdit({{ $row->id_barang }})"
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- MODAL DETAIL --}}
                            <div class="modal fade" id="detailInventaris{{ $row->id_barang }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content detail-card">
                                        <div class="modal-header py-2">
                                            <h6 class="modal-title fw-bold">
                                                Detail Barang
                                            </h6>
                                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body small">
                                            <div class="mb-2">
                                                <div class="text-muted">Kode Barang</div>
                                                <div class="fw-bold">{{ $row->kode_barang }}</div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Nama Barang</div>
                                                <div class="fw-bold">{{ $row->nama_barang }}</div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Jumlah Unit</div>
                                                <div class="fw-bold">{{ $row->jumlah }} unit</div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Kondisi</div>
                                                <div class="fw-bold">{{ $row->kondisi }}</div>
                                            </div>

                                            <hr>

                                            <div class="mb-2">
                                                <div class="text-muted">Dibuat oleh</div>
                                                <div class="fw-bold">{{ $row->created_by ?? '-' }}</div>
                                                <div class="text-muted">
                                                    NIBS: {{ $row->created_by_nibs ?? '-' }}
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Terakhir diubah oleh</div>
                                                <div class="fw-bold">{{ $row->updated_by ?? '-' }}</div>
                                                <div class="text-muted">
                                                    NIBS: {{ $row->updated_by_nibs ?? '-' }}
                                                </div>
                                            </div>

                                            <hr>

                                            <div>
                                                <div class="text-muted">Keterangan</div>
                                                <div>{{ $row->keterangan ?: '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
                                    Belum ada barang terdata.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    function toggleEdit(id) {
        const editRow = document.getElementById('edit-' + id);

        if (editRow) {
            editRow.classList.toggle('d-none');
        }
    }
</script>

<x-footer></x-footer>