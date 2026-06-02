<x-header
    title="Leaderboard Spiritual - Bridge"
    css="css/leaderboard.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">

    <div class="page-header">

        <div>

            <h2 class="page-title">
                Leaderboard Spiritual
            </h2>

            <p class="page-subtitle">
                Ranking spiritual mahasiswa
            </p>

        </div>

        <div class="filter-group">

            <select
                id="bulan"
                class="form-select">

                @for($i=1;$i<=12;$i++)

                    <option
                        value="{{ $i }}"
                        {{ $bulan==$i?'selected':'' }}>

                        {{ DateTime::createFromFormat('!m',$i)->format('F') }}

                    </option>

                @endfor

            </select>

            <select
                id="tahun"
                class="form-select">

                @for($i=date('Y')-2;$i<=date('Y')+2;$i++)

                    <option
                        value="{{ $i }}"
                        {{ $tahun==$i?'selected':'' }}>

                        {{ $i }}

                    </option>

                @endfor

            </select>

        </div>

    </div>
    <div class="leaderboard-card">

    <input
        type="text"
        id="searchInput"
        class="form-control mb-3"
        placeholder="Cari mahasiswa...">

    <table class="table">

        <thead>

            <tr>

                <th>Rank</th>
                <th>Nama</th>
                <th>Universitas</th>
                <th>Angkatan</th>
                <th>Progress</th>
                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            @foreach($rankList as $index => $row)

            <tr>

                <td>

                    {{ $index + 1 }}

                </td>

                <td>

                    {{ $row['nama'] }}

                </td>

                <td>

                    {{ $row['universitas'] }}

                </td>

                <td>

                    {{ $row['angkatan'] }}

                </td>

                <td>

                    <strong>
                        {{ $row['skor'] }}%
                    </strong>

                </td>

                <td>

                    <a
                        href="{{ route('mahasiswa.amalan', $row['id']) }}"
                        class="btn btn-success btn-sm">

                        Lihat Rekap

                    </a>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>
<script>

document
.getElementById('bulan')
.addEventListener('change', reload);

document
.getElementById('tahun')
.addEventListener('change', reload);

function reload()
{
    let bulan =
        document.getElementById('bulan').value;

    let tahun =
        document.getElementById('tahun').value;

    window.location =
        `?bulan=${bulan}&tahun=${tahun}`;
}

document
.getElementById('searchInput')
.addEventListener('keyup', function(){

    let value =
        this.value.toLowerCase();

    document
    .querySelectorAll('tbody tr')
    .forEach(row => {

        row.style.display =
            row.innerText
            .toLowerCase()
            .includes(value)
            ? ''
            : 'none';
    });
});

</script>


    <x-footer></x-footer>