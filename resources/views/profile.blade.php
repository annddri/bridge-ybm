<x-header title="Profile - Bridge" css="css/profile.css"></x-header>

<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>

<div class="main-content">
    <div class="header-gradient"></div>

    <div class="profile-wrapper">
        <div class="container-fluid profile-container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card card-profile p-4 p-md-5">
                        
                        <div class="text-center">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $foto_path }}?t={{ time() }}" alt="Profile" class="brand-logo shadow">
                                <span class="badge bg-<?= $theme ?> position-absolute bottom-0 end-0 px-3 py-2 rounded-pill shadow-sm text-uppercase fw-bold" style="transform: translate(-5%, -15%); font-size: 0.72rem;">
                                    {{ $u->role }}
                                </span>
                            </div>

                            <h3 class="fw-bold mt-2 text-dark">{{ $u->role }}</h3>
                            
                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <a href="edit_profile.php" class="btn btn-primary px-4 rounded-pill btn-sm fw-bold shadow-sm">Edit Profil</a>
                                <a href="index.php" class="btn btn-outline-secondary px-4 rounded-pill btn-sm fw-semibold shadow-sm">Ke Dashboard</a>
                            </div>
                        </div>

                        <hr class="opacity-25 my-4">

                        <div class="row px-md-2">
                            <div class="col-md-6 border-end border-light mb-4 mb-md-0">
                                <h6 class="fw-bold text-primary mb-4"><i class="fas fa-graduation-cap me-2"></i>Informasi Akademik</h6>
                                
                                <div class="mb-3">
                                    <div class="info-label">NIBS (Nomor Induk Beasiswa)</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->nibs}}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">NIM (Nomor Induk Mahasiswa)</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->nim }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Universitas</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->universitas }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Program Studi</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->prodi }}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Angkatan Beasiswa</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->angkatan }}</div>
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold text-primary mb-4"><i class="fas fa-id-card me-2"></i>Kontak & Hubungan Internal</h6>
                                
                                <div class="mb-3">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $u->email}}</div>
                                </div>
                                <div class="mb-3">
                                    <div class="info-label">Nomor Telepon (WhatsApp)</div>
                                    <div class="info-value">{{ $u->mahasiswaProfile->no_telp ?? 'Belum diisi' }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-footer></x-footer>