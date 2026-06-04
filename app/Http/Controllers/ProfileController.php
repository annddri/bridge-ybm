<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MahasiswaProfile;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'mahasiswa') {
            abort(403);
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

    public function updateWhatsapp(Request $request)
    {
        if (!session()->has('id_user')) {
            return response()->json(['status' => 'unauthorized'], 401);
        }

        $request->validate([
            'no_telp' => [
                'required',
                'regex:/^08[0-9]{8,11}$/'
            ],
        ], [
            'no_telp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_telp.regex' => 'Nomor WhatsApp harus berupa 10-13 angka tanpa huruf atau karakter.'
        ]);

        MahasiswaProfile::updateOrCreate(
            ['user_id' => session('id_user')],
            ['no_telp' => $request->no_telp]
        );

        return response()->json([
            'status' => 'success',
            'no_telp' => $request->no_telp ?: 'Belum diisi',
        ]);
    }
}