<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Akademik;
use App\Models\Toefl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AkademikController extends Controller
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

        $akademikQuery = Akademik::with('user');
        $toeflQuery = Toefl::with('user');

        if ($role_user === 'mahasiswa') {
            $akademikQuery->where('id_user', $id_user);
            $toeflQuery->where('id_user', $id_user);
        }

        $data_akademik = $akademikQuery
            ->orderBy('id', 'desc')
            ->get();

        $data_toefl = $toeflQuery
            ->orderBy('id', 'desc')
            ->get();

        $ipk_sekarang = Akademik::where('id_user', $id_user)->avg('ip');
        $ipk_sekarang = number_format($ipk_sekarang ?? 0, 2);

        return view('akademik', compact(
            'u',
            'role_user',
            'foto_path',
            'data_akademik',
            'data_toefl',
            'ipk_sekarang'
        ));
    }

    public function storeIp(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'semester' => 'required|integer|min:1|max:8',
            'ip' => 'required|numeric|min:0|max:4',
            'bukti_khs' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $fileName = null;

        if ($request->hasFile('bukti_khs')) {
            $file = $request->file('bukti_khs');
            $fileName = 'khs_smstr_' . $request->semester . '_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/akademik'), $fileName);
        }

        Akademik::create([
            'id_user' => session('id_user'),
            'semester' => $request->semester,
            'ip' => $request->ip,
            'file_verifikasi' => $fileName,
        ]);

        return redirect('/akademik')->with('success', 'Data IP berhasil dikirim.');
    }

    public function storeToefl(Request $request)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $request->validate([
            'score' => 'required|integer|min:0',
            'jenis_tes' => 'required|in:Pre-Test,Post-Test,Real Test',
            'bukti_toefl' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $fileName = null;

        if ($request->hasFile('bukti_toefl')) {
            $file = $request->file('bukti_toefl');
            $fileName = 'toefl_' . time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('uploads/toefl'), $fileName);
        }

        Toefl::create([
            'id_user' => session('id_user'),
            'score' => $request->score,
            'jenis_tes' => $request->jenis_tes,
            'file_sertifikat' => $fileName,
            'tanggal_upload' => now(),
        ]);

        return redirect('/akademik')->with('success', 'Riwayat TOEFL berhasil ditambahkan.');
    }

    public function destroyIp($id)
{
    if (!session()->has('id_user')) {
        return redirect('/login');
    }

    $ip = Akademik::findOrFail($id);

    if (session('role') === 'mahasiswa' && $ip->id_user != session('id_user')) {
        abort(403);
    }

    if ($ip->file_verifikasi) {
        $filePath = public_path('uploads/akademik/' . $ip->file_verifikasi);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }
    }

    $ip->delete();

    return redirect('/akademik')->with('success', 'Data IP semester berhasil dihapus.');
}

    public function destroyToefl($id)
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $toefl = Toefl::findOrFail($id);

        if (session('role') === 'mahasiswa' && $toefl->id_user != session('id_user')) {
            abort(403);
        }

        if ($toefl->file_sertifikat) {
            $filePath = public_path('uploads/toefl/' . $toefl->file_sertifikat);

            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        $toefl->delete();

        return redirect('/akademik')->with('success', 'Data TOEFL berhasil dihapus.');
    }
}