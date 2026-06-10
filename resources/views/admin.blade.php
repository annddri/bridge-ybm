<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

{{-- ===== TOPBAR ===== --}}
<nav class="admin-topbar">
    <div class="brand">
        <img src="{{ asset('img/New Logo YBM Secondary.png') }}" alt="YBM" onerror="this.style.display='none'">
        <div>
            <div class="brand-title">BRIDGE Admin Panel</div>
            <div class="brand-sub">Bright Scholarship</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="admin-badge">
            <i class="fas fa-shield-halved"></i>
            {{ session('name') }}
        </div>
        <a href="{{ route('logout') }}" class="btn-logout-top" onclick="confirmLogout(event, this.href)">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</nav>

{{-- ===== MAIN BODY ===== --}}
<div class="admin-body">

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(13,110,253,0.1);">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $users->count() }}</div>
                        <div class="stat-label">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(13,110,253,0.1);">
                        <i class="fas fa-user-graduate text-primary"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $users->where('role','mahasiswa')->count() }}</div>
                        <div class="stat-label">Mahasiswa</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(6,50,85,0.1);">
                        <i class="fas fa-house-user" style="color:#063255;"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $users->where('role','kepas')->count() }}</div>
                        <div class="stat-label">Kepala Asrama</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(20,184,166,0.1);">
                        <i class="fas fa-building" style="color:#0f766e;"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ $asramas->count() }}</div>
                        <div class="stat-label">Total Asrama</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL USERS ===== --}}
    <div class="panel-card mb-4">
        <div class="panel-header">
            <div class="section-title">
                <i class="fas fa-users"></i> Manajemen Akun Users
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('register') }}" class="btn-admin-primary d-flex align-items-center gap-2 text-decoration-none">
                    <i class="fas fa-user-plus"></i> Tambah Akun
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="tabel-users">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="35%">Nama / Username</th>
                        <th width="30%">Email</th>
                        <th width="15%">Role</th>
                        <th width="15%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        {{-- ===== Baris Data ===== --}}
                        <tr id="row-{{ $user->id }}" data-userid="{{ $user->id }}">
                            <td class="text-muted fs-8">{{ $index + 1 }}</td>

                            {{-- Nama --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $user->name }}</div>
                                <div class="fs-8 text-muted">ID: #{{ $user->id }}</div>
                            </td>

                            {{-- Email --}}
                            <td class="text-muted fs-7">{{ $user->email }}</td>

                            {{-- Role --}}
                            <td>
                                <span class="role-pill {{ $user->role }}">{{ $user->role }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            onclick="toggleEditRow({{ $user->id }})"
                                            title="Edit akun">
                                        <i class="fas fa-pen-to-square me-1"></i> Edit
                                    </button>
                                    @if($user->id !== session('id_user'))
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                onclick="hapusUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                title="Hapus akun">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="btn btn-sm btn-outline-secondary rounded-pill px-3 opacity-50"
                                              title="Tidak bisa hapus akun sendiri" style="cursor:not-allowed;">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- ===== Inline Edit Row ===== --}}
                        <tr class="edit-row" id="edit-row-{{ $user->id }}">
                            <td colspan="5">
                                <div class="edit-form-inner">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-pen text-primary"></i>
                                        <strong class="text-primary fs-7">Edit Akun: {{ $user->name }}</strong>
                                        <span class="role-pill {{ $user->role }} ms-1">{{ $user->role }}</span>
                                    </div>
                                    <form id="edit-form-{{ $user->id }}" onsubmit="submitEdit(event, {{ $user->id }})">
                                        @csrf
                                        <div class="row g-3">
                                            <!-- Left Column (Data Pribadi & Foto) -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text"
                                                           class="form-control"
                                                           id="edit-name-{{ $user->id }}"
                                                           name="name"
                                                           value="{{ $user->name }}"
                                                           required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email"
                                                           class="form-control"
                                                           id="edit-email-{{ $user->id }}"
                                                           name="email"
                                                           value="{{ $user->email }}"
                                                           required>
                                                </div>
                                            </div>

                                            <!-- Right Column (Password Atas Bawah) -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Password Baru</label>
                                                    <div class="input-group">
                                                        <input type="password"
                                                               class="form-control"
                                                               id="edit-pw-{{ $user->id }}"
                                                               name="password"
                                                               placeholder="Kosongkan jika tidak diubah">
                                                        <button type="button"
                                                                class="btn btn-outline-secondary"
                                                                onclick="toggleEditPw({{ $user->id }})"
                                                                style="border-radius:0 10px 10px 0; font-size:0.82rem;">
                                                            <i class="fas fa-eye" id="edit-eye-{{ $user->id }}"></i>
                                                        </button>
                                                    </div>
                                                    <div class="hint-text mt-1" style="font-size:0.8rem; color:#6c757d;">Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.</div>
                                                </div>
                                                <div id="pw-confirm-wrap-{{ $user->id }}" style="display:none;">
                                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                                    <input type="password"
                                                           class="form-control"
                                                           id="edit-pw-confirm-{{ $user->id }}"
                                                           name="password_confirmation"
                                                           placeholder="Ulangi password baru">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Dynamic Profile Fields --}}
                                        @if($user->role === 'mahasiswa' && $user->mahasiswaProfile)
                                            <hr class="my-4 border-secondary opacity-25">
                                            <h6 class="text-primary mb-3"><i class="fas fa-id-card me-2"></i>Profil Mahasiswa</h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label">NIBS <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="nibs" value="{{ $user->mahasiswaProfile->nibs }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">NIM <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="nim" value="{{ $user->mahasiswaProfile->nim }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Universitas</label>
                                                    <input type="text" class="form-control" name="universitas" value="{{ $user->mahasiswaProfile->universitas }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Prodi</label>
                                                    <input type="text" class="form-control" name="prodi" value="{{ $user->mahasiswaProfile->prodi }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Angkatan</label>
                                                    <input type="text" class="form-control" name="angkatan" value="{{ $user->mahasiswaProfile->angkatan }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Asrama</label>
                                                    <select name="asrama_id" class="form-select">
                                                        <option value="">-- Pilih Asrama --</option>
                                                        @foreach($asramas as $asrama)
                                                            <option value="{{ $asrama->id }}" {{ $user->mahasiswaProfile->asrama_id == $asrama->id ? 'selected' : '' }}>
                                                                {{ $asrama->nama_asrama }} ({{ $asrama->regional }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">No. WhatsApp</label>
                                                    <input type="text" class="form-control" name="no_telp" value="{{ $user->mahasiswaProfile->no_telp }}">
                                                </div>
                                            </div>
                                        @elseif($user->role === 'kepas' && $user->kepasProfile)
                                            <hr class="my-4 border-secondary opacity-25">
                                            <h6 class="text-primary mb-3"><i class="fas fa-building me-2"></i>Profil Kepala Asrama</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Asrama <span class="text-danger">*</span></label>
                                                    <select name="asrama_id" class="form-select" required>
                                                        <option value="">-- Pilih Asrama --</option>
                                                        @foreach($asramas as $asrama)
                                                            <option value="{{ $asrama->id }}" {{ $user->kepasProfile->asrama_id == $asrama->id ? 'selected' : '' }}>
                                                                {{ $asrama->nama_asrama }} ({{ $asrama->regional }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">No. WhatsApp</label>
                                                    <input type="text" class="form-control" name="no_telp" value="{{ $user->kepasProfile->no_telp }}">
                                                </div>
                                            </div>
                                        @endif

                                        <div class="d-flex gap-2 mt-3">
                                            <button type="submit"
                                                    class="btn-admin-primary"
                                                    id="btn-save-{{ $user->id }}">
                                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary rounded-pill px-4"
                                                    onclick="toggleEditRow({{ $user->id }})">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-users fs-2 d-block mb-2 opacity-25"></i>
                                Belum ada user terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== TABEL ASRAMA ===== --}}
    <div class="panel-card">
        <div class="panel-header">
            <div class="section-title">
                <i class="fas fa-building"></i> Manajemen Asrama
            </div>
            <button class="btn-admin-success d-flex align-items-center gap-2"
                    data-bs-toggle="modal" data-bs-target="#modalTambahAsrama">
                <i class="fas fa-plus"></i> Tambah Asrama
            </button>
        </div>

        <div class="table-responsive">
            <table class="table asrama-table" id="tabel-asrama">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Kode</th>
                        <th width="45%">Nama Asrama</th>
                        <th width="25%">Regional</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-asrama">
                    @forelse($asramas as $i => $asrama)
                        <tr id="asrama-row-{{ $asrama->id }}">
                            <td class="text-muted fs-8">{{ $i + 1 }}</td>
                            <td><span class="badge bg-light text-primary border border-primary-subtle fw-bold">{{ $asrama->kode_asrama }}</span></td>
                            <td class="fw-semibold">{{ $asrama->nama_asrama }}</td>
                            <td class="text-muted">{{ $asrama->regional ?? '—' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        onclick="hapusAsrama({{ $asrama->id }}, '{{ addslashes($asrama->nama_asrama) }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="asrama-empty">
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-building fs-2 d-block mb-2 opacity-25"></i>
                                Belum ada asrama terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end admin-body --}}

{{-- ===== MODAL TAMBAH ASRAMA ===== --}}
<div class="modal fade" id="modalTambahAsrama" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px; overflow:hidden;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg,#063255,#0d6efd); padding: 1.2rem 1.5rem;">
                <h6 class="modal-title text-white fw-bold m-0">
                    <i class="fas fa-building me-2"></i> Tambah Asrama Baru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="form-tambah-asrama" onsubmit="submitTambahAsrama(event)">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Kode Asrama <span class="text-danger">*</span></label>
                        <input type="text" name="kode_asrama" id="inp-kode-asrama"
                               class="form-control" placeholder="Contoh: BDG-01" required
                               style="border-radius:10px;">
                        <div class="text-muted" style="font-size:0.72rem; margin-top:3px;">Kode unik, otomatis jadi huruf kapital.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nama Asrama <span class="text-danger">*</span></label>
                        <input type="text" name="nama_asrama" id="inp-nama-asrama"
                               class="form-control" placeholder="Contoh: Bandung Bridge" required
                               style="border-radius:10px;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold fs-7">Regional</label>
                        <input type="text" name="regional" id="inp-regional"
                               class="form-control" placeholder="Contoh: Bandung"
                               style="border-radius:10px;">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn-admin-success w-100 py-2"
                                id="btn-simpan-asrama">
                            <i class="fas fa-save me-1"></i> Simpan Asrama
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== TOAST CONTAINER ===== --}}
<div id="toast-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ============================================
   TOAST NOTIFICATION
   ============================================ */
function showToast(message, isError = false) {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast-msg' + (isError ? ' error' : '');
    toast.innerHTML = `
        <span class="toast-icon">
            <i class="fas ${isError ? 'fa-times-circle' : 'fa-check-circle'}"></i>
        </span>
        <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3500);
}

/* ============================================
   PASSWORD TOGGLE (form edit inline)
   ============================================ */
function toggleEditPw(id) {
    const input = document.getElementById('edit-pw-' + id);
    const icon  = document.getElementById('edit-eye-' + id);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

/* ============================================
   INLINE EDIT ROW TOGGLE
   ============================================ */
function toggleEditRow(id) {
    // Tutup semua edit row lain dulu
    document.querySelectorAll('.edit-row').forEach(r => {
        if (r.id !== 'edit-row-' + id) r.classList.remove('show');
    });

    const editRow = document.getElementById('edit-row-' + id);
    editRow.classList.toggle('show');

    if (editRow.classList.contains('show')) {
        // Scroll ke edit row
        setTimeout(() => {
            editRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    // Tampil/sembunyikan konfirmasi password berdasarkan isi field pw
    const pwInput = document.getElementById('edit-pw-' + id);
    if (pwInput) {
        pwInput.addEventListener('input', function() {
            const wrap = document.getElementById('pw-confirm-wrap-' + id);
            wrap.style.display = this.value.length > 0 ? 'block' : 'none';
        });
    }
}

/* ============================================
   SUBMIT EDIT USER (AJAX PUT)
   ============================================ */
function submitEdit(event, id) {
    event.preventDefault();
    const btn = document.getElementById('btn-save-' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    const formData = new FormData(document.getElementById('edit-form-' + id));
    formData.append('_method', 'PUT');
    
    if (!formData.get('password')) {
        formData.delete('password');
        formData.delete('password_confirmation');
    }

    fetch(`/admin/users/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            // Update tampilan baris data tanpa reload
            const row = document.getElementById('row-' + id);
            row.querySelector('td:nth-child(2) .fw-semibold').textContent = res.user.name;
            row.querySelector('td:nth-child(3)').textContent = res.user.email;

            // Update form values
            document.getElementById('edit-name-' + id).value  = res.user.name;
            document.getElementById('edit-email-' + id).value = res.user.email;
            document.getElementById('edit-pw-' + id).value    = '';
            const wrap = document.getElementById('pw-confirm-wrap-' + id);
            if (wrap) wrap.style.display = 'none';

            toggleEditRow(id); // tutup edit row
            showToast(res.message);
        } else {
            showToast(res.message || 'Terjadi kesalahan.', true);
        }
    })
    .catch(() => showToast('Gagal terhubung ke server.', true))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Perubahan';
    });
}

/* ============================================
   HAPUS USER (AJAX DELETE)
   ============================================ */
function hapusUser(id, nama) {
    Swal.fire({
        title: 'Hapus Akun?',
        text: `Hapus akun "${nama}"? Semua data terkait akun ini juga akan ikut terhapus. Tindakan ini tidak bisa dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/users/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ _method: 'DELETE' }),
            })
            .then(r => r.json())
            .then(res => {
        if (res.status === 'success') {
            // Hapus baris dari tabel tanpa reload
            const row     = document.getElementById('row-' + id);
            const editRow = document.getElementById('edit-row-' + id);
            if (row)     row.remove();
            if (editRow) editRow.remove();
            showToast(res.message);

            // Update nomor baris
            renumberRows();
        } else {
            showToast(res.message || 'Gagal menghapus akun.', true);
        }
    })
            .catch(() => showToast('Gagal terhubung ke server.', true));
        }
    });
}

function renumberRows() {
    const rows = document.querySelectorAll('#tabel-users tbody tr:not(.edit-row)');
    let num = 1;
    rows.forEach(r => {
        const td = r.querySelector('td:first-child');
        if (td) td.textContent = num++;
    });
}

/* ============================================
   TAMBAH ASRAMA (AJAX POST)
   ============================================ */
function submitTambahAsrama(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-simpan-asrama');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    const data = {
        kode_asrama: document.getElementById('inp-kode-asrama').value,
        nama_asrama: document.getElementById('inp-nama-asrama').value,
        regional:    document.getElementById('inp-regional').value,
    };

    fetch('/admin/asrama', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            const a = res.asrama;
            const tbody = document.getElementById('tbody-asrama');

            // Hapus empty state jika ada
            const emptyRow = document.getElementById('asrama-empty');
            if (emptyRow) emptyRow.remove();

            // Tambah baris baru ke tabel
            const rowCount = tbody.querySelectorAll('tr').length + 1;
            const newRow = document.createElement('tr');
            newRow.id = 'asrama-row-' + a.id;
            newRow.innerHTML = `
                <td class="text-muted fs-8">${rowCount}</td>
                <td><span class="badge bg-light text-primary border border-primary-subtle fw-bold">${a.kode_asrama}</span></td>
                <td class="fw-semibold">${a.nama_asrama}</td>
                <td class="text-muted">${a.regional || '—'}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3"
                            onclick="hapusAsrama(${a.id}, '${a.nama_asrama.replace(/'/g,"\\'")}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>`;
            tbody.appendChild(newRow);

            // Reset form & tutup modal
            document.getElementById('form-tambah-asrama').reset();
            bootstrap.Modal.getInstance(document.getElementById('modalTambahAsrama')).hide();

            // Update stat card asrama
            const statCards = document.querySelectorAll('.stat-number');
            // stat card ke-4 adalah asrama
            showToast(res.message);
        } else {
            showToast(res.message || 'Gagal menyimpan asrama.', true);
        }
    })
    .catch(() => showToast('Gagal terhubung ke server.', true))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Asrama';
    });
}

/* ============================================
   HAPUS ASRAMA (AJAX DELETE)
   ============================================ */
function hapusAsrama(id, nama) {
    Swal.fire({
        title: 'Hapus Asrama?',
        text: `Hapus asrama "${nama}"? Pastikan tidak ada mahasiswa/kepas yang masih terhubung ke asrama ini.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/asrama/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ _method: 'DELETE' }),
            })
            .then(r => r.json())
            .then(res => {
        if (res.status === 'success') {
            const row = document.getElementById('asrama-row-' + id);
            if (row) row.remove();
            showToast(res.message);
        } else {
            showToast(res.message || 'Gagal menghapus asrama.', true);
        }
    })
    .catch(() => showToast('Gagal terhubung ke server.', true));
        }
    });
}

function confirmLogout(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Keluar dari Panel?',
        text: "Apakah Anda yakin ingin keluar?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
</body>
</html>
