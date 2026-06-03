<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DanaKas;
use App\Models\Masyarakat;
use App\Models\Portofolio;
use App\Models\Tahfidz;
use App\Models\User;

class KepasController extends Controller
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
            ->find(session('id_user'));

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        $total_mahasiswa = User::where('role', 'mahasiswa')->count();

        $total_tahfidz = Tahfidz::count();

        $total_portofolio = Portofolio::count();

        $total_masyarakat = Masyarakat::count();

        $total_daily = 0;
        $total_monthly = 0;

        return view('kepas', compact(
            'u',
            'foto_path',
            'total_mahasiswa',
            'total_tahfidz',
            'total_portofolio',
            'total_masyarakat',
            'total_daily',
            'total_monthly'
        ));
    }
    public function keuangan()
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

    $data_kas = DanaKas::orderByDesc('tanggal')
        ->get();

    $total_masuk = DanaKas::where(
        'jenis_transaksi',
        'Masuk'
    )->sum('nominal');

    $total_keluar = DanaKas::where(
        'jenis_transaksi',
        'Keluar'
    )->sum('nominal');

    $saldo = $total_masuk - $total_keluar;

    return view('keuangan', [
        'u' => $u,
        'role_user' => 'kepas',
        'foto_path' => $foto_path,

        'data_kas' => $data_kas,

        'total_masuk' => $total_masuk,
        'total_keluar' => $total_keluar,
        'saldo' => $saldo,

        'readonly' => true
    ]);
}
}   