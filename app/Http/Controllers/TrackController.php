<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackController extends Controller
{
    /**
     * GET /tracks/{id}
     */
    public function show($id)
    {
        $track = Track::find($id);
        if (!$track) {
            return response()->json([
                'success' => false,
                'message' => 'Track tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail track berhasil diambil',
            'data'    => $track,
        ], 200);
    }

    /**
     * PATCH /tracks/{id}
     * Body: salah satu/lebih dari field status_*
     * Nilai yang diizinkan: pending|done|rejected (sesuai migration enum)
     */
    public function update(Request $request, $id)
    {
        $track = Track::find($id);
        if (!$track) {
            return response()->json([
                'success' => false,
                'message' => 'Track tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $rules = [
            'status_invite'   => 'sometimes|required|in:pending,done,rejected',
            'status_respond'  => 'sometimes|required|in:pending,done,rejected',
            'status_docs'     => 'sometimes|required|in:pending,done,rejected',
            'status_draft'    => 'sometimes|required|in:pending,done,rejected',
            'status_schedule' => 'sometimes|required|in:pending,done,rejected',
            'status_sign'     => 'sometimes|required|in:pending,done,rejected',
            'status_print'    => 'sometimes|required|in:pending,done,rejected',
        ];

        $validator = Validator::make($request->all(), $rules, [
            'in' => 'Status harus salah satu dari: pending, done, atau rejected.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        foreach ($data as $k => $v) {
            $track->{$k} = $v;
        }
        $track->save();

        return response()->json([
            'success' => true,
            'message' => 'Track berhasil diperbarui',
            'data'    => $track,
        ], 200);
    }
}
