<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkademikController extends Controller
{
    public function index()
    {
        if (!session()->has('id_user')) {
            return redirect('/login');
        }

        $id_user = session('id_user');
        $role_user = session('role');

        $u = DB::table('users')
            ->leftJoin('mahasiswa_profiles', 'users.id', '=', 'mahasiswa_profiles.user_id')
            ->select(
                'users.id',
                'users.name',
                'users.role',
                'mahasiswa_profiles.foto_profil as foto_profil'
            )
            ->where('users.id', $id_user)
            ->first();

        $foto_path = asset('assets/img/' . ($u->foto_profil ?? 'default.png'));

        $akademikQuery = DB::table('akademik')
            ->join('users', 'akademik.id_user', '=', 'users.id')
            ->select('akademik.*', 'users.name');

        $toeflQuery = DB::table('toefl')
            ->join('users', 'toefl.id_user', '=', 'users.id')
            ->select('toefl.*', 'users.name');

        if ($role_user === 'mahasiswa') {
            $akademikQuery->where('akademik.id_user', $id_user);
            $toeflQuery->where('toefl.id_user', $id_user);
        }

        $data_akademik = $akademikQuery
            ->orderBy('akademik.id', 'desc')
            ->get();

        $data_toefl = $toeflQuery
            ->orderBy('toefl.id', 'desc')
            ->get();

        $ipk_sekarang = DB::table('akademik')
            ->where('id_user', $id_user)
            ->avg('ip');

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

        DB::table('akademik')->insert([
            'id_user' => session('id_user'),
            'semester' => $request->semester,
            'ip' => $request->ip,
            'file_verifikasi' => $fileName,
            'status' => 'Belum Lulus',
            'created_at' => now(),
            'updated_at' => now(),
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

        DB::table('toefl')->insert([
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

        DB::table('akademik')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now(),
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

        DB::table('toefl')
            ->where('id', $id)
            ->update([
                'status' => $status,
            ]);

        return redirect('/akademik');
    }
}