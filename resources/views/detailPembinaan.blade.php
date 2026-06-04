<x-header
    title="Detail Pembinaan - Bridge"
    css="css/detailTahfidz.css"
></x-header>

<x-sidebarKepas
    :u="$u"
    :foto-path="$foto_path"
></x-sidebarKepas>

<div class="main-content">
    <div class="topbar">

        <h2>Monitoring Pembinaan</h2>

        <a
            href="{{ route('mahasiswa.detail', $mahasiswa->id) }}"
            class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>

    </div>

    {{-- Banner profil mahasiswa --}}
    <div class="top-card">
        <div class="awardee-info">
            <img
                src="{{ asset('uploads/profile/' . ($mahasiswa->mahasiswaProfile->foto_profil ?? 'default.png')) }}"
                class="awardee-photo"
            >
            <div>
                <div class="awardee-name">{{ $mahasiswa->name }}</div>
                <div class="awardee-campus">
                    {{ $mahasiswa->mahasiswaProfile->universitas ?? '-' }}
                </div>
                <div class="awardee-campus">
                    NIBS: {{ $mahasiswa->mahasiswaProfile->nibs ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel data pembinaan --}}
    <div class="table-card">
        <table class="table table-hover">

            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Pemateri</th>
                    <th>Judul Materi</th>
                    <th class="text-center">Resume</th>
                </tr>
            </thead>

            <tbody>
                @forelse($dataPembinaan as $row)
                    <tr>
                        <td class="text-muted small">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                        </td>

                        <td class="fw-semibold">
                            {{ $row->nama_pemateri }}
                        </td>

                        <td>
                            <span title="{{ $row->judul_materi }}">
                                {{ $row->judul_materi }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($row->resume)
                                <a
                                    href="{{ asset('uploads/pembinaan/' . $row->resume) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-file-alt me-1"></i> File
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="fas fa-chalkboard-teacher d-block fs-3 mb-2 opacity-25"></i>
                            Belum ada data pembinaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

<x-footer></x-footer>
