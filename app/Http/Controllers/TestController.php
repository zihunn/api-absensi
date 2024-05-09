<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\jadwal_dosen;
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

class TestController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        $data = User::where('npm', $request->npm)->pluck('tahun_id');

        return response()->json([
            'data' => Tahun::whereBetween('tahunID', [$data, '20231'])->distinct()->pluck('TahunID')
        ]);
    }

    public function index2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
        ]);

        $data = User::where('npm', $request->npm)->first();

        return response()->json([
            'hadir' => $data->hadir,
            'izin' =>$data->izin,
            'sakit' =>$data->sakit,
            'alpa' =>$data->alpa,
        ]);
    }
}
