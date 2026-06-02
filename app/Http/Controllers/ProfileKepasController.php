<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KepasProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileKepasController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with([
            'kepasProfile.asrama'
        ])->findOrFail(session('id_user'));

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        return view('profileKepas', compact(
            'u',
            'foto_path'
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

        KepasProfile::updateOrCreate(
            ['user_id' => session('id_user')],
            ['no_telp' => $request->no_telp]
        );

        return response()->json([
            'status' => 'success',
            'no_telp' => $request->no_telp ?: 'Belum diisi',
        ]);
    }
}