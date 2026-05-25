<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Portofolio;
use Illuminate\Http\Request;

class PortofolioController extends Controller
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

        $query = Portofolio::with('user');

        if ($role_user === 'mahasiswa') {
            $query->where('id_user', $id_user);
        }

        $data_portofolio = $query
            ->orderBy('id', 'desc')
            ->get();

        return view('portofolio', compact(
            'u',
            'role_user',
            'foto_path',
            'data_portofolio'
        ));
    }

    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'kategori' => 'required|in:prestasi,organisasi,workshop/seminar',
            'tanggal_tahun' => 'required|string|max:50',
            'nama_kegiatan' => 'required|string|max:255',
            'penyelenggara_jabatan' => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $fileName = null;

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $fileName = 'portofolio_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/portofolio'), $fileName);
        }

        Portofolio::create([
            'id_user' => session('id_user'),
            'kategori' => $request->kategori,
            'tanggal_tahun' => $request->tanggal_tahun,
            'nama_kegiatan' => $request->nama_kegiatan,
            'penyelenggara_jabatan' => $request->penyelenggara_jabatan,
            'level' => $request->level,
            'status' => 'Belum Lulus',
            'file_bukti' => $fileName,
            'created_at' => now(),
        ]);

        return redirect('/portofolio')->with('success', 'Data portofolio berhasil diajukan.');
    }

    public function updateStatus($id, $status)
    {
        if (session('role') === 'mahasiswa') {
            abort(403);
        }

        if (!in_array($status, ['Lulus', 'Belum Lulus'])) {
            abort(400);
        }

        $portofolio = Portofolio::findOrFail($id);

        $portofolio->update([
            'status' => $status,
        ]);

        return redirect('/portofolio')->with('success', 'Status portofolio berhasil diperbarui.');
    }
}