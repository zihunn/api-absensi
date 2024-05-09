<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Dosen;
use App\Models\Jadwal_Mhsw;
use App\Models\Mhsw;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\krs;
use App\Models\Prodi;
use App\Models\Tahun;
use App\Models\User_Dosen;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'npm' => 'required',
            'password' => 'required',
            'device_id' => 'required',
            'role' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $nama = null;
        $tanggal_lahir = null;
        $prodi_id = null;
        $prodi_en = null;
        $prodiID = null;
        $programID = null;
        $TahunID = null;
        $dosen_id = null;
        $nidn = null;
        $gelar_depan = null;
        $gelar_belakang = null;
        $tempat_lahir = null;
        $krs = null;
        $jk = null;
        $npm = null;
        $total = null;
        $absen = null;
        $hadir = null;
        $izin = null;
        $sakit = null;
        $alpa = null;
        $prodi = null;
        $tahun_aktif = null;
        $no_hp = null;
        $device_id = null;
        $status_krs = null;

        if ($request->role == 'Mhsw') {
            $dataMhsw = Mhsw::where('MhswID', $request->npm)->first();

            if ($dataMhsw == null) {
                return response()->json([
                    'message' => 'Tidak di temukan',
                    'succcess' => false
                ], 401);
            }

            $tahun_aktif = Tahun::where([
                'NA' => 'N',
                'ProdiID' => $dataMhsw->ProdiID,
                'ProgramID' => $dataMhsw->ProgramID
            ])->pluck('TahunID');

            $krs = krs::where('MhswID', $request->npm)->where('TahunID', $tahun_aktif)->get();
            $jk = $dataMhsw->Kelamin;
            $npm = User::where('npm', $request->npm)->first();
            $total = $krs->sum('SKS');
            $absen = Absensi::where('MhswID', $request->npm)->get();
            $hadir = $absen->where('JenisPresensiID', 'H')->count();
            $izin = $absen->where('JenisPresensiID', 'I')->count();
            $sakit = $absen->where('JenisPresensiID', 'S')->count();
            $alpa = $absen->where('JenisPresensiID', 'M')->count();
            $prodi = Prodi::where('ProdiID', $dataMhsw['ProdiID'])->first();
            $prodi_id = $prodi['Nama'];
            $prodi_en = $prodi['Nama_en'];
            $prodiID = $dataMhsw['ProdiID'];
            $programID = $dataMhsw['ProgramID'];
            $TahunID = $dataMhsw['TahunID'];
            $tanggal_lahir = $dataMhsw['TanggalLahir'];
            $nama = $dataMhsw['Nama'];
            $no_hp = $dataMhsw['Telepon'];
            $device_id = $request->device_id;
            $status_krs = $dataMhsw['StatusSKS'];

            if ($npm != null) {
                return  response()->json([
                    'message' => 'Sudah Terdaftar'
                ], 402);
            }
        } else {
            $dataDosen = Dosen::where('Login', $request->npm)->first();
            $checkUser = User::where('dosen_id', $request->npm)->get();

            if ($dataDosen == null) {
                return response()->json([
                    'message' => 'Tidak di temukan',
                    'succcess' => false
                ], 401);
            }

            $dosen_id = $dataDosen->Login;
            $nidn = $dataDosen->NIDN;
            $gelar_depan = $dataDosen->GelarDepan;
            $gelar_belakang = $dataDosen->Gelar;
            $tempat_lahir = $dataDosen->TempatLahir;
            $nama = $dataDosen->Nama;

            if (!$checkUser) {
                return  response()->json([
                    $checkUser,
                    'message' => 'Sudah Terdaftar'
                ], 402);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $regis = User::create([
            'nama' => $nama,
            'npm' => $request->npm,
            'email' => $request->email,
            'device_id' => $request->device_id,
            'tanggal_lahir' => $tanggal_lahir,
            'no_hp' => $no_hp,
            'password' => bcrypt($request->password),
            'sks' => $total,
            'hadir' => $hadir,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpa' => $alpa,
            'jenis_kelamin' => '-',
            'prodi_id' => $prodi_id,
            'prodi_en' => $prodi_en,
            'prodiID' => $prodiID,
            'programID' => $programID,
            'tahun_id' => $TahunID,
            'dosen_id' => $dosen_id,
            'nidn' => $nidn,
            'gelar_depan' => $gelar_depan,
            'gelar_belakang' => $gelar_belakang,
            'tempat_lahir' => $tempat_lahir,
            'role' => $request->role,
            'status_krs' => $status_krs
        ]);

        if ($regis) {
            return response()->json([
                'data' => $regis,
                'success' => true,
                'message' => 'Registrasi Berhasil'
            ], 200);
        }
    }

    public function Login(Request $request)
    {
        if (!Auth::attempt(['npm' =>  $request->npm, 'password' => $request->password,])) {
            return response()->json([
                'success' => false,
                'message' => 'Npm atau password yang anda masukan salah'
            ], 400);
        }

        $user = User::where('npm', $request->npm)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User Tidak Ditemukan',
            ], 201);
        }

        if ($user->role == 'Dosen') {
            $token = $user->createToken("auth_token")->plainTextToken;
            $user->where('npm', $request->npm)->update(['remember_token' => $token]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Login',
                'account' => $user,
            ], 200);
        }

        if ($user['device_id'] != $request->device_id) {
            return response()->json([
                'message' => 'Silahkan login menggunakan device saat pertama kali daftar'
            ], 401);
        }

        $token = $user->createToken("auth_token")->plainTextToken;
        $user->where('npm', $request->npm)->update(['remember_token' => $token]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Login',
            'account' => User::where('npm', $request->npm)->first(),
        ], 200);
    }

    public function logout(Request $request)
    {
        // $request->user()->where('remember_token')
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }


    public function update(Request $request)
    {
        $input = $request->except('_method');

        // handle request Jika ada file
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // generate nama random
            $randomName = Str::random(45) . '.' . $image->getClientOriginalExtension();

            // Pinda ahkan file ke public/image dengan nama random tadi
            $image->move(public_path('image'), $randomName);
            $url = asset('image/' . $randomName);

            // replace input imagenya dengan url random tadi
            $input['image'] = $url;
        }

        try {

            User::where('npm', $request->npm)->update($input);

            $data = User::where('npm', $request->npm)->first();

            // $matkul = Jadwal_Mhsw::where('npm', $data->npm)->get();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil Login',
                'account' => $data,
                // 'matkul' => $matkul->count(),

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed Update Menu',
            ]);
        }
    }

    public function me(Request $request)
    {
        $data = Auth::user();
        // $matkul = Jadwal_Mhsw::where('npm', $data->npm)->get();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Login',
            'account' => $data,
            // 'matkul' => '',
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $new_password = $request->new_password;


        $user = $request->user();

        $user->update([
            'password' => bcrypt($new_password),
        ]);

        return response()->json([
            'message' => 'password berhasil diubah',
            'status' => $user
        ], 200);
    }
}
