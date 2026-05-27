<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;
use Illuminate\Http\Request;

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

        $u = User::with('mahasiswaProfile')
            ->where('id', $id_user)
            ->first();

        if (!$u) {
            abort(404, 'User tidak ditemukan.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));

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

        $tahun_mulai = \Carbon\Carbon::parse($u->created_at)->year;
        $tahun_akhir = $tahun_mulai + 4;

        if ($tahun < $tahun_mulai || $tahun > $tahun_akhir) {
            $tahun = $tahun_mulai;
        }

        $data_db = [];

        if ($role_user == 'mahasiswa') {
            $rows = Amalan::where('id_user', $id_user)
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

        $awardees = collect();

        if ($role_user != 'mahasiswa') {
            $awardees = User::with('mahasiswaProfile')
                ->where('role', 'mahasiswa')
                ->orderBy('name')
                ->get();
        }

        return view('amalan', compact(
            'u',
            'role_user',
            'bulan',
            'tahun',
            'tahun_mulai',
            'tahun_akhir',
            'jumlah_hari',
            'foto_path',
            'list_amalan',
            'data_db',
            'theme',
            'accent_color',
            'awardees',
        ));
    }

    public function update(Request $request)
    {
        if (!session()->has('id_user')) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'kolom' => 'required|string',
            'nilai' => 'required|integer|min:0|max:5',
        ]);

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

        Amalan::updateOrCreate(
            [
                'id_user' => $id_user,
                'tanggal' => $tanggal,
            ],
            [
                $kolom => $nilai,
            ]
        );

        return response()->json(['status' => 'success']);
    }
}