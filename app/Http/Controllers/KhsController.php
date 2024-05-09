<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Khs;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\Tahun;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KhsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ]);
        }

        $mahasiswaID = $request->npm;
        $selectedSemester = $request->semester ?? null;

        $dataMhsw = Mhsw::where('MhswID', $mahasiswaID)->first();

        $listSemester = Khs::where('MhswID', $mahasiswaID,)->pluck('Sesi');
        $listTahunID = Khs::where('MhswID', $mahasiswaID,)->pluck('TahunID');

        $data = null;

        if ($selectedSemester == null) {


            $data = DB::table('khs')
                ->where([
                    'khs.MhswID' => $mahasiswaID,
                    'khs.Sesi' => $listSemester->last()
                ])
                ->join('krs', 'khs.KHSID', '=', 'krs.KHSID')
                ->select(
                    'krs.MKKode as mk_kode',
                    'krs.Nama as nama',
                    'khs.Sesi as semester',
                    'krs.SKS as sks',
                    'krs.NilaiAkhir as nilai',
                    'krs.BobotNilai as bobot',
                    'krs.GradeNilai as grade'
                )->get();
        } else {

            $data = DB::table('khs')
                ->where([
                    'khs.MhswID' => $mahasiswaID,
                    'khs.Sesi' => $selectedSemester
                ])
                ->join('krs', 'khs.KHSID', '=', 'krs.KHSID')
                ->select(
                    'krs.MKKode as mk_kode',
                    'krs.Nama as nama',
                    'khs.Sesi as semester',
                    'krs.SKS as sks',
                    'krs.NilaiAkhir as nilai',
                    'krs.BobotNilai as bobot',
                    'krs.GradeNilai as grade'
                )->get();
        }

        if (count($data) == 0) {
            return response()->json([
                'message' => 'data tidak ada',
                'staus' => false
            ]);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data',
            'status' => true,
            'data' => [
                'semester' => $listSemester,
                'tahun_id' => $listTahunID,
                'data_nilai' => $data
            ]

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function print(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ]);
        }

        $mahasiswaID = $request->npm;
        $selectedSemester = $request->semester ?? null;

        $dataMhsw = Mhsw::where('MhswID', $mahasiswaID)->get();

        $listSemester = Khs::where('MhswID', $mahasiswaID,)->pluck('Sesi');
        $listTahunID = Khs::where('MhswID', $mahasiswaID,)->pluck('TahunID');
        $dosen_wali = Dosen::where('Login', $dataMhsw[0]['PenasehatAkademik'])->get();
        $data = null;

        if ($selectedSemester == null) {

            $data = DB::table('khs')
                ->where([
                    'khs.MhswID' => $mahasiswaID,
                    'khs.Sesi' => $listSemester->last()
                ])
                ->join('krs', 'khs.KHSID', '=', 'krs.KHSID')
                ->select(
                    'krs.MKKode as mk_kode',
                    'krs.Nama as nama',
                    'khs.Sesi as semester',
                    'krs.SKS as sks',
                    'krs.NilaiAkhir as nilai',
                    'krs.BobotNilai as bobot',
                    'krs.GradeNilai as grade',
                    'khs.TahunID as tahun',
                    'khs.IP as ip',
                    'khs.IPS as ips',

                )->get();
        } else {

            $data = DB::table('khs')
                ->where([
                    'khs.MhswID' => $mahasiswaID,
                    'khs.Sesi' => $selectedSemester
                ])
                ->join('krs', 'khs.KHSID', '=', 'krs.KHSID')
                ->select(
                    'krs.MKKode as mk_kode',
                    'krs.Nama as nama',
                    'khs.Sesi as semester',
                    'krs.SKS as sks',
                    'krs.NilaiAkhir as nilai',
                    'krs.BobotNilai as bobot',
                    'krs.GradeNilai as grade',
                    'khs.TahunID as tahun',
                    'khs.IP as ip',
                    'khs.IPS as ips',



                )->get();
        }
        $totalSKSBobot = $data->reduce(function ($carry, $item) {
            return $carry + ($item->sks * $item->bobot);
        }, 0);

        $date = Carbon::now()->format('j F Y');
        // return response($data);
        if ($request->debug) {
            return view('khs', compact(
                'dataMhsw',
                'dosen_wali',
                'data',
                'totalSKSBobot',
                'date'
            ));
        }

        $pdf = Pdf::loadView('khs', compact(
            'dataMhsw',
            'dosen_wali',
            'data',
            'totalSKSBobot',
            'date'

        ));
        $pdf->setPaper('A4');
        return $pdf->stream('KHS-' . 'asd'   . '.pdf');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return view('khs');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
