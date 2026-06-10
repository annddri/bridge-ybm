<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Cek apakah user yang login adalah administrator.
     */
    private function authCheck()
    {
        if (!session()->has('id_user') || session('role') !== 'administrator') {
            abort(403);
        }
    }

    /**
     * Halaman utama admin — tabel semua users + daftar asrama.
     */
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        if (session('role') !== 'administrator') {
            abort(403);
        }

        $users = User::with(['mahasiswaProfile', 'kepasProfile'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();
        $asramas = Asrama::orderBy('nama_asrama')->get();

        return view('admin', compact('users', 'asramas'));
    }

    /**
     * Edit akun user — diproses via AJAX (inline, tanpa reload).
     */
    public function updateUser(Request $request, $id)
    {
        $this->authCheck();

        $user = User::findOrFail($id);

        $rules = [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $id,
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        if ($user->role === 'mahasiswa') {
            $rules['nibs'] = 'required|string|max:50';
            $rules['nim'] = 'required|string|max:50';
            $rules['universitas'] = 'nullable|string|max:150';
            $rules['prodi'] = 'nullable|string|max:150';
            $rules['angkatan'] = 'nullable|string|max:10';
            $rules['asrama_id'] = 'nullable|exists:asramas,id';
            $rules['no_telp'] = 'nullable|string|max:20';
        } elseif ($user->role === 'kepas') {
            $rules['asrama_id'] = 'required|exists:asramas,id';
            $rules['no_telp'] = 'nullable|string|max:20';
        }

        $request->validate($rules, [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan akun lain.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nibs.required'      => 'NIBS wajib diisi.',
            'nim.required'       => 'NIM wajib diisi.',
            'asrama_id.required' => 'Asrama wajib dipilih untuk Kepala Asrama.',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // --- Handle Upload Foto Profil ---
        $namaFoto = null;
        if ($request->hasFile('foto_profil')) {
            $request->validate(['foto_profil' => 'image|max:2048'], [
                'foto_profil.image' => 'File harus berupa gambar.',
                'foto_profil.max'   => 'Ukuran foto maksimal 2MB.'
            ]);
            $file = $request->file('foto_profil');
            $namaFoto = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $namaFoto);
        }

        if ($user->role === 'mahasiswa' && $user->mahasiswaProfile) {
            $updateData = $request->only(['nibs', 'nim', 'universitas', 'prodi', 'angkatan', 'asrama_id', 'no_telp']);
            if ($namaFoto) {
                if ($user->mahasiswaProfile->foto_profil && file_exists(public_path('uploads/profile/' . $user->mahasiswaProfile->foto_profil))) {
                    @unlink(public_path('uploads/profile/' . $user->mahasiswaProfile->foto_profil));
                }
                $updateData['foto_profil'] = $namaFoto;
            }
            $user->mahasiswaProfile->update($updateData);
        } elseif ($user->role === 'kepas' && $user->kepasProfile) {
            $updateData = $request->only(['asrama_id', 'no_telp']);
            if ($namaFoto) {
                if ($user->kepasProfile->foto_profil && file_exists(public_path('uploads/profile/' . $user->kepasProfile->foto_profil))) {
                    @unlink(public_path('uploads/profile/' . $user->kepasProfile->foto_profil));
                }
                $updateData['foto_profil'] = $namaFoto;
            }
            $user->kepasProfile->update($updateData);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil diperbarui.',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    /**
     * Hapus akun user.
     */
    public function deleteUser($id)
    {
        $this->authCheck();

        // Jangan bisa hapus diri sendiri
        if ($id == session('id_user')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak bisa menghapus akun yang sedang aktif.',
            ], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil dihapus.',
        ]);
    }

    /**
     * Tambah asrama baru.
     */
    public function storeAsrama(Request $request)
    {
        $this->authCheck();

        $request->validate([
            'kode_asrama'  => 'required|string|max:20|unique:asramas,kode_asrama',
            'nama_asrama'  => 'required|string|max:100',
            'regional'     => 'nullable|string|max:100',
        ], [
            'kode_asrama.required' => 'Kode asrama wajib diisi.',
            'kode_asrama.unique'   => 'Kode asrama sudah digunakan.',
            'nama_asrama.required' => 'Nama asrama wajib diisi.',
        ]);

        $asrama = Asrama::create([
            'kode_asrama' => strtoupper($request->kode_asrama),
            'nama_asrama' => $request->nama_asrama,
            'regional'    => $request->regional,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Asrama berhasil ditambahkan.',
            'asrama'  => $asrama,
        ]);
    }

    /**
     * Hapus asrama.
     */
    public function deleteAsrama($id)
    {
        $this->authCheck();

        $asrama = Asrama::findOrFail($id);
        $asrama->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Asrama berhasil dihapus.',
        ]);
    }
}
