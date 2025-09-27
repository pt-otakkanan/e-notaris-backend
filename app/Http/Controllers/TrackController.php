<?php

namespace App\Http\Controllers;

use App\Models\Track;
use App\Models\Activity;
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

    public function lookupByCode(Request $request, string $code)
    {
        $activity = Activity::with([
            'track',
            'deed:id,name',
            'notaris:id,name,email',
        ])->where('tracking_code', $code)->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking code tidak ditemukan.',
            ], 404);
        }

        if (!$activity->track) {
            // belum ada track terasosiasi
            return response()->json([
                'success' => true,
                'message' => 'Track belum tersedia untuk aktivitas ini.',
                'data' => [
                    'activity' => [
                        'id'            => $activity->id,
                        'name'          => $activity->name,
                        'tracking_code' => $activity->tracking_code,
                        'deed'          => $activity->deed?->name,
                        'notaris'       => $activity->notaris?->name,
                        'status_approval' => $activity->status_approval, // accessor
                    ],
                    'steps' => [],
                    'current_step' => null,
                    'progress_percent' => 0,
                    'is_done' => false,
                ],
            ], 200);
        }

        $t = $activity->track;

        // Urutan langkah (atur label sesuai kebutuhan)
        $steps = [
            ['key' => 'invite',   'label' => 'Undang',         'status' => (string)$t->status_invite],
            ['key' => 'respond',  'label' => 'Respon Klien',   'status' => (string)$t->status_respond],
            ['key' => 'docs',     'label' => 'Dokumen',        'status' => (string)$t->status_docs],
            ['key' => 'draft',    'label' => 'Draft',          'status' => (string)$t->status_draft],
            ['key' => 'schedule', 'label' => 'Jadwal Baca',    'status' => (string)$t->status_schedule],
            ['key' => 'sign',     'label' => 'Tanda Tangan',   'status' => (string)$t->status_sign],
            ['key' => 'print',    'label' => 'Cetak/Final',    'status' => (string)$t->status_print],
        ];

        // Hitung progres
        $total = count($steps);
        $done  = 0;
        foreach ($steps as $s) {
            if (strtolower($s['status']) === 'done') $done++;
        }
        $progressPercent = $total > 0 ? (int) floor(($done / $total) * 100) : 0;

        // Tentukan current_step:
        // prioritas: pertama yang status-nya bukan 'done' (mis. 'todo'/'pending'/'rejected')
        $current = null;
        foreach ($steps as $idx => $s) {
            if (strtolower($s['status']) !== 'done') {
                $current = [
                    'index' => $idx,          // 0-based
                    'key'   => $s['key'],
                    'label' => $s['label'],
                    'status' => $s['status'],
                ];
                break;
            }
        }
        // jika semua done
        $isDone = $current === null;

        return response()->json([
            'success' => true,
            'message' => 'Progres track berhasil diambil.',
            'data' => [
                'activity' => [
                    'id'              => $activity->id,
                    'name'            => $activity->name,
                    'tracking_code'   => $activity->tracking_code,
                    'deed'            => $activity->deed?->name,
                    'notaris'         => $activity->notaris?->name,
                    'status_approval' => $activity->status_approval, // accessor dari model
                ],
                'steps'            => $steps,           // array urutan langkah
                'current_step'     => $current,         // null jika selesai
                'progress_percent' => $progressPercent, // 0..100
                'is_done'          => $isDone,
            ],
        ], 200);
    }

    public function lookupByCodePost(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
        ]);

        return $this->lookupByCode($request, $request->input('tracking_code'));
    }
}
