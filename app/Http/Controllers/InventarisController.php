<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class InventarisController extends Controller
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

        $data_inventaris = Inventaris::orderBy('id_barang', 'desc')->get();

        return view('inventaris', compact(
            'u',
            'role_user',
            'foto_path',
            'data_inventaris'
        ));
    }

    public function store(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'kode_barang' => 'required|string|max:20|unique:inventaris,kode_barang',
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'keterangan' => 'nullable|string',
        ]);

        $u = User::with('mahasiswaProfile')->find(session('id_user'));

        Inventaris::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi,
            'keterangan' => $request->keterangan,

            'created_by' => $u->name,
            'created_by_nibs' => $u->mahasiswaProfile->nibs ?? '-',
            'updated_by' => $u->name,
            'updated_by_nibs' => $u->mahasiswaProfile->nibs ?? '-',
        ]);

        return redirect('/inventaris')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id_barang)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $barang = Inventaris::findOrFail($id_barang);

        $request->validate([
            'kode_barang' => 'required|string|max:20|unique:inventaris,kode_barang,' . $id_barang . ',id_barang',
            'nama_barang' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'keterangan' => 'nullable|string',
        ]);

        $u = User::with('mahasiswaProfile')->find(session('id_user'));

        $barang->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'kondisi' => $request->kondisi,
            'keterangan' => $request->keterangan,

            'updated_by' => $u->name,
            'updated_by_nibs' => $u->mahasiswaProfile->nibs ?? '-',
        ]);

        return redirect('/inventaris')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id_barang)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $barang = Inventaris::findOrFail($id_barang);
        $barang->delete();

        return redirect('/inventaris')->with('success', 'Barang berhasil dihapus.');
    }
}