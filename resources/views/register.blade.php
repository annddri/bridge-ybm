<x-header title="Registrasi Akun - Bridge" css="css/register.css"></x-header>

<div class="container d-flex justify-content-center py-4">
    <div class="register-card w-100">

        {{-- ===== Header Card ===== --}}
        <div class="card-header-brand position-relative">
            <a href="{{ route('admin') }}" class="btn btn-sm btn-outline-secondary position-absolute top-0 start-0 m-3" style="border-radius: 20px;">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <img src="{{ asset('img/New Logo YBM Secondary.png') }}"
                 alt="YBM BRILiaN Logo"
                 class="logo-img">
            <h2>Registrasi Akun Baru</h2>
            <div class="subtitle">Bridge · Bright Scholarship · Admin Panel</div>
        </div>

        {{-- ===== Body Form ===== --}}
        <div class="card-body-form">

            {{-- Pesan Sukses --}}
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-start gap-2 mb-4" role="alert">
                    <i class="fas fa-check-circle mt-1"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Pesan Error Validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-4" role="alert">
                    <strong><i class="fas fa-exclamation-circle me-1"></i> Terdapat kesalahan input:</strong>
                    <ul class="mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.process') }}" method="POST" enctype="multipart/form-data" id="register-form">
                @csrf

                {{-- ===== SECTION: Informasi Akun ===== --}}
                <p class="section-label">
                    <i class="fas fa-user-shield me-1"></i> Informasi Akun
                </p>

                {{-- Nama Lengkap --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text"
                           id="name"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Masukkan nama lengkap"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="nama@email.com"
                           value="{{ old('email') }}"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               id="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Minimal 8 karakter"
                               required>
                        <button type="button" class="btn-toggle-pw" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger fs-7 mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Ulangi password"
                               required>
                        <button type="button" class="btn-toggle-pw" onclick="togglePassword('password_confirmation', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Role --}}
                <div class="mb-2">
                    <label for="role-select" class="form-label">Role / Peran <span class="text-danger">*</span></label>
                    <select id="role-select"
                            name="role"
                            class="form-select @error('role') is-invalid @enderror"
                            onchange="handleRoleChange(this.value)"
                            required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                        <option value="mahasiswa"     {{ old('role') === 'mahasiswa'     ? 'selected' : '' }}>🎓 Mahasiswa (Awardee)</option>
                        <option value="kepas"         {{ old('role') === 'kepas'         ? 'selected' : '' }}>🏠 Kepala Asrama</option>
                        <option value="administrator" {{ old('role') === 'administrator' ? 'selected' : '' }}>🛡️ Administrator</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ===== SECTION: Profil Mahasiswa ===== --}}
                <div id="fields-mahasiswa" class="profile-fields collapsed">
                    <p class="section-label mt-3">
                        <i class="fas fa-id-card me-1"></i> Profil Mahasiswa
                    </p>

                    <div class="row g-3">
                        {{-- NIBS --}}
                        <div class="col-md-6">
                            <label for="nibs" class="form-label">NIBS <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="nibs"
                                   name="nibs"
                                   class="form-control @error('nibs') is-invalid @enderror"
                                   placeholder="Nomor Induk Beasiswa"
                                   value="{{ old('nibs') }}">
                            @error('nibs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NIM --}}
                        <div class="col-md-6">
                            <label for="nim" class="form-label">NIM <span class="text-danger">*</span></label>
                            <input type="text"
                                   id="nim"
                                   name="nim"
                                   class="form-control @error('nim') is-invalid @enderror"
                                   placeholder="Nomor Induk Mahasiswa"
                                   value="{{ old('nim') }}">
                            @error('nim')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Universitas --}}
                        <div class="col-md-6">
                            <label for="universitas" class="form-label">Universitas</label>
                            <input type="text"
                                   id="universitas"
                                   name="universitas"
                                   class="form-control"
                                   placeholder="Nama universitas"
                                   value="{{ old('universitas') }}">
                        </div>

                        {{-- Prodi --}}
                        <div class="col-md-6">
                            <label for="prodi" class="form-label">Program Studi</label>
                            <input type="text"
                                   id="prodi"
                                   name="prodi"
                                   class="form-control"
                                   placeholder="Contoh: Teknik Informatika"
                                   value="{{ old('prodi') }}">
                        </div>

                        {{-- Angkatan --}}
                        <div class="col-md-6">
                            <label for="angkatan" class="form-label">Angkatan</label>
                            <input type="text"
                                   id="angkatan"
                                   name="angkatan"
                                   class="form-control"
                                   placeholder="Contoh: 2023"
                                   value="{{ old('angkatan') }}"
                                   maxlength="10">
                        </div>

                        {{-- Asrama (Mahasiswa) --}}
                        <div class="col-md-6">
                            <label for="asrama_id_mhs" class="form-label">Asrama</label>
                            <select id="asrama_id_mhs"
                                    name="asrama_id"
                                    class="form-select @error('asrama_id') is-invalid @enderror">
                                <option value="">-- Pilih Asrama (opsional) --</option>
                                @foreach ($asramas as $asrama)
                                    <option value="{{ $asrama->id }}"
                                        {{ old('asrama_id') == $asrama->id && old('role') === 'mahasiswa' ? 'selected' : '' }}>
                                        {{ $asrama->nama_asrama }}
                                        @if($asrama->regional) ({{ $asrama->regional }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No. Telepon Mahasiswa --}}
                        <div class="col-md-6">
                            <label for="no_telp_mhs" class="form-label">No. WhatsApp</label>
                            <input type="text"
                                   id="no_telp_mhs"
                                   name="no_telp"
                                   class="form-control"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_telp') }}">
                        </div>

                        {{-- Foto Profil Mahasiswa --}}
                        <div class="col-12">
                            <label class="form-label">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="foto-preview-wrap">
                                    <i class="fas fa-user-circle" id="foto-placeholder"></i>
                                    <img id="foto-preview" src="#" alt="Preview">
                                </div>
                                <div>
                                    <input type="file"
                                           id="foto_profil_mhs"
                                           name="foto_profil"
                                           class="form-control @error('foto_profil') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewFoto(this)">
                                    <div class="hint-text mt-1">Format: JPG, PNG, WebP · Maks. 2MB</div>
                                    @error('foto_profil')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== SECTION: Profil Kepala Asrama ===== --}}
                <div id="fields-kepas" class="profile-fields collapsed">
                    <p class="section-label mt-3">
                        <i class="fas fa-building me-1"></i> Profil Kepala Asrama
                    </p>

                    <div class="row g-3">
                        {{-- Asrama (Kepas) - Wajib --}}
                        <div class="col-12">
                            <label for="asrama_id_kepas" class="form-label">Asrama <span class="text-danger">*</span></label>
                            <select id="asrama_id_kepas"
                                    name="asrama_id"
                                    class="form-select @error('asrama_id') is-invalid @enderror">
                                <option value="" disabled selected>-- Pilih Asrama --</option>
                                @foreach ($asramas as $asrama)
                                    <option value="{{ $asrama->id }}"
                                        {{ old('asrama_id') == $asrama->id && old('role') === 'kepas' ? 'selected' : '' }}>
                                        {{ $asrama->nama_asrama }}
                                        @if($asrama->regional) · {{ $asrama->regional }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('asrama_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- No. Telepon Kepas --}}
                        <div class="col-md-6">
                            <label for="no_telp_kepas" class="form-label">No. WhatsApp</label>
                            <input type="text"
                                   id="no_telp_kepas"
                                   name="no_telp"
                                   class="form-control"
                                   placeholder="08xxxxxxxxxx"
                                   value="{{ old('no_telp') }}">
                        </div>

                        {{-- Foto Profil Kepas --}}
                        <div class="col-12">
                            <label class="form-label">Foto Profil</label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="foto-preview-wrap-kepas">
                                    <i class="fas fa-user-circle" id="foto-placeholder-kepas"
                                       style="color:#adb5bd;font-size:2rem;"></i>
                                    <img id="foto-preview-kepas" src="#" alt="Preview"
                                         style="width:100%;height:100%;object-fit:cover;display:none;border-radius:50%;">
                                </div>
                                <div>
                                    <input type="file"
                                           id="foto_profil_kepas"
                                           name="foto_profil"
                                           class="form-control @error('foto_profil') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewFotoKepas(this)">
                                    <div class="hint-text mt-1">Format: JPG, PNG, WebP · Maks. 2MB</div>
                                    @error('foto_profil')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== SECTION: Info Administrator ===== --}}
                <div id="fields-administrator" class="profile-fields collapsed">
                    <p class="section-label mt-3">
                        <i class="fas fa-shield-halved me-1"></i> Role Administrator
                    </p>
                    <div class="alert alert-danger py-2 px-3 d-flex align-items-center gap-2" style="border-radius:12px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span class="fs-7">Administrator memiliki akses penuh ke panel registrasi ini. Pastikan hanya orang yang dipercaya.</span>
                    </div>
                </div>

                {{-- ===== Tombol Submit ===== --}}
                <div class="d-grid mt-4">
                    <button type="submit" class="btn-register" id="btn-submit">
                        <i class="fas fa-user-plus me-2"></i> Daftarkan Akun
                    </button>
                </div>

                <p class="text-center hint-text mt-3">
                    <i class="fas fa-lock me-1"></i>
                    Halaman ini bersifat <strong>rahasia</strong>. Jangan bagikan URL ini ke selain admin.
                </p>

            </form>
        </div>{{-- end card-body-form --}}
    </div>{{-- end register-card --}}
</div>

{{-- ===== JavaScript ===== --}}
<script>
    // --- Inisialisasi dari old() value saat ada validation error ---
    document.addEventListener('DOMContentLoaded', function () {
        const oldRole = '{{ old('role') }}';
        if (oldRole) {
            handleRoleChange(oldRole);
        }
    });

    /**
     * Toggle visibility field berdasarkan role yang dipilih.
     */
    function handleRoleChange(role) {
        const sections = {
            mahasiswa:     document.getElementById('fields-mahasiswa'),
            kepas:         document.getElementById('fields-kepas'),
            administrator: document.getElementById('fields-administrator'),
        };

        // Sembunyikan semua section
        Object.values(sections).forEach(el => {
            if (el) {
                el.classList.remove('expanded');
                el.classList.add('collapsed');
            }
        });

        // Nonaktifkan field kepas agar tidak ikut tersubmit
        setKepasFieldsDisabled(true);
        setMahasiswaFieldsDisabled(true);

        // Tampilkan section yang sesuai
        if (role && sections[role]) {
            sections[role].classList.remove('collapsed');
            sections[role].classList.add('expanded');

            if (role === 'mahasiswa') setMahasiswaFieldsDisabled(false);
            if (role === 'kepas')     setKepasFieldsDisabled(false);
        }
    }

    function setMahasiswaFieldsDisabled(disabled) {
        const fields = ['nibs', 'nim', 'universitas', 'prodi', 'angkatan',
                        'asrama_id_mhs', 'no_telp_mhs', 'foto_profil_mhs'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = disabled;
        });
    }

    function setKepasFieldsDisabled(disabled) {
        const fields = ['asrama_id_kepas', 'no_telp_kepas', 'foto_profil_kepas'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.disabled = disabled;
        });
    }

    /**
     * Toggle show/hide password.
     */
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    /**
     * Preview foto profil mahasiswa sebelum upload.
     */
    function previewFoto(input) {
        const preview     = document.getElementById('foto-preview');
        const placeholder = document.getElementById('foto-placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src          = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * Preview foto profil kepas sebelum upload.
     */
    function previewFotoKepas(input) {
        const preview     = document.getElementById('foto-preview-kepas');
        const placeholder = document.getElementById('foto-placeholder-kepas');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src          = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<x-footer></x-footer>
