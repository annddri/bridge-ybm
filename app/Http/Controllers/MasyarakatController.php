<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Masyarakat;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MasyarakatController extends Controller
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
            abort(404, 'User tidak ditemukan.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));

        $query = Masyarakat::with('user');

        if ($role_user === 'mahasiswa') {
            $query->where('id_user', $id_user);
        }

        $data_masyarakat = $query
            ->orderBy('id', 'desc')
            ->get();

        return view('masyarakat', compact(
            'u',
            'role_user',
            'foto_path',
            'data_masyarakat'
        ));
    }

    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'kategori' => 'required|in:kunjungan,social_project,narasumber',
            'waktu' => 'required|string|max:50',
            'lokasi_sasaran_peserta' => 'nullable|string|max:255',
            'kunjungan_sospro_materi' => 'required|string|max:255',
            'link_laporan' => 'nullable|url|max:255',
        ]);

        Masyarakat::create([
            'id_user' => session('id_user'),
            'kategori' => $request->kategori,
            'waktu' => $request->waktu,
            'lokasi_sasaran_peserta' => $request->lokasi_sasaran_peserta,
            'kunjungan_sospro_materi' => $request->kunjungan_sospro_materi,
            'link_laporan' => $request->link_laporan,
            'created_at' => now(),
        ]);

        return redirect('/masyarakat')->with('success', 'Data sosial masyarakat berhasil diajukan.');
    }
    
    public function destroy($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $masyarakat = Masyarakat::findOrFail($id);

        if (session('role') === 'mahasiswa' && $masyarakat->id_user != session('id_user')) {
            abort(403);
        }

        $masyarakat->delete();

        return redirect('/masyarakat')->with('success', 'Data sosial masyarakat berhasil dihapus.');
    }
}