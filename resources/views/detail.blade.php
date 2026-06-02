<x-header title="Detail Mahasiswa - Bridge" css="css/detailMahasiswa.css"></x-header>
<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">

    <div class="topbar">

        <h1 class="page-title">
            Monitoring Mahasiswa
        </h1>
    </div>

    <div class="monitor-card">

        <div class="awardee-banner">

            <div class="awardee-profile">

                <img
                    src="{{ asset('uploads/profile/' . ($mahasiswa->mahasiswaProfile->foto_profil ?? 'default.png')) }}"
                    class="awardee-img"
                >

                <div>

                    <div class="awardee-name">
                        {{ $mahasiswa->name }}
                    </div>

                    <div class="awardee-desc">
                        {{ $mahasiswa->mahasiswaProfile->universitas ?? '-' }}
                    </div>

                    <div class="badge-info">
                        <i class="fas fa-user-graduate"></i>
                        Mahasiswa BRIDGE
                    </div>

                </div>

            </div>

        </div>

        <div class="monitor-body">

            <h5 class="section-title">
                Menu Monitoring
            </h5>

            <div class="menu-grid">

                <a href="{{ route('mahasiswa.amalan', $mahasiswa->id) }}"
                   class="menu-item">

                    <div class="icon-wrap">
                        <i class="fas fa-pray"></i>
                    </div>

                    <div class="menu-title">
                        Spiritual
                    </div>

                    <div class="menu-subtitle">
                        Monitoring ibadah dan amalan
                    </div>

                </a>

                <a href="{{ route('mahasiswa.tahfidz', $mahasiswa->id) }}"
                   class="menu-item">

                    <div class="icon-wrap">
                        <i class="fas fa-book-quran"></i>
                    </div>

                    <div class="menu-title">
                        Tahfidz
                    </div>

                    <div class="menu-subtitle">
                        Monitoring hafalan
                    </div>

                </a>

                <a href="{{ route('mahasiswa.akademik', $mahasiswa->id) }}"
                   class="menu-item">

                    <div class="icon-wrap">
                        <i class="fas fa-graduation-cap"></i>
                    </div>

                    <div class="menu-title">
                        Akademik
                    </div>

                    <div class="menu-subtitle">
                        Monitoring akademik
                    </div>

                </a>

                <a href="{{ route('mahasiswa.portofolio', $mahasiswa->id) }}"
                   class="menu-item">

                    <div class="icon-wrap">
                        <i class="fas fa-award"></i>
                    </div>

                    <div class="menu-title">
                        Portofolio
                    </div>

                    <div class="menu-subtitle">
                        Monitoring prestasi
                    </div>

                </a>

                <a href="{{ route('mahasiswa.masyarakat', $mahasiswa->id) }}"
                   class="menu-item">

                    <div class="icon-wrap">
                        <i class="fas fa-people-group"></i>
                    </div>

                    <div class="menu-title">
                        Sosial
                    </div>

                    <div class="menu-subtitle">
                        Monitoring sosial
                    </div>

                </a>

            </div>

        </div>

    </div>

</div>
<x-footer></x-footer>