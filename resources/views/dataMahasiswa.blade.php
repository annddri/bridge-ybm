<x-header title="Data Mahasiswa - Bridge" css="css/dataMahasiswa.css"></x-header>
<x-sidebarKepas
    :u="$u"
    :fotoPath="$foto_path"
></x-sidebarKepas>

<div class="main-content">

    <div class="page-header">
        
    <div class="page-header-content">

        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>
            Data Mahasiswa
        </h1>

        <div class="page-subtitle">
            Monitoring mahasiswa binaan asrama.
        </div>

        <div class="badge-asrama">

            <i class="fas fa-building me-2"></i>

            {{ $u->kepasProfile->asrama->nama_asrama ?? '-' }}

        </div>

    </div>

</div>

<table class="table align-middle">

    <thead>

    <tr>

        <th>No</th>
        <th>Mahasiswa</th>
        <th>NIBS</th>
        <th>Universitas</th>
        <th>Angkatan</th>
        <th>Aksi</th>

    </tr>

    </thead>

    <tbody>

    @forelse($mahasiswa as $row)

        <tr>

            <td>
                {{ $loop->iteration }}
            </td>

            <td>

                <div class="awardee-profile">

                    <img
                        src="{{ asset('uploads/profile/' . ($row->mahasiswaProfile->foto_profil ?? 'default.png')) }}"
                        class="avatar-member">

                    <div>

                        <div class="awardee-name">
                            {{ $row->name }}
                        </div>

                        <div class="awardee-email">
                            {{ $row->email }}
                        </div>

                    </div>

                </div>

            </td>

            <td>
                {{ $row->mahasiswaProfile->nibs ?? '-' }}
            </td>

            <td>
                {{ $row->mahasiswaProfile->universitas ?? '-' }}
            </td>

            <td>
                {{ $row->mahasiswaProfile->angkatan ?? '-' }}
            </td>

            <td>

                <a href="{{ route('mahasiswa.detail', $row->id) }}"
                   class="btn-monitor">

                    <i class="fas fa-eye me-2"></i>

                    Detail

                </a>

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="6">

                <div class="empty-state">

                    Belum ada mahasiswa
                    pada asrama ini.

                </div>

            </td>

        </tr>

    @endforelse

    </tbody>

</table>

</div>
<x-footer></x-footer>