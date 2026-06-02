<x-header
    title="Monitoring Sosial Masyarakat - Bridge"
    css="css/detailMasyarakat.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">
    <div class="topbar">

        <h2>
            Monitoring Sosial Masyarakat
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

    @php

        $kategori = [
            '📍 Kunjungan Program' => $kunjungan,
            '🌱 Social Project' => $socialProject,
            '🎤 Narasumber Pembinaan' => $narasumber
        ];

    @endphp

@foreach($kategori as $judul => $data)

<div class="card-custom">

    <div class="section-title">
        {{ $judul }}
    </div>

    <div class="table-responsive">

        <table class="table-custom">

            <thead>

                <tr>

                    <th>Waktu</th>

                    @if($judul == '📍 Kunjungan Program')

                        <th>Daftar Kunjungan</th>
                        <th>Lokasi</th>

                    @elseif($judul == '🌱 Social Project')

                        <th>Nama Social Project</th>
                        <th>Sasaran</th>

                    @else

                        <th>Judul Materi</th>
                        <th>Jumlah Peserta</th>

                    @endif

                    <th>Laporan</th>

                </tr>

            </thead>

            <tbody>

                @forelse($data as $row)

                    <tr>

                        <td>
                            {{ $row->waktu }}
                        </td>

                            <td>
                                {{ $row->lokasi_sasaran_peserta }}
                            </td>

                            <td>
                                {{ $row->kunjungan_sospro_materi }}
                            </td>


                        <td class="text-center">

                            @if($row->link_laporan)

                                <a
                                    href="{{ $row->link_laporan }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary rounded-pill">

                                    <i class="fas fa-link me-1"></i>
                                    Lihat

                                </a>

                            @else

                                <span class="text-muted">
                                    Tidak ada
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">

                            <div class="empty-state">
                                Belum ada data
                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endforeach

</div>

<x-footer></x-footer>