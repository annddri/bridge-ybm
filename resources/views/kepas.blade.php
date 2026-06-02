<x-header css="css/kepas.css" title="Dashboard Kepala Asrama - Bridge"></x-header>


<x-sidebarKepas
    :u="$u"
    :fotoPath="$foto_path"
></x-sidebarKepas>

<div class="main-content">

<div class="content-body">

    {{-- WELCOME CARD --}}
    <div class="card welcome-box border-0 rounded-4 mb-4">

        <div class="card-body p-4">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h3 class="fw-bold mb-2">
                        Dashboard Kepala Asrama
                    </h3>

                    <p class="text-muted mb-0">
                        Selamat datang kembali,
                        <strong>{{ $u->name }}</strong>.
                        Kelola dan monitor perkembangan awardee serta
                        aktivitas asrama melalui dashboard ini.
                    </p>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                        {{ now()->translatedFormat('d F Y') }}
                    </span>

                </div>

            </div>

        </div>

    </div>

    {{-- STATISTIK --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
            <div class="card live-card border-0 rounded-4 h-100">

                <div class="card-body">

                    <div class="small text-muted mb-2">
                        Total Mahasiswa
                    </div>

                    <h2 class="fw-bold text-primary">
                        {{ $total_mahasiswa }}
                    </h2>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card live-card border-0 rounded-4 h-100">

                <div class="card-body">

                    <div class="small text-muted mb-2">
                        Total Tahfidz
                    </div>

                    <h2 class="fw-bold text-success">
                        {{ $total_tahfidz }}
                    </h2>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card live-card border-0 rounded-4 h-100">

                <div class="card-body">

                    <div class="small text-muted mb-2">
                        Total Portofolio
                    </div>

                    <h2 class="fw-bold text-warning">
                        {{ $total_portofolio }}
                    </h2>

                </div>

            </div>
        </div>

        <div class="col-md-3">
            <div class="card live-card border-0 rounded-4 h-100">

                <div class="card-body">

                    <div class="small text-muted mb-2">
                        Total Sosial Masyarakat
                    </div>

                    <h2 class="fw-bold text-danger">
                        {{ $total_masyarakat }}
                    </h2>

                </div>

            </div>
        </div>

    </div>
    {{-- QUICK ACCESS --}}
<div class="row g-4 mb-4">

    <div class="col-md-6">

        <a href="{{ route('inventaris.index') }}"
           class="text-decoration-none">

            <div class="card border-0 rounded-4 h-100 quick-card">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center">

                        <div class="quick-icon bg-primary-subtle">
                            <i class="fas fa-boxes text-primary"></i>
                        </div>

                        <div class="ms-3">

                            <h5 class="fw-bold mb-1">
                                Inventaris Asrama
                            </h5>

                            <small class="text-muted">
                                Kelola data inventaris dan aset asrama
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </a>

    </div>

    <div class="col-md-6">

        <a href="{{ route('leaderboard') }}"
           class="text-decoration-none">

            <div class="card border-0 rounded-4 h-100 quick-card">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center">

                        <div class="quick-icon bg-success-subtle">
                            <i class="fas fa-trophy text-success"></i>
                        </div>

                        <div class="ms-3">

                            <h5 class="fw-bold mb-1">
                                Leaderboard Spiritual
                            </h5>

                            <small class="text-muted">
                                Lihat ranking perkembangan spiritual mahasiswa
                            </small>

                        </div>

                    </div>

                </div>

            </div>

        </a>

    </div>

</div>



</div>
</div>

<x-footer></x-footer>