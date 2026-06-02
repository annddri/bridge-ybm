<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Amalan;

class DataMahasiswaController extends Controller
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

        $asramaId = $u->kepasProfile->asrama_id;

        $mahasiswa = User::with('mahasiswaProfile')
            ->where('role', 'mahasiswa')
            ->whereHas('mahasiswaProfile', function ($q) use ($asramaId) {

                $q->where('asrama_id', $asramaId);

            })
            ->orderBy('name')
            ->get();

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        return view(
            'dataMahasiswa',
            compact(
                'u',
                'foto_path',
                'mahasiswa'
            )
        );
    }
    public function detail($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'kepas') {
            abort(403);
        }

        $u = User::with('kepasProfile')
            ->findOrFail(session('id_user'));

        $mahasiswa = User::with('mahasiswaProfile')
            ->findOrFail($id);

        $foto_path = asset(
            'uploads/profile/' .
            ($u->kepasProfile->foto_profil ?? 'default.png')
        );

        return view(
            'detail',
            compact(
                'u',
                'mahasiswa',
                'foto_path'
            )
        );
    }

}