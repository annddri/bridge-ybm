<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        if (!$user) {
            return back()->with('error', 'Email tidak terdaftar!');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password salah!');
        }

        session([
            'id_user' => $user->id,
            'nama' => $user->name,
            'role' => $user->role,

            // kalau kolom ini masih ada di tabel users
            'ro' => $user->ro ?? null,
            'id_asrama' => $user->id_asrama ?? null,
            'universitas' => $user->universitas ?? null,
            'angkatan' => $user->angkatan ?? null,
        ]);

        if ($user->role === 'mahasiswa') {
            return redirect('/dashboard');
        }

        if ($user->role === 'pengurus') {
            return redirect('/dashboard-pengurus');
        }

        return redirect('/dashboard-admin');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}