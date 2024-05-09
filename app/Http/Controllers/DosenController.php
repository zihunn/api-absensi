<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\jadwaldosen;
use App\Models\Tahun;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Return_;

class DosenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addDosen(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'dosen_id' => 'required',
            'jadwal_id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $dataInput = $req;

        $checkData = jadwaldosen::where([
            'JadwalID' => $dataInput->jadwal_id,
            'DosenID' => $dataInput->dosen_id,
        ])->get();

        if (count($checkData) != 0) {
            return response()->json([
                'message' => 'Dosen Sudah ada',
                'status' => true
            ], 404);
        } else {
            $input = jadwaldosen::create([
                'JadwalID' => $dataInput->jadwal_id,
                'DosenID' => $dataInput->dosen_id,
                'JenisDosenID' => $dataInput->status,
                'TglBuat' => $today,
                'LoginBuat' => 'TestMobile'
            ]);

            if (!$input) {
                return response()->json([
                    $input
                ]);
            }

            return response()->json([
                'message' => 'Berhasil menambahkan dosen',
                'status' => true
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($dosenId)
    {


        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'KEP',
            'ProgramID' => 'PG'
        ])->pluck('TahunID');

        $jadwalIds = Jadwal::where('TahunID', $tahun_aktif[0])
            ->pluck('JadwalID');

        $jadwalsArray = Jadwal::whereIn('JadwalID', $jadwalIds)
            ->where('DosenID', $dosenId)
            ->join('mk', 'jadwal.MKID', '=', 'mk.MKID')
            ->select(
                'jadwal.Nama as nama',
                'jadwal.JadwalID as Jadwal_id',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.JamSelesai as jam_selesai',
                'jadwal.ProdiID as prodi',
                'mk.Sesi as semester'
            )
            ->get()->toArray();

        $jadwalDosensArray = jadwaldosen::whereIn('jadwaldosen.JadwalID', $jadwalIds)
            ->where('jadwaldosen.DosenID', $dosenId)
            ->join('jadwal', 'jadwaldosen.JadwalID', '=', 'jadwal.JadwalID')
            ->join('mk', 'jadwal.MKID', '=', 'mk.MKID')
            ->select(
                'jadwal.Nama as nama',
                'jadwal.JadwalID as Jadwal_id',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.JamSelesai as jam_selesai',
                'jadwal.ProdiID as prodi',
                'mk.Sesi as semester',
            )
            ->get()->toArray();

        if (count($jadwalsArray) == 0 && count($jadwalDosensArray) == 0) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => false,
            ], 404);
        }
        $combinedJadwalsArray = array_merge($jadwalsArray, $jadwalDosensArray);

        return response()->json([
            'message' => 'Berhasil mengambil data',
            'status' => true,
            'total' => count($combinedJadwalsArray),
            'data' => $combinedJadwalsArray
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = jadwaldosen::where('JadwalDosenID', $id)->get();

        if (!$data->isEmpty()) {
            jadwaldosen::where('JadwalDosenID', $id)->delete();

            return response()->json([
                'message' => 'Dosen berhasil dihapus',
                'status' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => false
            ], 404);
        }
    }

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
}
