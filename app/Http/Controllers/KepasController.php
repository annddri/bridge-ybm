<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tahfidz;
use App\Models\Portofolio;
use App\Models\Masyarakat;

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
}   