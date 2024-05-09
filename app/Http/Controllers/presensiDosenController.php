<?php

namespace App\Http\Controllers;


use App\Models\jadwal_dosen;
use App\Models\Mhsw;
use App\Models\presensi_dosen;
use App\Models\PresensiMhsw;
use App\Models\Tahun;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\IsEmpty;

use function PHPUnit\Framework\isEmpty;

class presensiDosenController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dosen_id' => 'required',
            'jadwal_id' => 'required',
            'pertemuan' => 'required',
            'tanggal' => 'date|date_format:Y-m-d',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $dosen_id = $request->dosen_id;
        $jadwal_id = $request->jadwal_id;
        $pertemuan = $request->pertemuan;
        $tanggal = $request->tanggal;
        $jam_mulai = $request->jam_mulai;
        $jam_selesai = $request->jam_selesai;

        // UBAH NA NYA JADI N
        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'KEP',
            'ProgramID' => 'PG'
        ])->pluck('TahunID');

        $data = presensi_dosen::where([
            'JadwalID' => $jadwal_id,
            'TahunID' => $tahun_aktif[0],
            'Pertemuan' => $pertemuan
        ])->get();

        if (count($data) != 0) {
            return response()->json([
                'message' => 'Pertemuan sudah ada',
                'status' => false
            ], 401);
        }

        $insert = [
            'HonorDosenID' => 0,
            'TahunID' => $tahun_aktif[0],
            'JadwalID' => $jadwal_id,
            'Pertemuan' => $pertemuan,
            'DosenID' => $dosen_id,
            'Tanggal' => $tanggal,
            'JamMulai' => $jam_mulai,
            'JamSelesai' => $jam_selesai,
            'Hitung' => 'N',
            'Catatan' => '',
            'SKSHonor' => 0,
            'TunjanganSKS' => 0,
            'TunjanganTransport' => 0,
            'TunjanganTetap' => 0,
            'NA' => 'N',
            'LoginBuat' => 'Mobile',
            'TanggalBuat' => $tanggal,
            'LoginEdit' => null,
            'TanggalEdit' => $tanggal
        ];

        $result =  DB::table('presensi')->insert(
            array($insert)
        );

        if (!$result) {
            return response()->json([
                'message' => 'gagal membuat pertemuan',
                'status' => false
            ], 404);
        }
        return response()->json([
            'message' => 'berhasil membuat pertemuan',
            'status' => true
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jadwal_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $jadwal_id = $request->jadwal_id;

        // UBAH NA NYA JADI N
        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'KEP',
            'ProgramID' => 'PG'
        ])->pluck('TahunID');

        $data = presensi_dosen::where([
            'JadwalID' => $jadwal_id,
            'TahunID' => $tahun_aktif[0]
        ])->get();

        $total = $data->count();

        if (!$data) {
            return response()->json([
                'message' => 'belum ada pertemuan',
                'status' => true
            ]);
        }

        return response()->json([
            'message' => 'berhasil mengambil data',
            'status' => true,
            'total' => $total,
            'data' => $data
        ]);
    }

    public function getPresensiMhsw(Request $request)
    {

        $data = DB::table('presensimhsw')
            ->where('PresensiID', $request->presensi_id)
            //ubah join mhsw to join users
            ->join('mhsw', 'presensimhsw.MhswID', '=', 'mhsw.MhswID')
            ->leftJoin('users', 'presensimhsw.MhswID', '=', 'users.npm')
            ->join('jenispresensi', 'presensimhsw.JenisPresensiID', '=', 'jenispresensi.JenisPresensiID')
            ->select([
                'mhsw.Nama as nama',
                'mhsw.MhswID as npm',
                'users.image as image',
                'jenispresensi.Nama as status',
                'jenispresensi.Nilai as nilai',
            ])
            ->get();

        if (count($data) == 0) {
            return response()->json([
                'data' => 'Tidak ada mahasiswa'
            ]);
        }
        return response()->json([
            'total' => count($data),
            'data' => $data,
        ]);
    }

    public function delete($id)
    {

        $data = presensi_dosen::where('PresensiID', $id)->delete();
        $presensi_mhsw = PresensiMhsw::where('PresensiID', $id)->delete();

        if (!$presensi_mhsw) {
            return response()->json([
                'message' => 'Data Berhasil Dihapus'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data Gagal Dihapus'
            ]);
        }
    }

    public function detailPresensiMhsw(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'jadwal_id' => 'required',
            'presensi_id' => 'required',
            'npm' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $data = DB::table('presensimhsw')->where([
            'JadwalID' => $req->jadwal_id,
            'PresensiID' => $req->presensi_id,
            'MhswID' => $req->npm,
        ])->get();

        $value = 0;

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Mahasiswa belum absen',
                'value' => $value,
                'status' => false
            ]);
        }

        if ($data[0]->JenisPresensiID == 'H') {
            $value = 1;
        } elseif ($data[0]->JenisPresensiID == 'I') {
            $value = 2;
        } elseif ($data[0]->JenisPresensiID == 'S') {
            $value = 3;
        } else {
            $value = 4;
        }

        return response()->json([
            'message' => 'Berhasil mengambil data',
            'value' => $value,
            'data' => $data
        ]);
    }
}
