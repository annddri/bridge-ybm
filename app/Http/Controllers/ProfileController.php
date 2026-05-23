<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $u = DB::table('users')
            ->leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'mahasiswa_profiles.foto_profil as foto_profil',
                'mahasiswa_profiles.nibs',
                'mahasiswa_profiles.nim',
                'mahasiswa_profiles.universitas',
                'mahasiswa_profiles.prodi',
                'mahasiswa_profiles.angkatan',
                'mahasiswa_profiles.no_telp',
            )
            ->where('users.id', $id_user)
            ->first();

        if (!$u) {
            abort(404, 'User tidak ditemukan di database.');
        }

        $foto_path = asset('img/' . ($u->foto_profil ?? 'default.png'));

        $role_colors = [
            'mahasiswa' => 'primary',
            'pengurus' => 'success',
            'administrator' => 'danger',
        ];

        $theme = $role_colors[$u->role] ?? 'secondary';

        return view('profile', compact(
            'u',
            'role_user',
            'foto_path',
            'theme'
        ));
    }
}