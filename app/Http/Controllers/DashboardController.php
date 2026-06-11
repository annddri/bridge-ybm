<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;
use App\Models\Tahfidz;
use App\Models\Akademik;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'mahasiswa') {
            abort(403, 'Akses ditolak. Anda bukan mahasiswa.');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $u = User::with('mahasiswaProfile')
            ->where('id', $id_user)
            ->first();

        if (!$u) {
            abort(404, 'User tidak ditemukan.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));


$bulan = now()->month;
$tahun = now()->year;

$jumlah_hari = cal_days_in_month(
    CAL_GREGORIAN,
    $bulan,
    $tahun
);

$rows = Amalan::where('id_user', $id_user)
    ->whereMonth('tanggal', $bulan)
    ->whereYear('tanggal', $tahun)
    ->get();
        
$list_amalan = [
    'shalat_5_waktu'  => ['tipe'=>'harian','target'=>5],
    'shalat_malam'    => ['tipe'=>'bulanan','target'=>10],
    'dzikir_pagi'     => ['tipe'=>'harian','target'=>1],
    'mendoakan_orang' => ['tipe'=>'harian','target'=>1],
    'shalat_dhuha'    => ['tipe'=>'harian','target'=>1],
    'membaca_alquran' => ['tipe'=>'harian','target'=>1],
    'shaum_sunnah'    => ['tipe'=>'bulanan','target'=>3],
    'berinfak'        => ['tipe'=>'harian','target'=>1],
];

$totalPersen = 0;

foreach ($list_amalan as $kolom => $attr) {

    $total_input = 0;

    foreach ($rows as $row) {
        $total_input += (int) ($row->$kolom ?? 0);
    }

    if ($attr['tipe'] === 'harian') {

        $persen =
            ($total_input /
            ($jumlah_hari * $attr['target'])) * 100;

    } else {

        $persen =
            ($total_input /
            $attr['target']) * 100;
    }

    $persen = min($persen, 100);

    $totalPersen += $persen;
}

$score_spiritual =
    round(
        $totalPersen / count($list_amalan),
        1
    );

$score_sisa =
    100 - $score_spiritual;

    $nama_bulan_ini =
        now()->translatedFormat('F Y');

        // ---- Data Tahfidz ----
        $tahfidz_terbaru = Tahfidz::where('id_user', $id_user)
            ->orderBy('tanggal_tes', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $total_tahfidz = Tahfidz::where('id_user', $id_user)->count();

        // ---- Data Akademik (IPK) ----
        $ipk = Akademik::where('id_user', $id_user)->avg('ip');
        $ipk = $ipk ? round($ipk, 2) : null;

        $akademik_terbaru = Akademik::where('id_user', $id_user)
            ->orderBy('semester', 'desc')
            ->first();

        $ip_terbaru      = $akademik_terbaru->ip ?? null;
        $semester_terbaru = $akademik_terbaru->semester ?? null;

        return view('dashboard', compact(
            'u',
            'role_user',
            'foto_path',
            'nama_bulan_ini',
            'score_spiritual',
            'tahfidz_terbaru',
            'total_tahfidz',
            'ipk',
            'ip_terbaru',
            'semester_terbaru',
        ));
    }
}