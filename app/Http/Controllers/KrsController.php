<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\Jadwal_Mhsw;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\mk;
use App\Models\Tahun;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class KrsController extends Controller
{

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

        $dataMhsw = Mhsw::where('MhswID', $request->npm)->get();

        if (count($dataMhsw) == 0) {
            return response()->json([
                'message' => 'Mahasiswa tidak ditemuka',
            ], 404);
        }

        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => $dataMhsw[0]['ProdiID'],
            'ProgramID' => $dataMhsw[0]['ProgramID']
        ])->first();

        $dosen_wali = Dosen::where('Login', $dataMhsw[0]['PenasehatAkademik'])->get();

        $status_krs = '';

        if ($dataMhsw[0]['StatusSKS'] == '0') {
            $status_krs = 'Tidak disetujui';
        } elseif ($dataMhsw[0]['StatusSKS'] == '1') {
            $status_krs = 'Disetujui';
        } elseif ($dataMhsw[0]['StatusSKS'] == '2') {
            $status_krs = 'Belum diperiksa';
        } elseif ($dataMhsw[0]['StatusSKS'] == null) {
            $status_krs = 'Belum KRS';
        }


        $mulai_krs_dateTime = new DateTime($tahun_aktif->TglKRSMulai);
        $selesai_krs_dateTime = new DateTime($tahun_aktif->TglKRSSelesai);

        $startDateKrs = Carbon::parse($mulai_krs_dateTime)->translatedFormat('l, j F Y');
        $endDateKrs = Carbon::parse($selesai_krs_dateTime)->translatedFormat('l, j F Y');

        $now = Carbon::now();
        $avail_krs = false;

        if ($now->between($mulai_krs_dateTime, $selesai_krs_dateTime)) {
            $avail_krs = true;
        } else {
            $avail_krs = false;
        }

        $dataKrs = DB::table('krs')
            ->where([
                'krs.MhswID' => $dataMhsw[0]['MhswID'],
                'krs.TahunID' => $tahun_aktif->TahunID
            ])
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->join('mk', 'jadwal.MKKode', '=', 'mk.MKKode')
            ->select(
                'krs.KRSID as krs_id',
                'jadwal.JadwalID as jadwal_id',
                'jadwal.Nama as Nama',
                'jadwal.SKS as Sks',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.jamSelesai as jam_selesai',
                'mk.Sesi as semester',
                'dosen.Nama as dosen'
            )
            ->get();
        $totalSks = DB::table('krs')
            ->where([
                'krs.MhswID' => $dataMhsw[0]['MhswID'],
                'krs.TahunID' => $tahun_aktif->TahunID
            ])
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->join('mk', 'jadwal.MKKode', '=', 'mk.MKKode')
            ->sum('jadwal.SKS');

        $dosen = $dosen_wali[0]['GelarDepan'] . ' ' . $dosen_wali[0]['Nama'] . ' ' . $dosen_wali[0]['Gelar'];
        return response()->json([
            'message' => 'berhasil mengambil data',
            'dosen' =>  $dosen,
            'status_krs' => $status_krs,
            'mulai_krs' => $startDateKrs,
            'selesai_krs' => $endDateKrs,
            // 'avail_krs' => $avail_krs,
            'avail_krs' => true,
            'total_sks' => $totalSks,
            'data' => $dataKrs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function cetak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ]);
        }

        $data_mhsw = Mhsw::where('MhswID', $request->npm)->get();

        if (count($data_mhsw) == 0) {
            return response()->json([
                'message' => 'Mahasiswa tidak ditemukan',
            ], 404);
        }

        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => $data_mhsw[0]['ProdiID'],
            'ProgramID' => $data_mhsw[0]['ProgramID']
        ])->first();

        $dosen_wali = Dosen::where('Login', $data_mhsw[0]['PenasehatAkademik'])->get();

        $dataKrs = DB::table('krs')
            ->where([
                'krs.MhswID' => $data_mhsw[0]['MhswID'],
                'krs.TahunID' => $tahun_aktif->TahunID
            ])
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->join('mk', 'jadwal.MKKode', '=', 'mk.MKKode')
            ->select(
                'krs.KRSID as krs_id',
                'jadwal.JadwalID as jadwal_id',
                'mk.MKKode as mk_kode',
                'jadwal.Nama as Nama',
                'jadwal.SKS as Sks',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.jamSelesai as jam_selesai',
                'mk.Sesi as semester',
                'dosen.Nama as dosen',
            )
            ->get();
        $totalSks = DB::table('krs')
            ->where([
                'krs.MhswID' => $data_mhsw[0]['MhswID'],
                'krs.TahunID' => $tahun_aktif->TahunID
            ])
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->join('mk', 'jadwal.MKKode', '=', 'mk.MKKode')
            ->sum('jadwal.SKS');

        $date = Carbon::now()->format('j F Y');
        // return response()->json(['data' => $dataKrs]);
        if ($request->debug) {
            return view('print', compact(
                'data_mhsw',
                'dataKrs',
                'totalSks',
                'dosen_wali',
                'tahun_aktif',
                'date'
            ));
        }

        $pdf = Pdf::loadView('print', compact(
            'data_mhsw',
            'dataKrs',
            'totalSks',
            'dosen_wali',
            'tahun_aktif',
            'date'

        ));
        $pdf->setPaper('A4');

        return $pdf->download('KRS-' . $data_mhsw[0]['MhswID']   . '.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MhswID' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $mahasiswaID = $request->MhswID;

        $dataMhsw = Mhsw::where('MhswID', $mahasiswaID)->first();

        $tahun_aktif = Tahun::where([
            'ProgramID' => $dataMhsw->ProgramID,
            'ProdiID' => $dataMhsw->ProdiID,
            'NA' => 'N',
        ])->pluck('TahunID');

        if (!$tahun_aktif) {
            return response()->json([
                'Message' => 'Tahun Aktif Tidak Ditemukan',
                'Status' => false
            ], 401);
        }

        $years = Tahun::where('ProdiID', $dataMhsw->ProdiID,)->whereBetween(
            'TahunID',
            [$dataMhsw->TahunID, $tahun_aktif]
        )->pluck('TahunID');

        if ($years == []) {
            return response()->json([
                'Message' => 'Semester Tidak Dtemukan',
                'Status' => false
            ], 402);
        }

        $unique_year = [];

        foreach ($years as $year) {
            if (!in_array($year, $unique_year)) {
                $unique_year[] = $year;
            }
        }

        $semester = count($unique_year);

        $date = Carbon::now()->format('Y');

        $dataMk = mk::where([
            'ProdiID' => $dataMhsw->ProdiID,
            'Sesi' => $semester,

        ])->where('TglBuat', 'like',  '%' . $date . '%')->pluck('MKID');

        if ($dataMk == []) {
            return response()->json([
                'Message' => 'Mata Kuliah Tidak Ditemukan',
                'Status' => false
            ], 403);
        }

        $dataAll =
            DB::table('mk')
            ->whereIn('mk.MKID', $dataMk)
            ->join('jadwal', 'mk.MKID', '=', 'jadwal.MKID')
            ->join('hari', 'jadwal.HariID', '=', 'hari.HariID')
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->select(
                'jadwal.JadwalID as Jadwal_id',
                'jadwal.TahunID as Tahun_id',
                'jadwal.ProdiID as Prodi',
                'jadwal.ProgramID as Program',
                'jadwal.MKID as Mk_id',
                'jadwal.MKKode as Mk_kode',
                'jadwal.Nama as Nama_mk',
                'hari.Nama as Hari',
                'jadwal.JamMulai as Jam_mulai',
                'jadwal.JamSelesai as Jam_selesai',
                'jadwal.SKS',
                'jadwal.TglMulai as Tanggal_mulai',
                'jadwal.TglSelesai as Tanggal_selesai',
                'dosen.Nama as dosen',
            )
            ->get();


        return response()->json([
            'Message' => 'Berhasil Mengambil Data',
            'Status' => true,
            'Data' => $dataAll
        ]);
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
