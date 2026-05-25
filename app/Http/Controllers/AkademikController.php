<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Akademik;
use App\Models\Toefl;
use Illuminate\Http\Request;

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
            'status' => 'Belum Lulus',
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
            'status' => 'Belum Lulus',
            'tanggal_upload' => now(),
        ]);

        return redirect('/akademik')->with('success', 'Riwayat TOEFL berhasil ditambahkan.');
    }

    public function updateIpStatus($id, $status)
    {
        if (session('role') === 'mahasiswa') {
            abort(403);
        }

        if (!in_array($status, ['Lulus', 'Belum Lulus'])) {
            abort(400);
        }

        $akademik = Akademik::findOrFail($id);

        $akademik->update([
            'status' => $status,
        ]);

        return redirect('/akademik');
    }

    public function updateToeflStatus($id, $status)
    {
        if (session('role') === 'mahasiswa') {
            abort(403);
        }

        if (!in_array($status, ['Lulus', 'Belum Lulus'])) {
            abort(400);
        }

        $toefl = Toefl::findOrFail($id);

        $toefl->update([
            'status' => $status,
        ]);

        return redirect('/akademik');
    }
}