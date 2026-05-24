<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TahfidzController extends Controller
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
                'users.role',
                'mahasiswa_profiles.foto_profil as foto_profil'
            )
            ->where('users.id', $id_user)
            ->first();

        $foto_path = asset('assets/img/' . ($u->foto_profil ?? 'default.png'));

        $query = DB::table('tahfidz')
            ->join('users', 'tahfidz.id_user', '=', 'users.id')
            ->select('tahfidz.*', 'users.name');

        if ($role_user === 'mahasiswa') {
            $query->where('tahfidz.id_user', $id_user);
        }

        $data_tahfidz = $query->orderBy('tahfidz.id', 'desc')->get();

        return view('tahfidz', compact(
            'u',
            'role_user',
            'foto_path',
            'data_tahfidz'
        ));
    }

    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'nama_surah' => 'required|string|max:100',
            'tanggal_tes' => 'required|date',
            'file_verifikasi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $fileName = null;

        if ($request->hasFile('file_verifikasi')) {
            $file = $request->file('file_verifikasi');
            $fileName = 'tahfidz_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/tahfidz'), $fileName);
        }

        DB::table('tahfidz')->insert([
            'id_user' => session('id_user'),
            'nama_surah' => $request->nama_surah,
            'tanggal_tes' => $request->tanggal_tes,
            'file_verifikasi' => $fileName,
            'status' => 'Belum Lulus',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/tahfidz')->with('success', 'Setoran berhasil diunggah.');
    }

    public function updateStatus($id, $status)
    {
        if (session('role') !== 'kepala_asrama') {
            abort(403);
        }

        if (!in_array($status, ['Lulus', 'Belum Lulus'])) {
            abort(400);
        }

        DB::table('tahfidz')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);

        return redirect('/tahfidz');
    }
}