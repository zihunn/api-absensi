<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\Jadwal_Mhsw;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\Ruangan;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\DB;
use SplSubject;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        $dataMhsw = Mhsw::where('MhswID', $request->npm)->first();

        // UBAH NA NYA JADI N
        $tahun_aktif = Tahun::where([
            'TahunID' => '20232', // JANGAN LUPA DIHAPUS
            'NA' => 'Y',
            'ProdiID' => $dataMhsw->ProdiID,
            'ProgramID' => $dataMhsw->ProgramID
        ])->pluck('TahunID');

        $data = krs::where('MhswID', $request->npm)->whereIn('TahunID', $tahun_aktif)->get();
        $jadwal = Jadwal::whereIn('JadwalID', $data->pluck('JadwalID'))->get();
        $subjects = $jadwal->pluck('Nama');
        $startTimes = $jadwal->pluck('JamMulai');
        $timeOvers = $jadwal->pluck('JamSelesai');
        $startDate = $jadwal->pluck('TglMulai');
        $dateCompletion = $jadwal->pluck('TglSelesai');
        $name_mhsw = Mhsw::where('MhswID', $request->npm)->pluck('Nama');
        $dosen = [];
        $dosenID = [];
        $gelarDepan = [];
        $gelarBelakang = [];
        $days = [];
        $count = $startTimes->count();

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 401);
        }

        foreach ($jadwal->pluck('DosenID') as $id) {
            $xmodel = Dosen::where('Login', $id)->pluck('Nama');
            if ($xmodel) {
                array_push($dosen, $xmodel);
            }
        }
        foreach ($jadwal->pluck('DosenID') as $id) {
            $xmodel = Dosen::where('Login', $id)->pluck('GelarDepan');
            if ($xmodel) {
                array_push($gelarDepan, $xmodel);
            }
        }
        foreach ($jadwal->pluck('DosenID') as $id) {
            $xmodel = Dosen::where('Login', $id)->pluck('Gelar');
            if ($xmodel) {
                array_push($gelarBelakang, $xmodel);
            }
        }
        foreach ($jadwal->pluck('DosenID') as $id) {
            $xmodel = Dosen::where('Login', $id)->pluck('Login');
            if ($xmodel) {
                array_push($dosenID, $xmodel);
            }
        }
        foreach ($jadwal->pluck('HariID') as $ids) {
            $xmode = Hari::where('HariID', $ids)->pluck('Nama');
            if ($xmode) {
                array_push($days, $xmode);
            }
        }
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $arrayNpm[] = $request->npm;
            $arrayName[] = $name_mhsw;
            $result[] = [
                'npm' => $arrayNpm[$i],
                'nama_mhsw' => $arrayName[$i][0],
                'matkul' => $subjects[$i],
                'tahun_id' => $data->pluck('TahunID')[$i],
                'hari' => $days[$i][0],
                'jam_mulai' => $startTimes[$i],
                'jam_selesai' => $timeOvers[$i],
                'tanggal_mulai' => $startDate[$i],
                'tanggal_selesai' => $dateCompletion[$i],
                'dosen' => implode('', [$gelarDepan[$i][0], $dosen[$i][0], ' ', $gelarBelakang[$i][0]]),
                'dosenID' => $dosenID[$i][0]
            ];

            $data_mhsw = Jadwal_Mhsw::where([
                'npm' => $request->npm,
                'tahun_id' => $tahun_aktif
            ])->get();

            $collection = collect($result);
        }
        $input = $collection->whereNotIn('matkul', $data_mhsw->pluck('matkul'))->all();

        $regis = Jadwal_Mhsw::insert($input);

        if (!$regis) {
            return response()->json(
                [
                    'success' => false
                ],
                403
            );
        };
        return response()->json([
            'message' => 'success',
            'data' => $regis,
        ], 200);
    }

    static $list = [];
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
            'date' => 'date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }


        $npm = $request->npm ?? null;
        $date = $request->date ?? null;

        $dataMhsw = app(User::class)->newQuery();

        if (!empty($npm)) {
            $dataMhsw = $dataMhsw->where('npm', $npm)->first();
        }
        // UBAH NA NYA JADI N !!!
        $tgl = Tahun::where([
            'NA' => 'N',
            'ProdiID' => $dataMhsw->prodiID,
            'ProgramID' => $dataMhsw->programID
        ])->first();



        $tglMulai = explode(' ', $tgl->TglKuliahMulai)[0];

        $tglSelesai = explode(' ', $tgl->TglKuliahSelesai)[0];


        $begin = new DateTime($tglMulai);

        $end   = new DateTime($tglSelesai);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $var = $i->format("Y-m-d");
            self::$list[] = [
                'data' => $i->format("Y-m-d"),
                'days' => Carbon::parse($i)->translatedFormat('l')
            ];
        }

        // $day = collect(self::$list)->where('data',  $date)->first();

        $collection = collect(self::$list);

        $day = $collection->where('data', $date)->pluck('days');


        if (!$day) {
            return response()->json([
                'message' => 'Tanggal tidak valid',
                'success' => false,
                'total' => 0,
                'data' => $day
            ], 401);
        }

        $data = DB::table('krs')
            ->where([
                'MhswID' => $dataMhsw->npm,
                'krs.TahunID' => $tgl->TahunID,
                // 'JadwalID' =>'3255'
            ])
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->join('hari', 'jadwal.HariID', '=', 'hari.HariID')
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->join('mk', 'jadwal.MKID', '=', 'mk.MKID')
            ->join('users', 'krs.MhswID', '=', 'users.npm')
            ->where('hari.Nama', $day)
            ->select([
                'krs.MhswID as npm',
                'users.nama as nama_mhsw',
                'jadwal.Nama as matkul',
                'hari.Nama as hari',
                'krs.TahunID as tahun_id',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.JamSelesai as jam_selesai',
                'jadwal.TglMulai as tanggal_mulai',
                'jadwal.TglSelesai as tanggal_selesai',
                'dosen.Nama as dosen',
                'dosen.Login as dosenID',
                'krs.KRSID as krs_id',
                'jadwal.JadwalID as jadwal_id'
            ])->get();

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Tidak ada matkul hari ini',
                'total' => count($data),
                'success' => false,
                'data' => $data
            ], 201);
        }

        return response()->json([
            'message' => 'Berhasil menggambil data',
            'success' => true,
            'total' => count($data),
            'data' => $data
        ], 200);
    }

    public function getJadwalDosen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dosen_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $date = $request->date ?? null;

        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'KEP',
            'ProgramID' => 'PG'
        ])->first();

        $dataDosen = app(User::class)->newQuery();

        if (empty($dosen_id)) {
            $dataDosen = $dataDosen->where('dosen_id', $request->dosen_id)->first();
        }

        $tglMulai = explode(' ', $tahun_aktif->TglKuliahMulai)[0];

        $tglSelesai = explode(' ', $tahun_aktif->TglKuliahSelesai)[0];


        $begin = new DateTime($tglMulai);

        $end   = new DateTime($tglSelesai);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $var = $i->format("Y-m-d");
            self::$listJadwal[] = [
                'data' => $i->format("Y-m-d"),
                'days' => Carbon::parse($i)->translatedFormat('l')
            ];
        }

        $day = null;
        $day = collect(self::$listJadwal)->where('data',  $date)->first();

        if (!$day) {
            return response()->json([
                'message' => 'Tanggal tidak valid',
                'success' => false,
                'total' => 0,
                'data' => $day
            ], 401);
        }

        $data = DB::table('jadwal')
            ->where([
                'jadwal.DosenID' => $dataDosen->dosen_id,
                'jadwal.TahunID' => $tahun_aktif->TahunID
            ])
            ->join('hari', 'jadwal.HariID', '=', 'hari.HariID')
            ->where('hari.Nama', $day['days'])
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->join('mk', 'jadwal.MKID', '=', 'mk.MKID')
            ->leftJoin('presensi', 'jadwal.JadwalID', '=', 'presensi.JadwalID')
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
                'mk.Sesi as Semester',
                DB::raw('COUNT(presensi.JadwalID) as total_presensi')
            )
            ->groupBy('jadwal.JadwalID', 'jadwal.TahunID', 'jadwal.ProdiID', 'jadwal.ProgramID', 'jadwal.MKID', 'jadwal.MKKode', 'jadwal.Nama', 'hari.Nama', 'jadwal.JamMulai', 'jadwal.JamSelesai', 'jadwal.SKS', 'jadwal.TglMulai', 'jadwal.TglSelesai', 'dosen.Nama', 'mk.Sesi')
            ->get();

        $listMK = Jadwal::where(
            'TahunID',
            $tahun_aktif->TahunID
        )->pluck('JadwalID');

        $dataSec = DB::table('jadwaldosen')
            ->whereIn(
                '.jadwaldosen.JadwalID',
                $listMK,
            )
            ->where('jadwaldosen.DosenID', $dataDosen->dosen_id)
            ->join('jadwal', 'jadwaldosen.JadwalID', '=', 'jadwal.JadwalID')
            ->where('hari.Nama', $day['days'])
            ->join('hari', 'jadwal.HariID', '=', 'hari.HariID')
            ->join('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->join('mk', 'jadwal.MKID', '=', 'mk.MKID')
            ->where('hari.Nama', $day['days'])
            ->leftJoin('presensi', 'jadwal.JadwalID', '=', 'presensi.JadwalID')

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
                'mk.Sesi as Semester',
                DB::raw('COUNT(presensi.JadwalID) as total_presensi')
            )
            ->groupBy('jadwal.JadwalID', 'jadwal.TahunID', 'jadwal.ProdiID', 'jadwal.ProgramID', 'jadwal.MKID', 'jadwal.MKKode', 'jadwal.Nama', 'hari.Nama', 'jadwal.JamMulai', 'jadwal.JamSelesai', 'jadwal.SKS', 'jadwal.TglMulai', 'jadwal.TglSelesai', 'dosen.Nama', 'mk.Sesi')
            ->get();

        if ((count($data) == 0) && (count($dataSec) == 0)) {
            return response()->json([
                'message' => 'Tidak ada matkul hari ini',
                'total' => count($data),
                'success' => false,
            ], 201);
        }
        $dataGabungan = $data->merge($dataSec);

        $dataGabungan = $dataGabungan->unique('Jadwal_id');

        return response()->json([
            'message' => 'berhasil mengambil data',

            'total' => count($data) + count($dataSec),
            'status' => true,
            'data' => $dataGabungan
        ], 200);
    }

    static $listJadwal = [];
    public function getJadwalDosenTime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dosen_id' => 'required',
            'date' => 'date|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }


        $dosen_id = $request->dosen_id ?? null;
        $date = $request->date ?? null;

        $dataDosen = app(User::class)->newQuery();

        if (!empty($dosen_id)) {
            $dataDosen = $dataDosen->where('dosen_id', $dosen_id)->first();
        }
        // UBAH NA JADI N
        $tgl = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'NERS',
        ])->first();
        // $tgl = Tahun::where([
        //     'NA' => 'N',
        //     'ProdiID' => 'KEP'
        // ])->get()->last();
        $tglMulai = explode(' ', $tgl->TglKuliahMulai)[0];

        $tglSelesai = explode(' ', '2024-03-10 00:00:00')[0];


        $begin = new DateTime($tglMulai);

        $end   = new DateTime($tglSelesai);

        for ($i = $begin; $i <= $end; $i->modify('+1 day')) {
            $var = $i->format("Y-m-d");
            self::$listJadwal[] = [
                'data' => $i->format("Y-m-d"),
                'days' => Carbon::parse($i)->translatedFormat('l')
            ];
        }

        $day = null;
        $day = collect(self::$listJadwal)->where('data',  $date)->first();

        if (!$day) {
            return response()->json([
                'message' => 'Tanggal tidak valid',
                'success' => false,
                'total' => 0,
                'data' => $day
            ], 401);
        }

        // $data = jadwal_dosen::where([
        //     'dosen_id' => $dosen_id,
        //     'hari' => $day['days']
        // ])->get();

        // if (count($data) == 0) {
        //     return response()->json([
        //         'message' => 'Tidak ada matkul hari ini',
        //         'total' => count($data),
        //         'success' => false,
        //         // 'data' => $day
        //     ], 201);
        // }

        return response()->json([
            'message' => 'Berhasil menggambil data',
            'success' => true,
            // 'total' => count($data),
            'data' => $day
        ], 200);
    }
}
