<x-header title="Selamat Datang di Bridge" css="css/login.css"></x-header>

<div class="landing-wrapper">
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container w-100">
            <div class="row align-items-center justify-content-center">
                <!-- Left: Headline & Description -->
                <div class="col-lg-7 text-white text-center text-lg-start mb-5 mb-lg-0 hero-content">
                    <img src="{{ asset('img/New Logo YBM Secondary.png') }}" alt="YBM BRILiaN" class="img-fluid hero-logo mb-4">
                    <h1 class="display-4 fw-bold mb-3 hero-title">Membangun Generasi <br><span class="text-accent">Pemimpin Masa Depan</span></h1>
                    <p class="lead mb-4 hero-subtitle">
                        <strong>BRIDGE</strong> (BRIght Dormitory Growth Environment) adalah platform terintegrasi untuk memonitoring, mendampingi, dan memberdayakan Awardee Bright Scholarship.
                    </p>
                    <a href="#features" class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold explore-btn">
                        Pelajari Fitur <i class="fas fa-arrow-down ms-2"></i>
                    </a>
                </div>

                <!-- Right: Login Form -->
                <div class="col-lg-5">
                    <div class="card login-card border-0">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="fw-bold mb-1 text-primary-dark">Masuk ke Akun Anda</h4>
                            <p class="text-muted fs-7 mb-4">Silakan login untuk mengakses dashboard.</p>

                            @if (session('error'))
                                <div class="alert alert-danger text-center py-2 fs-7 rounded-3">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('login.process') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-secondary fs-7">Alamat Email</label>
                                    <div class="input-group login-input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary fs-7">Password</label>
                                    <div class="input-group login-input-group">
                                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                                    </div>
                                </div>
                                
                                <button type="submit" name="login" class="btn btn-custom-primary w-100 fw-bold text-white shadow-sm">
                                    Masuk ke Dashboard <i class="fas fa-sign-in-alt ms-1"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Background decorative elements -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="features-section py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h6 class="text-accent fw-bold text-uppercase tracking-wider">Fitur Utama</h6>
                <h2 class="fw-bold text-primary-dark">Ekosistem Pembinaan Komprehensif</h2>
                <p class="text-muted max-w-600 mx-auto mt-3">Bridge dirancang khusus dengan berbagai modul untuk memastikan setiap perkembangan awardee tercatat dan terukur dengan baik.</p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-blue-light text-blue mb-3">
                            <i class="fas fa-quran"></i>
                        </div>
                        <h5 class="fw-bold">Tahfidz & Amalan</h5>
                        <p class="text-muted fs-7">Tracking target hafalan Al-Quran bulanan serta pencatatan mutabaah yaumi secara rutin.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-orange-light text-orange mb-3">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h5 class="fw-bold">Akademik & Bahasa</h5>
                        <p class="text-muted fs-7">Monitoring perkembangan Indeks Prestasi (IP) per semester dan skor tes TOEFL.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-green-light text-green mb-3">
                            <i class="fas fa-award"></i>
                        </div>
                        <h5 class="fw-bold">Portofolio Prestasi</h5>
                        <p class="text-muted fs-7">Pencatatan rekam jejak prestasi, pengalaman organisasi, dan kompetisi yang diikuti.</p>
                    </div>
                </div>
                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-purple-light text-purple mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="fw-bold">Sosial Masyarakat</h5>
                        <p class="text-muted fs-7">Dokumentasi peran aktif awardee dalam kegiatan sosial dan pemberdayaan masyarakat.</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-1 justify-content-center">
                <!-- Feature 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-teal-light text-teal mb-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="fw-bold">Pembinaan Asrama</h5>
                        <p class="text-muted fs-7">Jadwal materi pembinaan, kehadiran, dan pengumpulan resume kegiatan secara terpusat.</p>
                    </div>
                </div>
                <!-- Feature 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card h-100">
                        <div class="icon-box bg-red-light text-red mb-3">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h5 class="fw-bold">Inventaris & Keuangan</h5>
                        <p class="text-muted fs-7">Manajemen barang fasilitas asrama serta transparansi pengelolaan dana kas dan operasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-primary-dark text-white text-center py-4">
        <p class="mb-0 fs-7 opacity-75">&copy; {{ date('Y') }} YBM BRILiaN. All rights reserved.</p>
    </footer>
</div>

<x-footer></x-footer>