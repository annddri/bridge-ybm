<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;
use App\Models\Tahfidz;
use App\Models\Akademik;
use App\Models\Portofolio;
use App\Models\Masyarakat;

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
                    $row->$key ?? '';
                    }
                    }
                    $totalKeseluruhan = 0;
                    $totalMaksimal = count($list_amalan) * $jumlah_hari;
            
                    $persentasePerAmalan = [];
            
                    foreach ($list_amalan as $key => $amalan) {
            
                        $jumlahCentang = 0;
            
                        for ($d = 1; $d <= $jumlah_hari; $d++) {
            
                            if (($data_db[$key][$d] ?? 0) == 1) {
                                $jumlahCentang++;
                            }
                        }
            
                        $persentasePerAmalan[$key] =
                            round(($jumlahCentang / $jumlah_hari) * 100, 2);
            
                        $totalKeseluruhan += $jumlahCentang;
                    }
            
                    $persentaseKeseluruhan =
                        $totalMaksimal > 0
                            ? round(
                                ($totalKeseluruhan / $totalMaksimal) * 100,
                                2
                            )
                            : 0;

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
}       