<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tahfidz;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TahfidzController extends Controller
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
            abort(404, 'User tidak ditemukan.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));

        $query = Tahfidz::with('user');

        if ($role_user === 'mahasiswa') {
            $query->where('id_user', $id_user);
        }

        $data_tahfidz = $query
            ->orderBy('id', 'desc')
            ->get();

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

        Tahfidz::create([
            'id_user' => session('id_user'),
            'nama_surah' => $request->nama_surah,
            'tanggal_tes' => $request->tanggal_tes,
            'file_verifikasi' => $fileName,
            'status' => 'Belum Lulus',
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

        $tahfidz = Tahfidz::findOrFail($id);

        $tahfidz->update([
            'status' => $status,
        ]);

        return redirect('/tahfidz');
    }
}