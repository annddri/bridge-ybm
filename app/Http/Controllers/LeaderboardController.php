<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;

class LeaderboardController extends Controller
{
    public function index()
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

        $bulan = request('bulan', date('m'));
        $tahun = request('tahun', date('Y'));

        $list_amalan = [
            'shalat_5_waktu' => 5,
            'shalat_malam' => 10,
            'dzikir_pagi' => 1,
            'mendoakan_orang' => 1,
            'shalat_dhuha' => 1,
            'membaca_alquran' => 1,
            'shaum_sunnah' => 3,
            'berinfak' => 1,
        ];

        $mahasiswaList = User::with('mahasiswaProfile')
            ->where('role', 'mahasiswa')
            ->get();

        $rankList = [];

        foreach ($mahasiswaList as $mhs) {

            $records = Amalan::where(
                'id_user',
                $mhs->id
            )
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

            $jumlah_hari = cal_days_in_month(
    CAL_GREGORIAN,
    $bulan,
    $tahun
);

$skorTotal = 0;

foreach ($list_amalan as $field => $target) {

    $jumlah = $records->sum($field);

    if (
        in_array(
            $field,
            [
                'shalat_malam',
                'shaum_sunnah'
            ]
        )
    ) {

        $persen =
            ($jumlah / $target)
            * 100;

    } else {

        $persen =
            ($jumlah /
            ($jumlah_hari * $target))
            * 100;
    }

    $persen = min($persen, 100);

    $skorTotal += $persen;
}

$skorAkhir =
    round(
        $skorTotal /
        count($list_amalan),
        2
    );

            $rankList[] = [
                'id' => $mhs->id,
                'nama' => $mhs->name,
                'universitas' =>
                    $mhs->mahasiswaProfile->universitas
                    ?? '-',
                'angkatan' =>
                    $mhs->mahasiswaProfile->angkatan
                    ?? '-',
                'skor' => $skorAkhir
            ];
        }

        usort(
            $rankList,
            fn($a, $b)
            => $b['skor'] <=> $a['skor']
        );

        return view(
            'leaderboard',
            compact(
                'u',
                'foto_path',
                'bulan',
                'tahun',
                'rankList'
            )
        );
    }
}