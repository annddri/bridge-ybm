<div class="sidebar shadow">


<div class="sidebar-brand">
    <img src="{{ $fotoPath  }}?t={{ time() }}"
         alt="Profile"
         class="brand-logo shadow">

    <div>
        <h5 class="fw-bold m-0 text-white fs-6">
            {{ $u->name }}
        </h5>

        <small
            class="text-info fw-bold text-uppercase"
            style="
                letter-spacing:0.5px;
                font-size:0.75rem;
                display:block;
                margin-top:3px;
            ">
            Kepala Asrama
        </small>
    </div>
</div>

<div class="mt-3">

    {{-- DASHBOARD --}}
    <a href="/kepas"
       class="nav-link {{ request()->is('kepas') ? 'active' : '' }}">
        <i class="fas fa-home"></i>
        Dashboard
    </a>

    <a href="/profile-kepas"
       class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
        <i class="fas fa-user-circle"></i>
        Profil Saya
    </a>

    {{-- MANAJEMEN --}}
    <div
        class="px-4 py-2 small text-uppercase fw-bold text-white-50"
        style="
            letter-spacing:1px;
            margin-top:15px;
            margin-bottom:5px;
            font-size:0.75rem;
        ">
        Manajemen
    </div>

    <a href="/data-mahasiswa"
       class="nav-link {{ request()->is('data-mahasiswa') ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        Data Mahasiswa
    </a>


<a href="{{ route('leaderboard') }}"
   class="nav-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
    <i class="fas fa-pray"></i>
    Leaderboard Spiritual
</a>    


    {{-- FITUR ASRAMA --}}
    <div
        class="px-4 py-2 small text-uppercase fw-bold text-white-50"
        style="
            letter-spacing:1px;
            margin-top:15px;
            margin-bottom:5px;
            font-size:0.75rem;
        ">
        Fitur Asrama
    </div>

    <a href="/inventaris"
       class="nav-link">
        <i class="fas fa-boxes-stacked"></i>
        Inventaris
    </a>

    {{-- <a href="/keuangan"
       class="nav-link">
        <i class="fas fa-wallet"></i>
        Keuangan
    </a> --}}

    {{-- LAPORAN
    <div
        class="px-4 py-2 small text-uppercase fw-bold text-white-50"
        style="
            letter-spacing:1px;
            margin-top:15px;
            margin-bottom:5px;
            font-size:0.75rem;
        ">
        Laporan
    </div>

    <a href="#"
       class="nav-link disabled opacity-50"
       onclick="return false;">
        <i class="fas fa-calendar-day"></i>
        Laporan Harian
        <span class="badge bg-secondary ms-auto">
            Soon
        </span>
    </a>

    <a href="#"
       class="nav-link disabled opacity-50"
       onclick="return false;">
        <i class="fas fa-file-alt"></i>
        Laporan Bulanan
        <span class="badge bg-secondary ms-auto">
            Soon
        </span>
    </a> --}}

    {{-- LOGOUT --}}
    <a href="/logout"
       class="nav-link logout-link"
       onclick="return confirm('Yakin ingin keluar?')">

        <i class="fas fa-sign-out-alt"></i>
        Keluar
    </a>

</div>
```

</div>
