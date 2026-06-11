<?php

namespace App\Http\Controllers;

use App\Models\Pembinaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class PembinaanController extends Controller
{
    /**
     * Halaman Pembinaan untuk Mahasiswa.
     */
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'mahasiswa') {
            abort(403);
        }

        $id_user   = session('id_user');
        $role_user = session('role');

        $u = User::with('mahasiswaProfile')
            ->where('id', $id_user)
            ->first();

        if (!$u) {
            abort(404, 'User tidak ditemukan.');
        }

        $foto_path = asset('uploads/profile/' . ($u->mahasiswaProfile->foto_profil ?? 'default.png'));

        $data_pembinaan = Pembinaan::where('id_user', $id_user)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pembinaan', compact(
            'u',
            'role_user',
            'foto_path',
            'data_pembinaan'
        ));
    }

    /**
     * Simpan data pembinaan baru (oleh mahasiswa).
     */
    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'mahasiswa') {
            abort(403);
        }

        $request->validate([
            'tanggal'       => 'required|date',
            'nama_pemateri' => 'required|string|max:150',
            'judul_materi'  => 'required|string|max:255',
            'resume'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'tanggal.required'       => 'Tanggal wajib diisi.',
            'nama_pemateri.required' => 'Nama pemateri wajib diisi.',
            'judul_materi.required'  => 'Judul materi wajib diisi.',
            'resume.mimes'           => 'File resume harus berupa PDF, JPG, atau PNG.',
            'resume.max'             => 'Ukuran file resume maksimal 5MB.',
        ]);

        $namaFile = null;

        if ($request->hasFile('resume')) {
            $file     = $request->file('resume');
            $namaFile = 'resume_' . session('id_user') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/pembinaan'), $namaFile);
        }

        Pembinaan::create([
            'id_user'       => session('id_user'),
            'tanggal'       => $request->tanggal,
            'nama_pemateri' => $request->nama_pemateri,
            'judul_materi'  => $request->judul_materi,
            'resume'        => $namaFile,
        ]);

        return redirect('/pembinaan')->with('success', 'Data pembinaan berhasil disimpan.');
    }

    /**
     * Hapus data pembinaan (hanya milik sendiri).
     */
    public function destroy($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $pembinaan = Pembinaan::findOrFail($id);

        if (session('role') === 'mahasiswa' && $pembinaan->id_user != session('id_user')) {
            abort(403);
        }

        // Hapus file resume jika ada
        if ($pembinaan->resume) {
            $filePath = public_path('uploads/pembinaan/' . $pembinaan->resume);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $pembinaan->delete();

        return redirect('/pembinaan')->with('success', 'Data pembinaan berhasil dihapus.');
    }


}
