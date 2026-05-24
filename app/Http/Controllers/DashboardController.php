<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $u = DB::table('users')->where('id', $id_user)->first();

        $foto_path = asset('uploads/profile/' . ($u->foto_profil ?? 'default.png'));

        $current_bulan = date('m');
        $nama_bulan_ini = date('F');

        // sementara dulu, nanti bisa dibuat fungsi hitung aslinya
        $score_spiritual = 0;
        $score_sisa = 100 - $score_spiritual;

        return view('dashboard', compact(
            'u',
            'role_user',
            'foto_path',
            'nama_bulan_ini',
            'score_spiritual',
            'score_sisa'
        ));
    }
}