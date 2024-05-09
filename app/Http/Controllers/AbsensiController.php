<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\jadwal_dosen;
use App\Models\Khs;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\Presensi_mhsw;
use App\Models\Tahun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $npm = $request->npm ?? null;
        $data = app(User::class)->newQuery();

        if (!empty($npm)) {
            $data = $data->where('npm', $npm)->first();
        }

        return response()->json([
            'message' => 'berhasil mengambil data',
            'success' => true,
            'data' => [
                'hadir' => $data->hadir,
                'izin' => $data->izin,
                'sakit' => $data->sakit,
                'alpa' => $data->alpa,
            ]
        ], 200);
    }

    public function absen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
            'presensi_id' => 'required|integer',
            'status' => 'required',
            'nilai' => 'required|integer',
            'jadwal_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $nilai = $request->nilai ?? 0;
        $dataMhsw = Mhsw::where('MhswID', $request->npm)->first();

        $tahunID = Tahun::where([
            'NA' => 'N',
            'ProdiID' => $dataMhsw->ProdiID,
            'ProgramID' => $dataMhsw->ProgramID
        ])->pluck('TahunID');

        $krs = krs::where([
            'TahunID' => $tahunID[0],
            'MhswID' => $dataMhsw->MhswID,
            'JadwalID' => $request->jadwal_id
        ])->get();


        $isAbsen = Absensi::where([
            'MhswID' => $dataMhsw->MhswID,
            'JadwalID' => $request->jadwal_id,
            'PresensiID' => $request->presensi_id
        ])->first();

        if ($isAbsen != null) {
            return response()->json([
                'message' => 'anda sudah absen pada matkul ini',
                'status' => false
            ], 401);
        }

        $post = Absensi::insert([
            'JadwalID' => $request->jadwal_id,
            'KRSID' => $krs[0]->KRSID,
            'PresensiID' => $request->presensi_id,
            'MhswID' => $dataMhsw->MhswID,
            'JenisPresensiID' => $request->status,
            'Nilai' => $nilai,
            'NA' => 'N'
        ]);

        // $update = User::where('npm', $request->npm)->increment('hadir', 1);

        if (!$post) {
            return response()->json([
                'message' => 'tidak berhasil absen',
                'status' => false
            ], 402);
        }
        return response()->json([
            'message' => 'berhasil absen',
            'status' => $post
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {


        $npm = $request->npm ?? null;
        $status = $request->status ?? null;
        $paginate = $request->paginate ?? null;
        $data = app(Presensi_mhsw::class)->newQuery();

        if (!empty($npm)) {
            $data = $data->where([
                'mhsw_id' => $npm,
                'status' => $status
            ])->paginate($paginate);
        }

        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
