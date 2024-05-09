<?php

namespace App\Http\Controllers;

use App\Models\todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($status)
    {
        $data = todo::where('status', $status)->orderBy('date', 'desc')->get();

        if (count($data) == 0) {
            return response()->json([
                'message' => 'Tidak ada task',
                'status' => false
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mengambil data',
            'status' => true,
            'total' => count($data),
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npm' => 'required',
            'title_task' => 'required|string',
            'desc_task' => 'required|max:255',
            'status' => 'required|string',
            'category' => 'required|string',
            'date' => 'required',
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false
            ], 400);
        }

        $npm = $request->npm ?? null;
        $title_task = $request->title_task ?? null;
        $desc_task = $request->desc_task ?? null;
        $status = $request->status ?? null;
        $category = $request->category ?? null;
        $date = $request->date ?? null;
        $time = $request->time ?? null;

        $insert = todo::create([
            'npm' => $npm,
            'title_task' => $title_task,
            'desc_task' => $desc_task,
            'status' => $status,
            'category' => $category,
            'date' => $date,
            'time' => $time,
        ]);

        if (!$insert) {
            return response()->json([
                'message' => 'Gagal menambahkan task',
                'status' => false
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil menambahkan task',
            'status' => true,
            'data' => $insert
        ]);
    }

    public function edit(Request $request)
    {
        $input = $request->except('_method');

        try {
            todo::where('id', $request->id)->update($input);

            $data = todo::where('id', $request->id)->first();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Update',
                'matkul' => $data,

            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal Update',

            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delete = todo::where('id', $id)->delete();

        if ($delete) {
            return response()->json([
                'message' => 'Data Berhasil Dihapus'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data Gagal Dihapus'
            ]);
        }
    }
}
