<?php

namespace App\Http\Controllers;

use App\Models\DanaKas;
use App\Models\User;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        if (!in_array($role_user, ['mahasiswa', 'kepala_asrama'])) {
            abort(403);
        }

        $u = User::leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.role',
                'mahasiswa_profiles.foto_profil',
                'mahasiswa_profiles.nibs'
            )
            ->where('users.id', $id_user)
            ->first();

        $foto_path = asset('uploads/profile/' . ($u->foto_profil ?? 'default.png'));

        $data_kas = DanaKas::orderBy('tanggal', 'desc')
            ->orderBy('id_kas', 'desc')
            ->get();

        $total_masuk = DanaKas::where('jenis_transaksi', 'Masuk')->sum('nominal');
        $total_keluar = DanaKas::where('jenis_transaksi', 'Keluar')->sum('nominal');
        $saldo = $total_masuk - $total_keluar;

        return view('keuangan', compact(
            'u',
            'role_user',
            'foto_path',
            'data_kas',
            'total_masuk',
            'total_keluar',
            'saldo'
        ));
    }

    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $u = User::leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select('users.id', 'users.name', 'mahasiswa_profiles.nibs')
            ->where('users.id', session('id_user'))
            ->first();

        DanaKas::create([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'created_by_id' => session('id_user'),
            'created_by' => $u->name,
            'created_by_nibs' => $u->nibs,
        ]);

        return redirect('/keuangan')->with('success', 'Transaksi kas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $kas = DanaKas::findOrFail($id);

        if ($kas->created_by_id != session('id_user')) {
            abort(403);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|in:Masuk,Keluar',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $u = User::leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select('users.id', 'users.name', 'mahasiswa_profiles.nibs')
            ->where('users.id', session('id_user'))
            ->first();

        $kas->update([
            'tanggal' => $request->tanggal,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
        ]);

        return redirect('/keuangan')->with('success', 'Transaksi kas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $kas = DanaKas::findOrFail($id);

        if ($kas->created_by_id != session('id_user')) {
            abort(403);
        }

        $kas->delete();

        return redirect('/keuangan')->with('success', 'Transaksi kas berhasil dihapus.');
    }
}