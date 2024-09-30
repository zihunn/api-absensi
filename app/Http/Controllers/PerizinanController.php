<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\jadwaldosen;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\Perizinan;
use App\Models\PresensiMhsw;
use App\Models\Tahun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PerizinanController extends Controller
{
    public function index(Request $request)
    {
        $npm = $request->npm ?? null;
        $dosen_id = $request->dosen_id ?? null;
        $data_perizinan = null;

        if ($npm) {
            $data_perizinan = DB::table('perizinan_presensi')
                ->where('perizinan_presensi.npm', $npm)
                ->orderBy('perizinan_presensi.created_at', 'desc')
                ->join('mhsw', 'perizinan_presensi.npm', '=', 'mhsw.MhswID')
                ->join('jadwal', 'perizinan_presensi.jadwal_id', '=', 'jadwal.JadwalID')
                ->join('hari', 'jadwal.HariID', '=', 'hari.HariID')
                ->select(
                    'perizinan_presensi.id as id',
                    'perizinan_presensi.presensi_id as presensi_id',
                    'perizinan_presensi.jadwal_id as jadwal_id',
                    'hari.Nama as hari',
                    'jadwal.JamMulai as jam_mulai',
                    'jadwal.JamSelesai as jam_selesai',
                    'jadwal.Nama as nama_mk',
                    'perizinan_presensi.npm as npm',
                    'mhsw.Nama as nama_mhsw',
                    'perizinan_presensi.description as keterangan',
                    'perizinan_presensi.category as kategori',
                    'perizinan_presensi.file as file',
                    'perizinan_presensi.file as file',
                    'perizinan_presensi.approve_by as disetujui',
                    'perizinan_presensi.created_at as tanggal',

                )
                ->get();

            if (count($data_perizinan) == 0) {
                return response()->json([
                    'message' => 'Data tidak ditemukan',
                    'status' => false
                ], 404);
            }

            return response()->json([
                'message' => 'berhasil mengambil data',
                'status' => true,
                'total_data' => count($data_perizinan),
                'data' => $data_perizinan
            ], 200);
        }
        $data_perizinan = DB::table('perizinan_presensi')
            ->where('perizinan_presensi.dosen_primary', $dosen_id)
            ->orWhere('perizinan_presensi.dosen_secondary', $dosen_id)
            ->orderBy('perizinan_presensi.created_at', 'desc')

            ->leftjoin('dosen', function ($join) {
                $join->on('dosen.Login', '=', 'perizinan_presensi.dosen_secondary');
                $join->orOn('dosen.Login', '=', 'perizinan_presensi.dosen_primary');
            })
            ->select(
                'perizinan_presensi.id as id',
                'perizinan_presensi.npm as npm',
                'dosen.Nama as nama_dosen',
                'perizinan_presensi.presensi_id as presensi_id',
                'perizinan_presensi.jadwal_id as jadwal_id',
                'perizinan_presensi.description as keterangan',
                'perizinan_presensi.category as kategori',
                'perizinan_presensi.file as file',
                'perizinan_presensi.file as file',
                'perizinan_presensi.approve_by as disetujui',
                'perizinan_presensi.created_at as tanggal',
                DB::raw("CASE WHEN perizinan_presensi.dosen_primary = '$dosen_id' THEN perizinan_presensi.read_primary 
                        WHEN perizinan_presensi.dosen_secondary = '$dosen_id' THEN perizinan_presensi.read_secondary 
                        ELSE NULL END as read_status")
            )
            ->get()
            ->map(function ($item) {
                $item->read_status = $item->read_status == 1;
                return $item;
            });

        if (count($data_perizinan) == 0) {
            return response()->json([
                'message' => 'Data tidak ditemukan',
                'status' => false
            ], 404);
        }

        return response()->json([
            'message' => 'berhasil mengambil data',
            'status' => true,
            'total_data' => count($data_perizinan),
            'data' => $data_perizinan
        ], 200);
    }

    public function readStatus(Request $request)
    {

        $dosenId = $request->input('dosen_id');
        $dataId = $request->input('id');

        $perizinan = Perizinan::find($dataId);

        if (!$perizinan) {
            return response()->json([
                'message' => 'Data not found',
                'status' => 'error'
            ], 404);
        }

        if ($perizinan->dosen_primary == $dosenId) {
            $perizinan->read_primary = 1;
        } elseif ($perizinan->dosen_secondary == $dosenId) {
            $perizinan->read_secondary = 1;
        } else {
            return response()->json([
                'message' => 'Unauthorized access',
                'status' => 'error'
            ], 403);
        }

        $perizinan->save();

        return response()->json([
            'message' => 'Berhasil mengubah data',
            'status' => true,
        ]);
    }

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'npm' => 'required',
            'jadwal_id' => 'required',
            'presensi_id' => 'required',
            'description' => 'required',
            'category' => 'in:Sakit,Izin,Alpa',
            'krs_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ]);
        }

        $npm = $request->npm;
        $jadwal_id = $request->jadwal_id;
        $presensi_id = $request->presensi_id;
        $description = $request->description;
        $category = $request->category;
        $krs_id = $request->krs_id;

        $dosen_primary = jadwal::where('JadwalID', $jadwal_id)->pluck('DosenID');
        $dosen_secondary = jadwaldosen::where('JadwalID', $jadwal_id)->pluck('DosenID');
        // $dosen_secondary = '';

        $created_at = Carbon::now();

        $data = Perizinan::where([
            'presensi_id' => $presensi_id,
            'npm' => $npm,
            'created_at' => $created_at->toDateString(),
        ])->get();

        if (count($data) != 0) {
            return response()->json([
                'message' => 'anda sudah melakukan perizinan pada pertemuan ini',
                'status' => false,
            ]);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $filename = time() . '_' . $file->getClientOriginalName();
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('image'), $filename);

            $file_url = url('image/' . $filename);
        }

        $dataMhsw = $this->getMahasiswa($npm);

        $prodi = $this->checkProdi($dataMhsw->ProdiID);

        $tahun = Tahun::where('TahunID', $dataMhsw->TahunID)->pluck('Nama');

        $tahun = explode(' ', $tahun)[0];
        $tahun = explode('-', $tahun);
        $tahun = substr($tahun[0], -2) . '-' . substr($tahun[1], -2);
        

        $input = Perizinan::create([
            'npm' => $npm,
            'prodi' => $prodi,
            'angkatan' => $tahun,
            'presensi_id' => $presensi_id,
            'jadwal_id' => $jadwal_id,
            'krs_id' => $krs_id,
            'description' => $description,
            'category' => $category,
            'file' => $file_url ?? null,
            'dosen_primary' => $dosen_primary[0],
            'dosen_secondary' => $dosen_secondary[0] ?? null,
            'created_at' => $created_at->toDateString()
        ]);

        if (!$input) {
            return response()->json([
                'message' => 'gagal membuat perizinan',
                'status' => false,
            ], 400);
        }

        return response()->json([
            'message' => 'berhasil membuat perizinan',
            'status' => true,
            'data' => $input,
        ], 200);
    }

    public function approve($id)
    {

        $datas = Perizinan::all();

        $data = $datas->find($id);

        $category = null;

        $nilai = 1;

        $data_presensi = PresensiMhsw::where([
            'PresensiID' => $data->presensi_id,
            'MhswID' => $data->npm
        ])->get();

        if (count($data_presensi) != 0) {
            return response()->json([
                'message' => 'Mahasiswa sudah melakukan absen',
                'status' => false,
            ]);
        }

        if ($data->category == 'Hadir') {
            $category = 'H';
        } elseif ($data->category == 'Izin') {
            $category = 'I';
        } elseif ($data->category == 'Sakit') {
            $category = 'S';
        } else {
            $category = 'M';
        }

        if ($category == 'M') {
            $nilai = 0;
        }

        $input = PresensiMhsw::create([
            'JadwalID' => $data->jadwal_id,
            'KRSID' => $data->krs_id,
            'PresensiID' => $data->presensi_id,
            'MhswID' => $data->npm,
            'JenisPresensiID' => $category,
            'Nilai' => $nilai,
        ]);

        if (!$input) {
            return response()->json([
                'message' => 'gagal membuat kehadiran',
                'status' => false,
            ], 400);
        }

        return response()->json([
            'message' => 'berhasil menambahkan kehadiaran',
            'status' => true,
        ]);
    }

    public function delete($id)
    {
        $delete = Perizinan::where('id', $id)->delete();

        if ($delete) {
            return response()->json([
                'message' => 'gagal menghapus data',
                'status' => false,
            ], 400);
        }
        return response()->json([
            'message' => 'berhasil menghapus data',
            'status' => true,
        ], 200);
    }

    public function showMk($npm)
    {

        $dataMhsw = $this->getMahasiswa($npm);
        if (!$dataMhsw) {
            return $this->responseError('Mahasiswa tidak ditemukan', 404);
        }

        $tahunAktif = $this->getTahunAktif($dataMhsw);
        if ($tahunAktif->isEmpty()) {
            return $this->responseError('Tahun aktif tidak ditemukan', 404);
        }

        $krsData = $this->getKrsDataFromDB($dataMhsw->MhswID, $tahunAktif);
        if ($krsData->isEmpty()) {
            return $this->responseError('Data KRS tidak ditemukan', 404);
        }

        $result = $this->constructKrsData($krsData);

        return response()->json([
            'message' => 'Berhasil mengambil data KRS',
            'data' => array_values($result), // Menggunakan array_values untuk menghilangkan key asal dari database
            'status' => true
        ], 200);
    }

    private function getMahasiswa($npm)
    {
        return Mhsw::where('MhswID', $npm)->first();
    }

    private function getTahunAktif($dataMhsw)
    {
        return Tahun::where([
            'ProgramID' => $dataMhsw->ProgramID,
            'ProdiID' => $dataMhsw->ProdiID,
            'NA' => 'N',
        ])->pluck('TahunID');
    }

    private function getKrsDataFromDB($mhswId, $tahunAktif)
    {
        return DB::table('krs')
            ->join('jadwal', 'krs.JadwalID', '=', 'jadwal.JadwalID')
            ->leftJoin('dosen', 'jadwal.DosenID', '=', 'dosen.Login')
            ->leftJoin('presensi', 'jadwal.JadwalID', '=', 'presensi.JadwalID')
            ->select(
                'jadwal.JadwalID as id',
                'krs.KRSID as krs_id',
                'jadwal.nama',
                'jadwal.JamMulai as jam_mulai',
                'jadwal.JamSelesai as jam_selesai',
                'presensi.Pertemuan as presensi_pertemuan',
                'presensi.PresensiID as presensi_id',
                'dosen.Nama as dosen'
            )
            ->where('krs.MhswID', $mhswId)
            ->where('krs.TahunID', $tahunAktif)
            ->get();
    }

    private function constructKrsData($data)
    {
        $result = [];
        foreach ($data as $row) {
            if (!isset($result[$row->id])) {
                // Jika ID ini belum ada dalam result, inisialisasi entri baru
                $result[$row->id] = [
                    'id' => $row->id,
                    'nama' => $row->nama,
                    'jam_mulai' => $row->jam_mulai,
                    'jam_selesai' => $row->jam_selesai,
                    'dosen' => $row->dosen,
                    'krs_id' => $row->krs_id,
                    'data_presensi' => [] // Sementara inisialisasi sebagai array kosong
                ];
            }

            // Tambahkan presensi ke array 'data_presensi' jika ada data presensi
            if ($row->presensi_pertemuan !== null) {
                $result[$row->id]['data_presensi'][] = [
                    'pertemuan' => $row->presensi_pertemuan,
                    'presensi_id' => $row->presensi_id
                ];
            }
        }

        // Cek setiap 'data_presensi', ubah menjadi null jika kosong
        foreach ($result as $key => $value) {
            if (empty($value['data_presensi'])) {
                $result[$key]['data_presensi'] = null;
            }
        }

        return array_values($result); // Kembalikan hanya nilai dari array untuk menghilangkan key berbasis 'id'
    }

    private function responseError($message, $statusCode)
    {
        return response()->json([
            'message' => $message,
            'status' => false
        ], $statusCode);
    }

    private function checkProdi($prodi_id)
    {
        $prodi = '';
        if ($prodi_id == 'KEB') {
            $prodi = 'Kebidanan';
        } elseif ($prodi_id == 'KES') {
            $prodi = 'Kesmas';
        } elseif ($prodi_id == 'KEP') {
            $prodi = 'Keperawatan';
        } elseif ($prodi_id == 'NERS') {
            $prodi = 'Profesi Ners';
        } elseif ($prodi_id == 'RMIK') {
            $prodi = 'Rekam Medis';
        } else {
            $prodi = 'Informatika';
        }
        return $prodi;
    }
}
