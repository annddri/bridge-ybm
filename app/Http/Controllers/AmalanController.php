<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmalanController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $u = DB::table('users')
            ->leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'mahasiswa_profiles.foto_profil as foto_profil'
            )
            ->where('users.id', $id_user)
            ->first();

        $foto_path = asset('assets/img/' . ($u->foto_profil ?? 'default.png'));

        $list_amalan = [
            'shalat_5_waktu'  => ['nama' => 'Shalat Berjamaah 5 Waktu', 'tipe' => 'harian', 'target' => 5, 'unit' => '/hari'],
            'shalat_malam'    => ['nama' => 'Shalat Malam/Qiyamul Lail', 'tipe' => 'bulanan', 'target' => 10, 'unit' => '/bln'],
            'dzikir_pagi'     => ['nama' => 'Dzikir Pagi', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
            'mendoakan_orang' => ['nama' => 'Mendoakan/memaafkan orang', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
            'shalat_dhuha'    => ['nama' => 'Shalat Dhuha', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
            'membaca_alquran' => ['nama' => 'Membaca Al-Quran', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
            'shaum_sunnah'    => ['nama' => 'Shaum Sunnah', 'tipe' => 'bulanan', 'target' => 3, 'unit' => '/bln'],
            'berinfak'        => ['nama' => 'Berinfak', 'tipe' => 'harian', 'target' => 1, 'unit' => '/hari'],
        ];

        $data_db = [];

        if ($role_user == 'mahasiswa') {
            $rows = DB::table('amalan')
                ->where('id_user', $id_user)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();

            foreach ($rows as $row) {
                $d = (int) date('d', strtotime($row->tanggal));

                foreach ($list_amalan as $key => $val) {
                    $data_db[$key][$d] = $row->$key ?? '';
                }
            }
        }

        $role_colors = [
            'mahasiswa' => 'primary',
            'pengurus' => 'success',
            'administrator' => 'danger',
        ];

        $theme = $role_colors[$role_user] ?? 'secondary';
        $accent_color = ($role_user == 'mahasiswa') ? '#0d6efd' : '#063255';

        $awardees = [];

        if ($role_user != 'mahasiswa') {
            $awardees = DB::table('users')
                ->leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
                ->select(
                    'users.id',
                    'users.name',
                    'mahasiswa_profiles.universitas',
                    'mahasiswa_profiles.angkatan'
                )
                ->where('users.role', 'mahasiswa')
                ->orderBy('users.name')
                ->get();
        }

        return view('amalan', compact(
            'u',
            'role_user',
            'bulan',
            'tahun',
            'jumlah_hari',
            'foto_path',
            'list_amalan',
            'data_db',
            'theme',
            'accent_color',
            'awardees'
        ));
    }

    public function update(Request $request)
    {
        if (!session()->has('id_user')) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $id_user = session('id_user');

        $tanggal = $request->tanggal;
        $kolom = $request->kolom;
        $nilai = (int) $request->nilai;

        $allowedColumns = [
            'shalat_5_waktu',
            'shalat_malam',
            'dzikir_pagi',
            'mendoakan_orang',
            'shalat_dhuha',
            'membaca_alquran',
            'shaum_sunnah',
            'berinfak',
        ];

        if (!in_array($kolom, $allowedColumns)) {
            return response()->json(['status' => 'invalid column'], 422);
        }

        DB::table('amalan')->updateOrInsert(
            [
                'id_user' => $id_user,
                'tanggal' => $tanggal,
            ],
            [
                $kolom => $nilai,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['status' => 'success']);
    }
}