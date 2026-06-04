<?php

namespace App\Http\Controllers;

use App\Models\Asrama;
use App\Models\KepasProfile;
use App\Models\MahasiswaProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Tampilkan form registrasi.
     * Hanya bisa diakses oleh administrator.
     */
    public function showRegister()
    {
        if (!session()->has('id_user') || session('role') !== 'administrator') {
            return redirect('/login')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        $asramas = Asrama::orderBy('nama_asrama')->get();

        return view('register', compact('asramas'));
    }

    /**
     * Proses form registrasi dan simpan data user + profil.
     */
    public function register(Request $request)
    {
        if (!session()->has('id_user') || session('role') !== 'administrator') {
            return redirect('/login')->with('error', 'Akses ditolak. Halaman ini hanya untuk administrator.');
        }

        // --- Validasi dasar (berlaku untuk semua role) ---
        $rules = [
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:mahasiswa,kepas,administrator',
        ];

        // --- Validasi tambahan berdasarkan role ---
        if ($request->role === 'mahasiswa') {
            $rules['nibs']       = 'required|string|max:30|unique:mahasiswa_profiles,nibs';
            $rules['nim']        = 'required|string|max:30|unique:mahasiswa_profiles,nim';
            $rules['prodi']      = 'nullable|string|max:100';
            $rules['angkatan']   = 'nullable|string|max:10';
            $rules['universitas'] = 'nullable|string|max:100';
            $rules['no_telp']    = 'nullable|string|max:20';
            $rules['asrama_id']  = 'nullable|exists:asramas,id';
            $rules['foto_profil'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        if ($request->role === 'kepas') {
            $rules['asrama_id']   = 'required|exists:asramas,id';
            $rules['no_telp']     = 'nullable|string|max:20';
            $rules['foto_profil'] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        $messages = [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'nibs.required'      => 'NIBS wajib diisi untuk mahasiswa.',
            'nibs.unique'        => 'NIBS ini sudah terdaftar.',
            'nim.required'       => 'NIM wajib diisi untuk mahasiswa.',
            'nim.unique'         => 'NIM ini sudah terdaftar.',
            'asrama_id.required' => 'Asrama wajib dipilih untuk Kepala Asrama.',
            'asrama_id.exists'   => 'Asrama yang dipilih tidak valid.',
            'foto_profil.image'  => 'File harus berupa gambar.',
            'foto_profil.max'    => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        // --- Proses simpan dalam satu transaksi database ---
        DB::transaction(function () use ($request) {

            // 1. Buat user baru
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // 2. Handle upload foto profil (jika ada)
            $namaFoto = null;
            if ($request->hasFile('foto_profil')) {
                $file      = $request->file('foto_profil');
                $namaFoto  = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $namaFoto);
            }

            // 3. Buat profil sesuai role
            if ($request->role === 'mahasiswa') {
                MahasiswaProfile::create([
                    'user_id'     => $user->id,
                    'asrama_id'   => $request->asrama_id ?: null,
                    'nibs'        => $request->nibs,
                    'nim'         => $request->nim,
                    'prodi'       => $request->prodi,
                    'angkatan'    => $request->angkatan,
                    'universitas' => $request->universitas,
                    'no_telp'     => $request->no_telp,
                    'foto_profil' => $namaFoto,
                ]);
            }

            if ($request->role === 'kepas') {
                KepasProfile::create([
                    'user_id'     => $user->id,
                    'asrama_id'   => $request->asrama_id,
                    'no_telp'     => $request->no_telp,
                    'foto_profil' => $namaFoto,
                ]);
            }
        });

        return redirect('/register')->with('success', 'Akun berhasil dibuat! User baru telah terdaftar di sistem.');
    }
}
