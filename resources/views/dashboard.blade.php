<x-header title="Dashboard - Bridge" css="css/dashboard.css"></x-header>

<x-sidebar 
    :u="$u" 
    :role-user="$role_user" 
    :foto-path="$foto_path" 
></x-sidebar>

<div class="main-content">
    
    <div class="content-body">
        
        <div class="card welcome-box border-0 mb-4 rounded-4">
            <div class="card-body p-4">
                <h4 class="fw-bold m-0" style="color: var(--navy-theme); letter-spacing: 0.5px;">
                    Hallo, Selamat Datang di Aplikasi BRIDGE
                </h4>
                <p class="text-muted small m-0 mt-1">Sistem informasi monitoring perkembangan program beasiswa, aktivitas keasramaan, dan capaian akademik mandiri.</p>
            </div>
        </div>

        <div class="row">
            
            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-4" style="letter-spacing: 0.5px;">
                                <i class="fas fa-chart-pie text-primary me-2"></i>Spiritual Score - {{ $nama_bulan_ini }}
                            </h6>
                            
                            <div style="position: relative; height: 170px; width: 170px; margin: 0 auto;">
                                <canvas id="spiritualChart"></canvas>
                                <div style="position: absolute; width: 100%; top: 50%; left: 0; transform: translateY(-50%); text-align: center;">
                                    <h3 class="fw-bold m-0" style="color: var(--navy-theme); font-size: 1.8rem;">{{ $score_spiritual }}%</h3>
                                    <small class="text-muted fw-semibold" style="font-size: 0.65rem;">Target</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-muted small mb-3" style="font-size: 0.76rem; line-height: 1.4;">Akumulasi otomatis rata-rata seluruh amalan yaumiyah kamu.</p>
                            <a href="/amalan" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold w-100" style="font-size: 0.78rem; background-color: var(--navy-theme); border-color: var(--navy-theme);">
                                <i class="fas fa-pray me-1"></i> Isi Amalan Hari Ini
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px;">
                                <i class="fas fa-book-quran text-success me-2"></i>Tahfidz Tracker
                            </h6>
                            <div class="p-3 bg-light rounded-3 mb-2" style="border: 1px solid rgba(0,0,0,0.03);">
                                <small class="text-muted d-block">Capaian Hafalan Saat Ini:</small>
                                <span class="fs-4 fw-bold" style="color: var(--navy-theme);">Juz 30</span>
                            </div>
                            <p class="text-muted small" style="font-size: 0.78rem;">Sistem perekaman kuantitas setoran hafalan baru (Ziyadah) dan pengulangan (Murojaah).</p>
                        </div>
                        <a href="/tahfidz" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold w-100" style="font-size: 0.78rem;">Buka Tahfidz</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card live-card border-0 rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing: 0.5px;">
                                <i class="fas fa-graduation-cap text-warning me-2"></i>Indeks Prestasi Kumulatif
                            </h6>
                            <div class="p-3 bg-light rounded-3 mb-2" style="border: 1px solid rgba(0,0,0,0.03);">
                                <small class="text-muted d-block">IPK Terakhir Terdata:</small>
                                <span class="fs-4 fw-bold" style="color: var(--navy-theme);">3.75 <span class="fs-6 text-muted fw-normal">/ 4.00</span></span>
                            </div>
                            <p class="text-muted small" style="font-size: 0.78rem;">Riwayat pengumpulan data Kartu Hasil Studi (KHS) mahasiswa awardee aktif per semester.</p>
                        </div>
                        <a href="/akademik" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-semibold w-100" style="font-size: 0.78rem; color: #d97706; border-color: #f59e0b;">Buka Akademik</a>
                    </div>
                </div>
            </div>

        </div> </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('spiritualChart');

    if (!canvas) return;

    const scoreTercapai = Number({{ $score_spiritual ?? 0 }});

    const scoreSisa = Math.max(
        0,
        100 - scoreTercapai
    );

    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',

        data: {
            labels: [
                'Target Tercapai',
                'Sisa Target'
            ],

            datasets: [{
                data: [
                    scoreTercapai,
                    scoreSisa
                ],

                backgroundColor: [
                    '#063255',
                    '#f1f5f9'
                ],

                borderWidth: 0,
                hoverOffset: 3
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            cutout: '84%',

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            return (
                                context.label +
                                ': ' +
                                context.raw +
                                '%'
                            );

                        }
                    }
                }

            }
        }
    });

});
</script>

<x-footer></x-footer>