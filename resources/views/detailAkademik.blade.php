<x-header
    title="Monitoring Akademik - Bridge"
    css="css/detailAkademik.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">

    <div class="topbar">

        <h2>
            Monitoring Akademik
        </h2>

        <a
            href="{{ route('mahasiswa.detail',$mahasiswa->id) }}"
            class="btn btn-outline-secondary">

            <i class="fas fa-arrow-left"></i>
            Kembali

        </a>

    </div>
    <div class="top-card">

        <div class="awardee-info">

            <img
                src="{{ asset('uploads/profile/' . ($mahasiswa->mahasiswaProfile->foto_profil ?? 'default.png')) }}"
                class="awardee-photo"
            >

            <div>

                <div class="awardee-name">
                    {{ $mahasiswa->name }}
                </div>

                <div class="awardee-campus">
                    {{ $mahasiswa->mahasiswaProfile->universitas ?? '-' }}
                </div>
                <div class="awardee-campus">
                    NIBS: {{ $mahasiswa->mahasiswaProfile->nibs ?? '-' }}
                </div>

            </div>

        </div>

    </div>
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="stat-card">
            <h3>{{ $totalSemester }}</h3>
            <p>Total Semester</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h3>{{ number_format($ipk,2) }}</h3>
            <p>IPK Kumulatif</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h3>{{ $toeflTertinggi ?? '-' }}</h3>
            <p>TOEFL Tertinggi</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <h3>{{ $statusAkademik }}</h3>
            <p>Status Akademik</p>
        </div>
    </div>

</div>
<div class="card-custom">

    <h5 class="section-title">
        Grafik Perkembangan IP
    </h5>

    <canvas id="ipChart"></canvas>

</div>
<div class="card-custom">

<h5 class="section-title">
    Riwayat IP Semester
</h5>

<table class="table table-hover">

<thead>
<tr>
    <th>Semester</th>
    <th>IP</th>
    <th>Bukti</th>
</tr>
</thead>

<tbody>

@foreach($riwayatAkademik as $row)

<tr>

    <td>
        Semester {{ $row->semester }}
    </td>

    <td class="fw-bold text-primary">
        {{ number_format($row->ip,2) }}
    </td>

    <td>

        @if($row->file_verifikasi)

            <a
                href="{{ asset('uploads/khs/'.$row->file_verifikasi) }}"
                target="_blank"
                class="btn btn-sm btn-outline-secondary">

                File

            </a>

        @endif

    </td>

</tr>

@endforeach

</tbody>

</table>

</div>
<div class="card-custom">

<h5 class="section-title">
    Riwayat TOEFL
</h5>

<table class="table table-hover">

<thead>

<tr>
    <th>Jenis Tes</th>
    <th>Score</th>
    <th>Sertifikat</th>
</tr>

</thead>

<tbody>

@foreach($riwayatToefl as $row)

<tr>

    <td>
        {{ $row->jenis_tes }}
    </td>

    <td class="fw-bold text-success">
        {{ $row->score }}
    </td>

    <td>

        @if($row->file_sertifikat)

            <a
                href="{{ asset('uploads/toefl/'.$row->file_sertifikat) }}"
                target="_blank"
                class="btn btn-sm btn-outline-secondary">

                File

            </a>

        @endif

    </td>

</tr>

@endforeach

</tbody>

</table>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx =
document.getElementById('ipChart');

new Chart(ctx, {

    type: 'line',

    data: {

        labels: [
            @foreach($riwayatAkademik as $row)
                'Semester {{ $row->semester }}',
            @endforeach
        ],

        datasets: [{
            data: [
                @foreach($riwayatAkademik as $row)
                    {{ $row->ip }},
                @endforeach
            ],
            tension:0.4,
            fill:true,
            borderWidth:3
        }]
    },

    options:{
        plugins:{
            legend:{
                display:false
            }
        }
    }
});

</script>

<x-footer></x-footer>