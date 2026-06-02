<x-header
    title="Spiritual Tracker - Bridge"
    css="css/detailAmalan.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">

    <div class="topbar">

        <div>
            <h2 class="page-title">
                Monitoring Spiritual
            </h2>

            <p class="page-subtitle">
                Detail amalan harian mahasiswa
            </p>
            
        </div>

    </div>


    {{-- FILTER BULAN --}}

    <div class="card filter-card">

        <form method="GET" id="filterForm">

            <div class="row align-items-end">

                <div class="col-md-3">

                    <label>Bulan</label>

                    <select
                        name="bulan"
                        id="bulan"
                        class="form-select">

                        @for($i=1;$i<=12;$i++)
                            <option
                                value="{{ $i }}"
                                {{ $bulan == $i ? 'selected' : '' }}>

                                {{ DateTime::createFromFormat('!m',$i)->format('F') }}

                            </option>
                        @endfor

                    </select>

                </div>

                <div class="col-md-3">

                    <label>Tahun</label>

                    <select
                        name="tahun"
                        id="tahun"
                        class="form-select">

                        @foreach($daftarTahun as $th)
                            <option
                                value="{{ $th }}"
                                {{ $tahun == $th ? 'selected' : '' }}>

                                {{ $th }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-md-3 ms-auto text-end">

                    <a href="{{ route('mahasiswa.detail', $mahasiswa->id) }}"
                    class="btn btn-outline-secondary">

                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali

                    </a>

                </div>

            </div>

        </form>

    </div>

    {{-- TABEL MONITORING --}}

    <div class="chart-card mb-4">

        <h5 class="mb-2">
            Persentase Spiritual
        </h5>

        <h2 class="text-success fw-bold">
            {{ $persentaseKeseluruhan }}%
        </h2>

        <small class="text-muted">
            Bulan {{ DateTime::createFromFormat('!m',$bulan)->format('F') }}
            {{ $tahun }}
        </small>

    </div>

    <div class="card table-card">

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead>

                <tr>

                    <th rowspan="2">
                        Amalan
                    </th>

                    @for($d=1;$d<=$jumlah_hari;$d++)

                        <th>
                            {{ $d }}
                        </th>

                    @endfor

                    <th>%</th>

                </tr>

                </thead>

                <tbody>

                @foreach($list_amalan as $key => $amalan)

                    <tr>

                        <td class="fw-bold">
                            {{ $amalan['nama'] }}
                        </td>

                        @for($d=1;$d<=$jumlah_hari;$d++)

                            @php
                                $status =
                                $data_db[$key][$d] ?? 0;
                            @endphp

                            <td class="text-center">

                                @if($status)

                                    <span
                                        class="badge bg-success">

                                        ✓

                                    </span>

                                @else

                                    <span
                                        class="badge bg-light text-dark">

                                        -

                                    </span>

                                @endif

                            </td>

                        @endfor

                        <td class="fw-bold text-primary">

                            {{ $persentasePerAmalan[$key] ?? 0 }}%

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
<script>

document.getElementById('bulan')
.addEventListener('change', function () {

    document
        .getElementById('filterForm')
        .submit();

});

document.getElementById('tahun')
.addEventListener('change', function () {

    document
        .getElementById('filterForm')
        .submit();

});

</script>
<x-footer></x-footer>