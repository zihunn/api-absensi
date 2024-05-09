<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Jadwal;
use App\Models\jadwaldosen;
use App\Models\krs;
use App\Models\Mhsw;
use App\Models\Tahun;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function search(Request $request)
    {
        $MhswID = $request->MhswID;
        $Nama = $request->Nama;
        $jadwal_id = $request->jadwal_id;
        $member_info = null;

        $tahun_aktif = Tahun::where([
            'NA' => 'N',
            'ProdiID' => 'KEP',
            'ProgramID' => 'PG'
        ])->pluck('TahunID');


        $member_info = DB::table('krs')
            ->where([
                'krs.JadwalID' => $jadwal_id,
                'krs.TahunID' => $tahun_aktif[0]
            ])
            ->join('mhsw', 'krs.MhswID', '=', 'mhsw.MhswID')
            ->leftJoin('users', 'krs.MhswID', '=', 'users.npm')
            ->select([
                'mhsw.Nama as nama',
                'krs.KRSID as krs_id',
                'krs.JadwalID as jadwal_id',
                'mhsw.MhswID as npm',
                'users.image as image',
            ])
            ->get();


        if (count($member_info) == 0) {
            return response()->json([
                'data' => 'data tidak ada'
            ]);
        }


        return Response()->json([
            'message' => 'success',
            'status' => true,
            'total' => count($member_info),
            'data' => $member_info
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function searchDosen(Request $request)
    {

        $nama = $request->nama;
        $excludeName = $request->excludeName;

        $query = User::where('nama', 'LIKE', "%{$nama}%")
            ->orderBy('nama', 'asc')
            ->where('role', 'Dosen')
            ->where('nama', 'NOT LIKE', "%{$excludeName}%");


        // Ambil hanya 9 data pertama
        $data = $query->take(9)->get()->map(function ($item) {
            return [
                'nama' => $item->nama,
                'dosen_id' => $item->npm,
                'email' => $item->email,
                'image' => $item->image,
            ];
        });

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Dosen tidak ditemukan',
                'status' => false,
                'total' => count($data),
                'data' => $data,
            ], 404);
        }

        return response()->json([
            'message' => 'berhasil mengambil data',
            'status' => true,
            'total' => count($data),
            'data' => $data,
        ], 200);
    }

    public function getDosenMk($jadwal_id)
    {

        $data = DB::table('jadwal')
            ->where('jadwal.JadwalID', $jadwal_id)
            ->leftJoin('jadwaldosen', 'jadwal.JadwalID', '=', 'jadwaldosen.JadwalID') // Menggunakan leftJoin di sini
            ->join('users', function ($join) {
                $join->on('jadwal.DosenID', '=', 'users.npm')
                    ->orOn('jadwaldosen.DosenID', '=', 'users.npm');
            })
            ->select(
                'users.npm as dosen_id',
                DB::raw('MAX(jadwaldosen.JadwalDosenID) as id'),
                DB::raw('MAX(users.nama) as nama'),
                DB::raw('MAX(users.email) as email'),
                DB::raw('MAX(users.image) as image'),
                DB::raw('MAX(CASE WHEN jadwal.DosenID = users.npm THEN true ELSE false END) as dosen_utama')
            )
            ->groupBy('users.npm')
            ->orderByRaw('MAX(CASE WHEN jadwal.DosenID = users.npm THEN 1 ELSE 0 END) DESC')
            ->get();

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Dosen tidak ditemukan',
                'status' => false,
                'total' => count($data),
            ], 404);
        }
        return response()->json([
            'message' => 'berhasil mengambil data',
            'status' => true,
            'total' => count($data),
            'data' => $data
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Ambil semua data dari tabel dosen
            $dosens = Dosen::all();

            foreach ($dosens as $dosen) {
                // Buat instance baru dari model User
                $user = new User();


                $user->nama = $dosen->Nama;
                $user->npm = $dosen->Login;
                $user->email = $dosen->Email;
                $user->device_id = null;
                $user->tanggal_lahir = $dosen->TanggalLahir;
                $user->no_hp = $dosen->Handphon;
                $user->password = bcrypt('dosen123');
                $user->sks = null;
                $user->hadir = null;
                $user->izin = null;
                $user->sakit = null;
                $user->alpa = null;
                $user->jenis_kelamin = '-';
                $user->prodi_id = null;
                $user->prodi_en = null;
                $user->prodiID = null;
                $user->programID = null;
                $user->tahun_id = null;
                $user->dosen_id = $dosen->Login;
                $user->nidn = $dosen->NIDN;
                $user->gelar_depan = $dosen->GelarDepan;
                $user->gelar_belakang = $dosen->Gelar;
                $user->tempat_lahir = $dosen->TempatLahir;
                $user->role = 'Dosen';
                $user->status_krs = null;
                // Tambahkan lebih banyak field sesuai kebutuhan

                $user->save(); // Simpan data user
            }

            DB::commit(); // Commit transaksi jika tidak ada error
            return response()->json(['message' => 'Data berhasil dipindahkan dari dosen ke user.']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            return response()->json(['message' => 'Terjadi kesalahan saat memindahkan data.', 'error' => $e->getMessage()]);
        }
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
