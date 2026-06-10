<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;
use App\Models\Tahfidz;
use App\Models\Akademik;
use App\Models\Portofolio;
use App\Models\Masyarakat;
use App\Models\Toefl;
use App\Models\Pembinaan;

class DataMahasiswaController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with([
            'kepasProfile.asrama'
        ])->findOrFail(session('id_user'));

        $asramaId = $u->kepasProfile->asrama_id;

        $mahasiswa = User::with('mahasiswaProfile')
            ->where('role', 'mahasiswa')
            ->whereHas('mahasiswaProfile', function ($q) use ($asramaId) {

                $q->where('asrama_id', $asramaId);

            })
            ->orderBy('name')
            ->get();

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        return view(
            'dataMahasiswa',
            compact(
                'u',
                'foto_path',
                'mahasiswa'
            )
        );
    }
    public function detail($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with('kepasProfile')
            ->findOrFail(session('id_user'));

        $mahasiswa = User::with('mahasiswaProfile')
            ->findOrFail($id);

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        return view(
            'detail',
            compact(
                'u',
                'mahasiswa',
                'foto_path'
            )
        );
    }
    public function detailAmalan($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with('kepasProfile')
            ->findOrFail(session('id_user'));

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        $mahasiswa = User::with('mahasiswaProfile')
            ->findOrFail($id);

        $bulan = request('bulan', date('m'));
        $tahun = request('tahun', date('Y'));

        $tahunAwal = $mahasiswa->mahasiswaProfile->angkatan;

        $daftarTahun = [];

        for ($i = 0; $i < 5; $i++) {
            $daftarTahun[] = $tahunAwal + $i;
        }

        $jumlah_hari = cal_days_in_month(
            CAL_GREGORIAN,
            $bulan,
            $tahun
        );

        $list_amalan = [
            'shalat_5_waktu' => [
                'nama' => 'Shalat 5 Waktu',
                'tipe' => 'harian',
                'target' => 5,
                'unit' => '/hari'
            ],

            'shalat_malam' => [
                'nama' => 'Shalat Malam',
                'tipe' => 'bulanan',
                'target' => 10,
                'unit' => '/bln'
            ],

            'dzikir_pagi' => [
                'nama' => 'Dzikir Pagi',
                'tipe' => 'harian',
                'target' => 1,
                'unit' => '/hari'
            ],

            'mendoakan_orang' => [
                'nama' => 'Memaafkan Orang',
                'tipe' => 'harian',
                'target' => 1,
                'unit' => '/hari'
            ],

            'shalat_dhuha' => [
                'nama' => 'Shalat Dhuha',
                'tipe' => 'harian',
                'target' => 1,
                'unit' => '/hari'
            ],

            'membaca_alquran' => [
                'nama' => 'Baca Al-Quran',
                'tipe' => 'harian',
                'target' => 1,
                'unit' => '/hari'
            ],

            'shaum_sunnah' => [
                'nama' => 'Shaum Sunnah',
                'tipe' => 'bulanan',
                'target' => 3,
                'unit' => '/bln'
            ],

            'berinfak' => [
                'nama' => 'Berinfak',
                'tipe' => 'harian',
                'target' => 1,
                'unit' => '/hari'
            ]
        ];

        $rows = Amalan::where('id_user', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $data_db = [];

        
        foreach ($rows as $row) {
            $d = (int) date(
        'd',
        strtotime($row->tanggal)
    );

    foreach ($list_amalan as $key => $val) {

        $data_db[$key][$d] =
            $row->$key ?? 0;
    }
}
            
            $persentasePerAmalan = [];

$totalPersen = 0;

foreach ($list_amalan as $key => $amalan) {

    $total_input = 0;

    for ($d = 1; $d <= $jumlah_hari; $d++) {

        $total_input +=
            (int) ($data_db[$key][$d] ?? 0);
    }

    if ($amalan['tipe'] === 'harian') {

        $persen =
            ($total_input /
            ($jumlah_hari * $amalan['target']))
            * 100;

    } else {

        $persen =
            ($total_input /
            $amalan['target'])
            * 100;
    }

    $persen = min($persen, 100);

    $persentasePerAmalan[$key] =
        round($persen, 2);

    $totalPersen += $persen;
}

$persentaseKeseluruhan =
    round(
        $totalPersen /
        count($list_amalan),
        2
    );

        return view(
            'detailAmalan',
            compact(
                'u',
                'foto_path',
                'mahasiswa',
                'bulan',
                'tahun',
                'daftarTahun',
                'jumlah_hari',
                'list_amalan',
                'data_db',
                'persentasePerAmalan',
                'persentaseKeseluruhan'
            )
        );
    }

    public function detailTahfidz($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with('kepasProfile')
            ->findOrFail(session('id_user'));

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        $mahasiswa = User::with('mahasiswaProfile')
            ->findOrFail($id);

        $dataTahfidz = Tahfidz::where(
                'id_user',
                $mahasiswa->id
            )
            ->orderByDesc('id')
            ->get();

        return view(
            'detailTahfidz',
            compact(
                'u',
                'foto_path',
                'mahasiswa',
                'dataTahfidz'
            )
        );
    }

    public function detailAkademik($id)
{
    if (!session()->has('id_user')) {
        return redirect('/login');
    }

    if (session('role') !== 'kepas') {
        abort(403);
    }

    $u = User::with('kepasProfile')
        ->findOrFail(session('id_user'));

    $foto_path = asset(
        'uploads/profile/' .
        ($u->kepasProfile->foto_profil ?? 'default.png')
    );

    $mahasiswa = User::with(
        'mahasiswaProfile'
    )->findOrFail($id);

    $riwayatAkademik =
        Akademik::where(
            'id_user',
            $mahasiswa->id
        )
        ->orderBy('semester')
        ->get();

$riwayatToefl =
    Toefl::where(
        'id_user',
        $mahasiswa->id
    )
    ->orderBy('id', 'desc')
    ->get();

    $ipk =
        Akademik::where(
            'id_user',
            $mahasiswa->id
        )
        ->avg('ip');

    $toeflTertinggi =
        Toefl::where(
            'id_user',
            $mahasiswa->id
        )
        ->max('score');

    $totalSemester =
        Akademik::where(
            'id_user',
            $mahasiswa->id
        )
        ->count();

    $statusAkademik = 'Baik';

    if ($ipk >= 3.75) {
        $statusAkademik = 'Sangat Baik';
    }
    elseif ($ipk < 3.00) {
        $statusAkademik = 'Perlu Perhatian';
    }

    return view(
        'detailAkademik',
        compact(
            'u',
            'foto_path',
            'mahasiswa',
            'riwayatAkademik',
            'riwayatToefl',
            'ipk',
            'toeflTertinggi',
            'totalSemester',
            'statusAkademik'
        )
    );
}
public function detailPortofolio($id)
{
    if (!session()->has('id_user')) {
        return redirect('/login');
    }

    if (session('role') !== 'kepas') {
        abort(403);
    }

    $u = User::with('kepasProfile')
        ->findOrFail(session('id_user'));

    $foto_path = asset(
        'uploads/profile/' .
        ($u->kepasProfile->foto_profil ?? 'default.png')
    );

    $mahasiswa = User::with('mahasiswaProfile')
        ->findOrFail($id);

    $prestasi = Portofolio::where(
        'id_user',
        $id
    )
    ->where('kategori', 'prestasi')
    ->orderBy('id', 'desc')
    ->get();

    $organisasi = Portofolio::where(
        'id_user',
        $id
    )
    ->where('kategori', 'organisasi')
    ->orderBy('id', 'desc')
    ->get();

    $workshop = Portofolio::where(
        'id_user',
        $id
    )
    ->where('kategori', 'workshop/seminar')
    ->orderBy('id', 'desc')
    ->get();

    return view(
        'detailPortofolio',
        compact(
            'u',
            'foto_path',
            'mahasiswa',
            'prestasi',
            'organisasi',
            'workshop'
        )
    );
}
public function detailMasyarakat($id)
{
    if (!session()->has('id_user')) {
        return redirect('/login');
    }

    if (session('role') !== 'kepas') {
        abort(403);
    }

    $u = User::with('kepasProfile')
        ->findOrFail(session('id_user'));

    $foto_path = asset(
        'uploads/profile/' .
        ($u->kepasProfile->foto_profil ?? 'default.png')
    );

    $mahasiswa = User::with('mahasiswaProfile')
        ->findOrFail($id);

    $kunjungan = Masyarakat::where(
        'id_user',
        $id
    )
    ->where('kategori', 'kunjungan')
    ->orderBy('id', 'desc')
    ->get();

    $socialProject = Masyarakat::where(
        'id_user',
        $id
    )
    ->where('kategori', 'social_project')
    ->orderBy('id', 'desc')
    ->get();

    $narasumber = Masyarakat::where(
        'id_user',
        $id
    )
    ->where('kategori', 'narasumber')
    ->orderBy('id', 'desc')
    ->get();

    return view(
        'detailMasyarakat',
        compact(
            'u',
            'foto_path',
            'mahasiswa',
            'kunjungan',
            'socialProject',
            'narasumber'
        )
    );
}

public function detailPembinaan($id)
{
    if (!session()->has('id_user')) {
        return redirect('/login');
    }

    if (session('role') !== 'kepas') {
        abort(403);
    }

    $u = User::with('kepasProfile')
        ->findOrFail(session('id_user'));

    $foto_path = asset(
        'uploads/profile/' .
        ($u->kepasProfile->foto_profil ?? 'default.png')
    );

    $mahasiswa = User::with('mahasiswaProfile')
        ->findOrFail($id);

    $dataPembinaan = Pembinaan::where('id_user', $id)
        ->orderBy('tanggal', 'desc')
        ->orderBy('id', 'desc')
        ->get();

    return view(
        'detailPembinaan',
        compact(
            'u',
            'foto_path',
            'mahasiswa',
            'dataPembinaan'
        )
    );
}
}