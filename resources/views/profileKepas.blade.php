<x-header title="Profile Kepala Asrama - Bridge" css="css/profileKepas.css"></x-header>
<x-sidebarKepas
 :u="$u"
 :fotoPath="$foto_path"
/>

<div class="main-content">

<div class="profile-header">

    <div class="profile-left">

        <h1 class="profile-name">
            {{ $u->name }}
        </h1>

        <div class="profile-role">
            Kepala Asrama YBM BRILiaN
        </div>

    </div>

    <img
        src="{{ $foto_path }}?t={{ time() }}"
        class="profile-avatar"
        alt="Foto Profil">

</div>

<div class="profile-card">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>

            <h4 class="section-title mb-1">
                Informasi Profil
            </h4>

            <p class="text-muted mb-0">
                Informasi akun dan data kepala asrama.
            </p>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Nama Lengkap
                </div>

                <div class="info-value">
                    {{ $u->name }}
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Email
                </div>

                <div class="info-value">
                    {{ $u->email }}
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Role
                </div>

                <div class="info-value">
                    Kepala Asrama
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Nomor WhatsApp
                </div>

                <div class="info-value">
                    <div class="d-flex align-items-center gap-2">
                        <span id="whatsappText">
                            {{ $u->kepasProfile->no_telp ?? 'Belum diisi' }}
                        </span>

                        <button
                            type="button"
                            id="editWhatsappBtn"
                            class="btn btn-sm btn-outline-primary rounded-circle">

                            <i class="fas fa-pen"></i>

                        </button>

                    </div>

                    <div id="whatsappEditBox" class="mt-2 d-none">

                        <small
                            id="whatsappError"
                            class="text-secondary d-block mb-1">
                        </small>

                        <input
                            type="text"
                            id="whatsappInput"
                            class="form-control form-control-sm"
                            value="{{ $u->kepasProfile->no_telp ?? '' }}"
                            placeholder="Masukkan nomor WhatsApp">

                        <div class="mt-2 d-flex gap-2">

                            <button
                                type="button"
                                id="saveWhatsappBtn"
                                class="btn btn-sm btn-primary">

                                Simpan

                            </button>

                            <button
                                type="button"
                                id="cancelWhatsappBtn"
                                class="btn btn-sm btn-secondary">

                                Batal

                            </button>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Kode Asrama
                </div>

                <div class="info-value">
                    {{ $u->kepasProfile->asrama->kode_asrama ?? '-' }}
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="info-box">

                <div class="info-label">
                    Nama Asrama
                </div>

                <div class="info-value">
                    {{ $u->kepasProfile->asrama->nama_asrama ?? '-' }}
                </div>

            </div>

        </div>

        <div class="col-md-12">

            <div class="info-box">

                <div class="info-label">
                    Regional
                </div>

                <div class="info-value">
                    {{ $u->kepasProfile->asrama->regional ?? '-' }}
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

    fetch("{{ route('profileKepas.updateWhatsapp') }}", {
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