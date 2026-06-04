<x-header title="Monitoring Pembinaan - Bridge" css="css/pembinaan.css"></x-header>

<x-sidebar-kepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebar-kepas>

<div class="main-content">
    <div class="content-body">

        {{-- ===== Header ===== --}}
        <div class="tracker-header-box p-4 rounded-4 mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-chalkboard-teacher fs-3 me-3" style="color: var(--navy-theme);"></i>
                <div>
                    <h4 class="fw-bold m-0 header-title">MONITORING PEMBINAAN</h4>
                    <p class="m-0 header-subtitle">
                        Pantau kegiatan pembinaan seluruh mahasiswa di asrama kamu.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===== Statistik ===== --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-box">
                    <div class="stat-number">{{ $data_pembinaan->count() }}</div>
                    <div class="stat-label"><i class="fas fa-list me-1"></i> Total Catatan Pembinaan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
                    <div class="stat-number">{{ $daftar_mahasiswa->count() }}</div>
                    <div class="stat-label"><i class="fas fa-users me-1"></i> Mahasiswa di Asrama Kamu</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);">
                    <div class="stat-number">{{ $data_pembinaan->pluck('id_user')->unique()->count() }}</div>
                    <div class="stat-label"><i class="fas fa-user-check me-1"></i> Mahasiswa Aktif Input</div>
                </div>
            </div>
        </div>

        {{-- ===== Filter ===== --}}
        <div class="filter-bar mb-4 d-flex align-items-center gap-3 flex-wrap">
            <i class="fas fa-filter text-muted"></i>
            <label class="fw-semibold small text-muted mb-0">Filter Mahasiswa:</label>
            <select id="filter-mahasiswa" class="form-select form-select-sm rounded-pill" style="max-width:260px;"
                    onchange="filterTable(this.value)">
                <option value="">— Semua Mahasiswa —</option>
                @foreach ($daftar_mahasiswa as $mhs)
                    <option value="{{ $mhs->id }}">
                        {{ $mhs->name }}
                        @if($mhs->mahasiswaProfile?->nibs)
                            ({{ $mhs->mahasiswaProfile->nibs }})
                        @endif
                    </option>
                @endforeach
            </select>

            <label class="fw-semibold small text-muted mb-0 ms-2">Filter Bulan:</label>
            <input type="month"
                   id="filter-bulan"
                   class="form-control form-control-sm rounded-pill"
                   style="max-width:180px;"
                   onchange="filterTable()">

            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                    onclick="resetFilter()">
                <i class="fas fa-times me-1"></i> Reset
            </button>
        </div>

        {{-- ===== Tabel Monitoring ===== --}}
        <div class="card live-card border-0 p-4 rounded-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <h6 class="fw-bold mb-0" style="color: var(--navy-theme);">
                    <i class="fas fa-table me-1"></i> Data Pembinaan Seluruh Mahasiswa
                </h6>
                <span class="badge bg-primary rounded-pill px-3" id="total-badge">
                    {{ $data_pembinaan->count() }} data
                </span>
            </div>

            <hr class="text-muted opacity-25 my-3">

            <div class="table-responsive">
                <table class="table table-hover align-middle m-0" id="tabel-pembinaan">
                    <thead>
                        <tr class="small text-muted text-uppercase">
                            <th width="4%"  class="border-0 py-3 px-3">#</th>
                            <th width="20%" class="border-0 py-3">Mahasiswa</th>
                            <th width="11%" class="border-0 py-3">Tanggal</th>
                            <th width="20%" class="border-0 py-3">Nama Pemateri</th>
                            <th width="30%" class="border-0 py-3">Judul Materi</th>
                            <th width="15%" class="border-0 py-3 text-center">Resume</th>
                        </tr>
                    </thead>

                    <tbody id="tbody-pembinaan">
                        @forelse ($data_pembinaan as $index => $row)
                            <tr data-userid="{{ $row->id_user }}"
                                data-bulan="{{ \Carbon\Carbon::parse($row->tanggal)->format('Y-m') }}">

                                <td class="px-3 text-muted small">{{ $index + 1 }}</td>

                                {{-- Nama Mahasiswa --}}
                                <td>
                                    <div class="mhs-name-badge">
                                        <img src="{{ asset('uploads/profile/' . ($row->user->mahasiswaProfile->foto_profil ?? 'default.png')) }}"
                                             alt="foto"
                                             class="mhs-avatar">
                                        <div>
                                            <div class="fw-semibold small text-dark">
                                                {{ $row->user->name }}
                                            </div>
                                            <div class="text-muted" style="font-size:0.72rem;">
                                                {{ $row->user->mahasiswaProfile->nibs ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-muted small">
                                    {{ date('d M Y', strtotime($row->tanggal)) }}
                                </td>

                                <td class="fw-semibold small text-dark">
                                    {{ $row->nama_pemateri }}
                                </td>

                                <td>
                                    <span class="judul-cell d-block small" title="{{ $row->judul_materi }}">
                                        {{ $row->judul_materi }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @if ($row->resume)
                                        <a href="{{ asset('uploads/pembinaan/' . $row->resume) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-action">
                                            <i class="fas fa-file-alt me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-chalkboard-teacher fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada data pembinaan dari mahasiswa di asrama ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Empty state saat filter tidak ada hasil --}}
            <div id="empty-filter" class="text-center text-muted py-5 d-none">
                <i class="fas fa-search fs-2 d-block mb-2 opacity-25"></i>
                Tidak ada data yang cocok dengan filter yang dipilih.
            </div>

        </div>
    </div>
</div>

{{-- ===== JavaScript Filter ===== --}}
<script>
    function filterTable() {
        const filterUser  = document.getElementById('filter-mahasiswa').value;
        const filterBulan = document.getElementById('filter-bulan').value;
        const rows        = document.querySelectorAll('#tbody-pembinaan tr');
        let   visible     = 0;

        rows.forEach(row => {
            const userId = row.dataset.userid ?? '';
            const bulan  = row.dataset.bulan  ?? '';

            const matchUser  = !filterUser  || userId === filterUser;
            const matchBulan = !filterBulan || bulan === filterBulan;

            if (matchUser && matchBulan) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('total-badge').textContent = visible + ' data';
        document.getElementById('empty-filter').classList.toggle('d-none', visible > 0);
        document.getElementById('tabel-pembinaan').classList.toggle('d-none', visible === 0);
    }

    function resetFilter() {
        document.getElementById('filter-mahasiswa').value = '';
        document.getElementById('filter-bulan').value     = '';
        filterTable();
    }
</script>

<x-footer></x-footer>
