<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Portofolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
            'file_bukti' => $fileName,
            'created_at' => now(),
        ]);

        return redirect('/portofolio')->with('success', 'Data portofolio berhasil diajukan.');
    }

    public function destroy($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $portofolio = Portofolio::findOrFail($id);

        if (session('role') === 'mahasiswa' && $portofolio->id_user != session('id_user')) {
            abort(403);
        }

        if ($portofolio->file_bukti) {
            $filePath = public_path('uploads/portofolio/' . $portofolio->file_bukti);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $portofolio->delete();

        return redirect('/portofolio')->with('success', 'Data portofolio berhasil dihapus.');
    }

}