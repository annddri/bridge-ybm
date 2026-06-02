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
                                <img src="{{ $foto_path }}?t={{ time() }}" alt="Profile" class="profile-img shadow">
                            </div>

                            <h3 class="fw-bold mt-2 text-dark">{{ $u->role }}</h3>
                            
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
                                {{-- <div class="mb-3">
                                    <div class="info-label">Asrama</div>
                                    <div class="info-value">{{ $u->}}</div>
                                </div> --}}
                                <div class="info-label">NOMOR TELEPON (WHATSAPP)</div>

                                    <div class="d-flex align-items-center gap-2">
                                        <span id="whatsappText">
                                            {{ $u->mahasiswaProfile->no_telp ?? 'Belum diisi' }}
                                        </span>

                                        <button type="button" id="editWhatsappBtn" class="btn btn-sm btn-outline-primary rounded-circle">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    </div>

                                    <div id="whatsappEditBox" class="mt-2 d-none">
                                        <small id="whatsappError" class="text-secondary d-block mb-1"></small>

                                        <input 
                                            type="text" 
                                            id="whatsappInput" 
                                            class="form-control form-control-sm"
                                            value="{{ $u->mahasiswaProfile->no_telp ?? '' }}"
                                            placeholder="Masukkan nomor WhatsApp"
                                        >
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" id="saveWhatsappBtn" class="btn btn-sm btn-primary">
                                                Simpan
                                            </button>

                                            <button type="button" id="cancelWhatsappBtn" class="btn btn-sm btn-secondary">
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editWhatsappBtn').addEventListener('click', function () {
    document.getElementById('whatsappEditBox').classList.remove('d-none');
    document.getElementById('whatsappError').innerText = 'Nomor WhatsApp harus 10-13 angka tanpa huruf atau karakter. Awali dengan 08...';
});

document.getElementById('cancelWhatsappBtn').addEventListener('click', function () {
    document.getElementById('whatsappEditBox').classList.add('d-none');
    document.getElementById('whatsappError').innerText = '';
});

document.getElementById('saveWhatsappBtn').addEventListener('click', function () {
    let noTelp = document.getElementById('whatsappInput').value.trim();
    let errorBox = document.getElementById('whatsappError');

    if (!/^08[0-9]{8,11}$/.test(noTelp)) {
        errorBox.innerText = 'Nomor WhatsApp harus diawali 08 dan berisi 10-13 angka tanpa huruf atau karakter.';
        return;
    }

    fetch("{{ route('profile.updateWhatsapp') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            no_telp: noTelp
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('whatsappText').innerText = data.no_telp;
            document.getElementById('whatsappEditBox').classList.add('d-none');
            errorBox.innerText = '';
        }
    });
});
</script>

<x-footer></x-footer>