<x-header
    title="Monitoring Portofolio - Bridge"
    css="css/detailPortofolio.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">
    <div class="topbar">

        <h2>
            Monitoring Portofolio
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
            '🏆 Prestasi' => $prestasi,
            '👥 Organisasi' => $organisasi,
            '📚 Seminar / Workshop' => $workshop
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

                        <th>Nama Kegiatan</th>

                        <th>Penyelenggara / Jabatan</th>

                        <th>Level</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($data as $row)

                    <tr>

                        <td>
                            {{ $row->tanggal_tahun }}
                        </td>

                        <td>
                            <strong>
                                {{ $row->nama_kegiatan }}
                            </strong>
                        </td>

                        <td>
                            {{ $row->penyelenggara_jabatan }}
                        </td>

                        <td>

                            <span class="badge-level">

                                {{ $row->level }}

                            </span>

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

<x-footer />