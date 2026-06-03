<x-header title="Keuangan Asrama - Bridge" css="css/keuangan.css"></x-header>

@if (($role_user ?? '') == 'kepas')
<x-sidebarKepas 
    :u="$u" 
    :fotoPath="$foto_path" 
></x-sidebarKepas>
@else
<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>
@endif

@php
    $readonly = $readonly ?? false;
@endphp
<div class="main-content">
    <div class="container-fluid">
        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-wallet fs-3 me-3" style="color: var(--accent-color);"></i>
                    <div>
                        <h4 class="fw-bold m-0 header-title">Kas Asrama</h4>
                        <p class="m-0 header-subtitle">
                            Kelola transaksi kas komunal asrama.
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

        {{-- RINGKASAN KAS --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="finance-card bg-success">
                    <small>Total Kas Masuk</small>
                    <h4>Rp {{ number_format($total_masuk, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="col-md-4">
                <div class="finance-card bg-danger">
                    <small>Total Kas Keluar</small>
                    <h4>Rp {{ number_format($total_keluar, 0, ',', '.') }}</h4>
                </div>
            </div>

            <div class="col-md-4">
                <div class="finance-card bg-primary">
                    <small>Saldo Kas</small>
                    <h4>Rp {{ number_format($saldo, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>

        @if(!$readonly)
        {{-- FORM TAMBAH TRANSAKSI --}}
        <div class="card form-card mb-4">
            <div class="mb-3">
                <h6 class="fw-bold m-0 text-dark">Tambah Transaksi Kas</h6>
                <small class="text-muted">
                    Penginput otomatis diambil dari akun yang sedang login.
                </small>
            </div>

            <form action="{{ route('keuangan.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Jenis Transaksi</label>
                        <select name="jenis_transaksi" class="form-select" required>
                            <option value="Masuk" {{ old('jenis_transaksi') == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="Keluar" {{ old('jenis_transaksi') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nominal</label>
                        <input
                            type="number"
                            name="nominal"
                            class="form-control"
                            value="{{ old('nominal') }}"
                            min="1"
                            placeholder="Contoh: 50000"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea
                            name="keterangan"
                            class="form-control"
                            rows="3"
                            placeholder="Contoh: Iuran kas bulan Mei"
                        >{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary-ybm rounded-pill px-4 fw-bold">
                            <i class="fas fa-plus me-2"></i>
                            Simpan Transaksi
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- TABEL KAS --}}
        <div class="card table-card">
            <div class="mb-3">
                <h6 class="fw-bold m-0 text-dark">Riwayat Kas Asrama</h6>
                <small class="text-muted">
                    Edit dan hapus hanya bisa dilakukan oleh pembuat transaksi.
                </small>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Penginput</th>
                            <th>Jenis</th>
                            <th>Nominal</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($data_kas as $row)
                            @php
                                $bolehKelola = !$readonly && $row->created_by_id == session('id_user');
                                $badge = $row->jenis_transaksi == 'Masuk'
                                    ? 'bg-success bg-opacity-10 text-success'
                                    : 'bg-danger bg-opacity-10 text-danger';
                            @endphp

                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>

                                <td>
                                    {{ date('d/m/Y', strtotime($row->tanggal)) }}
                                </td>

                                <td>
                                    <div class="fw-semibold">{{ $row->created_by ?? '-' }}</div>
                                    <small class="text-muted">NIBS: {{ $row->created_by_nibs ?? '-' }}</small>
                                </td>

                                <td>
                                    <span class="badge {{ $badge }} px-2 py-1 rounded-pill fw-bold">
                                        {{ $row->jenis_transaksi }}
                                    </span>
                                </td>

                                <td class="fw-bold">
                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                </td>

                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button
                                            type="button"
                                            class="btn btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailKas{{ $row->id_kas }}"
                                            title="Detail"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if ($bolehKelola)
                                            <button
                                                type="button"
                                                class="btn btn-outline-warning"
                                                onclick="toggleEditKas({{ $row->id_kas }})"
                                                title="Edit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <form
                                                action="{{ route('keuangan.destroy', $row->id_kas) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')"
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
                                                class="btn btn-outline-warning"
                                                disabled
                                                title="Hanya pembuat transaksi yang bisa mengedit"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-outline-danger"
                                                disabled
                                                title="Hanya pembuat transaksi yang bisa menghapus"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- FORM EDIT INLINE --}}
                            @if ($bolehKelola)
                                <tr id="editKas{{ $row->id_kas }}" class="edit-row d-none">
                                    <td colspan="6">
                                        <form
                                            action="{{ route('keuangan.update', $row->id_kas) }}"
                                            method="POST"
                                            class="edit-inline-card"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Tanggal</label>
                                                    <input
                                                        type="date"
                                                        name="tanggal"
                                                        class="form-control form-control-sm"
                                                        value="{{ $row->tanggal }}"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Jenis</label>
                                                    <select name="jenis_transaksi" class="form-select form-select-sm" required>
                                                        <option value="Masuk" {{ $row->jenis_transaksi == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                                                        <option value="Keluar" {{ $row->jenis_transaksi == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Nominal</label>
                                                    <input
                                                        type="number"
                                                        name="nominal"
                                                        class="form-control form-control-sm"
                                                        value="{{ $row->nominal }}"
                                                        min="1"
                                                        required
                                                    >
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label small fw-semibold">Keterangan</label>
                                                    <input
                                                        type="text"
                                                        name="keterangan"
                                                        class="form-control form-control-sm"
                                                        value="{{ $row->keterangan }}"
                                                    >
                                                </div>

                                                <div class="col-12 d-flex gap-2">
                                                    <button type="submit" class="btn btn-warning btn-sm text-white rounded-pill px-3 fw-bold">
                                                        <i class="fas fa-save me-1"></i>
                                                        Simpan
                                                    </button>

                                                    <button
                                                        type="button"
                                                        class="btn btn-light btn-sm rounded-pill px-3"
                                                        onclick="toggleEditKas({{ $row->id_kas }})"
                                                    >
                                                        Batal
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif

                            {{-- MODAL DETAIL --}}
                            <div class="modal fade" id="detailKas{{ $row->id_kas }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content detail-card">
                                        <div class="modal-header py-2">
                                            <h6 class="modal-title fw-bold">Detail Kas</h6>
                                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body small">
                                            <div class="mb-2">
                                                <div class="text-muted">Tanggal</div>
                                                <div class="fw-bold">
                                                    {{ date('d/m/Y', strtotime($row->tanggal)) }}
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Jenis Transaksi</div>
                                                <div class="fw-bold">{{ $row->jenis_transaksi }}</div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Nominal</div>
                                                <div class="fw-bold">
                                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                                </div>
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
                                                <div class="text-muted">Tanggal Ditambahkan</div>
                                                <div class="fw-bold">
                                                    {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->translatedFormat('d F Y, H:i') : '-' }}
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted">Tanggal Terakhir Diupdate</div>
                                                <div class="fw-bold">
                                                    {{ $row->updated_at ? \Carbon\Carbon::parse($row->updated_at)->translatedFormat('d F Y, H:i') : '-' }}
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
                                    Belum ada transaksi kas.
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
    function toggleEditKas(id) {
        const editRow = document.getElementById('editKas' + id);

        if (editRow) {
            editRow.classList.toggle('d-none');
        }
    }
</script>


<x-footer></x-footer>