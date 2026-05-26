<div class="sidebar shadow">
    <div class="sidebar-brand">
        <img src="{{ $fotoPath  }}?t={{ time() }}" alt="Profile" class="brand-logo shadow">
        <div>
            <h5 class="fw-bold m-0 text-white fs-6">{{ $u->name }}</h5>
            <small class="text-info fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem; display: block; margin-top: 3px;">
                {{ $u->role }}
            </small>
        </div>
    </div>

    <div class="mt-3">
        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Home
        </a>

        <a href="/profile" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Profil Saya
        </a>

        @if ($roleUser !== 'mahasiswa')
            <a href="/data-awardee" class="nav-link {{ request()->is('data-awardee') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Data Awardee
            </a>
        @endif

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Monitoring
        </div>

        <a href="/amalan" class="nav-link {{ request()->is('amalan') ? 'active' : '' }}">
            <i class="fas fa-pray"></i> Spiritual Tracker
        </a>

        <a href="/tahfidz" class="nav-link {{ request()->is('tahfidz') ? 'active' : '' }}">
            <i class="fas fa-book-quran"></i> Tahfidz Tracker
        </a>

        <a href="/akademik" class="nav-link {{ request()->is('akademik') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap"></i> Akademik
        </a>

        <a href="/portofolio" class="nav-link {{ request()->is('portofolio') ? 'active' : '' }}">
            <i class="fas fa-award"></i> Portofolio
        </a>

        <a href="/masyarakat" class="nav-link {{ request()->is('masyarakat') ? 'active' : '' }}">
            <i class="fas fa-people-group"></i> Sosial Masyarakat
        </a>

        <div class="px-4 py-2 small text-uppercase fw-bold text-white-50" style="letter-spacing: 1px; margin-top: 15px; margin-bottom: 5px; font-size: 0.75rem;">
            Fitur Asrama
        </div>

        <a href="/inventaris" class="nav-link {{ request()->is('inventaris') ? 'active' : '' }}">
            <i class="fas fa-boxes-stacked"></i> Inventaris Asrama
        </a>

        <a href="/keuangan" class="nav-link {{ request()->is('keuangan') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> Keuangan Asrama
        </a>

        <a href="{{ route('logout') }}" class="nav-link logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </div>
</div>