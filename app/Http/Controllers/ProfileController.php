<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $u = User::with('mahasiswaProfile')
            ->where('id', $id_user)
            ->first();

        if (!$u) {
            abort(404, 'User tidak ditemukan di database.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));

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