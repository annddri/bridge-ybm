<x-header
    title="Tahfid Tracker - Bridge"
    css="css/detailTahfidz.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">
    <div class="topbar">

        <h2>
            Monitoring Tahfidz
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
    <div class="table-card">
    <table class="table table-hover">

        <thead>

        <tr>

            <th>Surah / Materi</th>

            <th>Tanggal Tes</th>

            <th>Bukti</th>

        </tr>

        </thead>

        <tbody>

        @forelse($dataTahfidz as $row)

            <tr>

                <td>

                    <span class="badge-surah">
                        {{ $row->nama_surah }}
                    </span>

                </td>

                <td>
                    {{ \Carbon\Carbon::parse($row->tanggal_tes)->format('d M Y') }}
                </td>

                <td>

                    @if($row->file_verifikasi)

                        <a
                            href="{{ asset('uploads/tahfidz/'.$row->file_verifikasi) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-secondary">

                            File

                        </a>

                    @endif

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="4" class="text-center">

                    Belum ada data tahfidz

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

    </div>


<x-footer></x-footer>
</div>