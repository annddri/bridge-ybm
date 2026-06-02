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

            $skorTotal = 0;

            foreach ($list_amalan as $field => $target) {

                $jumlah = $records->sum($field);

                if ($records->count() > 0) {

                    $persen =
                        ($jumlah / $records->count())
                        / $target * 100;

                    $skorTotal += min(
                        $persen,
                        100
                    );
                }
            }

            $skorAkhir =
                round(
                    $skorTotal /
                    count($list_amalan),
                    1
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