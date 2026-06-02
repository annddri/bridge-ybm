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

    {{-- LAPORAN
    <div class="row g-4 mb-4">

        <div class="col-md-6">

            <div class="card border-0 rounded-4 shadow-sm h-100">

                <div class="card-body">

                    <h6 class="fw-bold mb-3">
                        Laporan Harian
                    </h6>

                    <div class="text-center py-4">

                        <i class="fas fa-calendar-day fs-1 text-muted mb-3"></i>

                        <div class="fw-semibold">
                            Fitur Belum Tersedia
                        </div>

                        <small class="text-muted">
                            Akan dikembangkan pada tahap berikutnya.
                        </small>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card border-0 rounded-4 shadow-sm h-100">

                <div class="card-body">

                    <h6 class="fw-bold mb-3">
                        Laporan Bulanan
                    </h6>

                    <div class="text-center py-4">

                        <i class="fas fa-file-alt fs-1 text-muted mb-3"></i>

                        <div class="fw-semibold">
                            Fitur Belum Tersedia
                        </div>

                        <small class="text-muted">
                            Akan dikembangkan pada tahap berikutnya.
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div> --}}

    {{-- LEADERBOARD
    <div class="card border-0 rounded-4 shadow-sm">

        <div class="card-header bg-white border-0 pt-4">

            <h5 class="fw-bold mb-0">
                Leaderboard Spiritual Tracker
            </h5>

            <small class="text-muted">
                Ranking progress amalan mahasiswa.
            </small>

        </div>

        <div class="card-body">

            <div class="text-center py-5 text-muted">

                <i class="fas fa-trophy fs-1 mb-3"></i>

                <div>
                    Leaderboard akan ditampilkan setelah
                    perhitungan amalan selesai diintegrasikan.
                </div>

            </div>

        </div>

    </div>

</div> --}}

</div>

<x-footer></x-footer>