<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MasyarakatController extends Controller
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

        $foto_path = asset('uploads/profile/' . ($u->foto_profil ?? 'default.png'));

        $query = DB::table('masyarakat')
            ->join('users', 'masyarakat.id_user', '=', 'users.id')
            ->select('masyarakat.*', 'users.name');

        if ($role_user === 'mahasiswa') {
            $query->where('masyarakat.id_user', $id_user);
        }

        $data_masyarakat = $query
            ->orderBy('masyarakat.id', 'desc')
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
            'lokasi_sasaran' => 'nullable|string|max:255',
            'nama_kegiatan_materi' => 'required|string|max:255',
            'keterangan_tambahan' => 'nullable|string',
            'link_laporan' => 'nullable|url|max:255',
        ]);

        DB::table('masyarakat')->insert([
            'id_user' => session('id_user'),
            'kategori' => $request->kategori,
            'waktu' => $request->waktu,
            'lokasi_sasaran' => $request->lokasi_sasaran,
            'nama_kegiatan_materi' => $request->nama_kegiatan_materi,
            'keterangan_tambahan' => $request->keterangan_tambahan,
            'link_laporan' => $request->link_laporan,
            'status' => 'Belum Lulus',
            'created_at' => now(),
        ]);

        return redirect('/masyarakat')->with('success', 'Data sosial masyarakat berhasil diajukan.');
    }

    public function updateStatus($id, $status)
    {
        if (session('role') === 'mahasiswa') {
            abort(403);
        }

        if (!in_array($status, ['Lulus', 'Belum Lulus'])) {
            abort(400);
        }

        DB::table('masyarakat')
            ->where('id', $id)
            ->update([
                'status' => $status,
            ]);

        return redirect('/masyarakat')->with('success', 'Status kegiatan sosial berhasil diperbarui.');
    }
}