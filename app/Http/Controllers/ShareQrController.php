<?php

namespace App\Http\Controllers;

use App\Models\ShareQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShareQrController extends Controller
{
    public function show(Request $request)
    {
        $jadwal_id = $request->input('jadwal_id');
        $dosen_id = $request->input('dosen_id');

        if (empty($jadwal_id)) {
            return response()->json(['error' => 'jadwal_id is required'], 400);
        }

        $query = ShareQR::where('jadwal_id', $jadwal_id);
        $query->select('*'); // Memilih semua kolom yang ada

        $data = $query->get();

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Belum ada qr code',
                'status' => false,
            ]);
        }

        // Menambahkan atau menyesuaikan isSender secara manual
        foreach ($data as $item) {
            $item->isSender = (!empty($dosen_id) && $item->dosen_id == $dosen_id);
        }

        return response()->json($data);
    }

    public function delete($id)
    {

        $delete = ShareQr::where('id', $id)->delete();

        if ($delete) {
            return response()->json([
                'message' => 'gagal menghapus data',
                'status' => false,
            ]);
        }
        return response()->json([
            'message' => 'berhasil',
            'status' => '',
        ]);
    }
}
