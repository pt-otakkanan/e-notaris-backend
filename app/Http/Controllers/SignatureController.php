<?php
// app/Http/Controllers/SignatureController.php
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DraftDeed;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use setasign\Fpdi\Fpdi;

class SignatureController extends Controller
{
    /**
     * POST /signatures/capture
     * Body: activity_id, draft_deed_id, image (file) | image_base64 (data URL)
     * Hasil: simpan PNG tanda tangan user (current user)
     */
    public function capture(Request $request)
    {
        $user = $request->user();

        $valid = Validator::make($request->all(), [
            'activity_id'   => 'required|integer|exists:activities,id',
            'draft_deed_id' => 'required|integer|exists:draft_deeds,id',
            'image'         => 'sometimes|file|mimes:png|max:4096',
            'image_base64'  => 'sometimes|string', // data:image/png;base64,...
        ], [
            'image.mimes'   => 'Gambar harus PNG.',
            'image.max'     => 'Ukuran gambar maksimal 4MB.',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $valid->errors(),
            ], 422);
        }

        $data = $valid->validated();

        // Otorisasi: notaris boleh capture untuk dirinya; client untuk dirinya sendiri.
        // Admin selalu boleh. (Sesuaikan kebijakanmu.)
        $activity = Activity::find($data['activity_id']);
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }
        if ($user->role_id !== 1) {
            // jika notaris, harus pemilik activity
            if ($user->role_id == 3 && $activity->user_notaris_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }
            // jika client, harus termasuk klien activity (opsional: cek pivot)
            if ($user->role_id == 2) {
                $isClient = $activity->clients()->where('users.id', $user->id)->exists();
                if (!$isClient) return response()->json(['success' => false, 'message' => 'Bukan klien pada aktivitas ini'], 403);
            }
        }

        // Ambil blob
        $tmpPath = null;
        try {
            if ($request->hasFile('image')) {
                $tmpPath = $request->file('image')->getRealPath();
            } else {
                $b64 = (string)($data['image_base64'] ?? '');
                if (!preg_match('/^data:image\/png;base64,/', $b64)) {
                    return response()->json(['success' => false, 'message' => 'Format base64 tidak valid (harus PNG).'], 422);
                }
                $raw = base64_decode(substr($b64, strpos($b64, ',') + 1));
                $tmpPath = sys_get_temp_dir() . '/sig_' . uniqid() . '.png';
                file_put_contents($tmpPath, $raw);
            }

            // Upload ke Cloudinary
            $folder   = "enotaris/activities/{$data['activity_id']}/signatures";
            $filename = 'sig_' . $user->id . '_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload($tmpPath, [
                'folder'        => $folder . '/',
                'public_id'     => $filename,
                'overwrite'     => true,
                'resource_type' => 'image',
                'format'        => 'png',
            ]);

            $url = $upload->getSecurePath();

            // Buat / update 1 signature record untuk user ini (per activity/draft)
            $sig = Signature::updateOrCreate(
                [
                    'activity_id'   => $data['activity_id'],
                    'draft_deed_id' => $data['draft_deed_id'],
                    'user_id'       => $user->id,
                ],
                [
                    'image_url'  => $url,
                    'image_path' => $publicId,
                    // reset placement jika capture ulang (opsional)
                    // 'page' => null, 'x' => null, 'y' => null, 'width' => null, 'height' => null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan berhasil diunggah',
                'data'    => $sig,
            ], 201);
        } finally {
            if ($tmpPath && file_exists($tmpPath)) @unlink($tmpPath);
        }
    }

    /**
     * POST /signatures/place
     * Body: activity_id, draft_deed_id, page(1..), x, y, width, height, confirm (bool)
     * - Koordinat dalam POINT dengan origin KIRI-BAWA.
     * - Jika confirm=true → set signed_at = now (user telah menandatangani).
     */
    public function place(Request $request)
    {
        $user = $request->user();

        $valid = Validator::make($request->all(), [
            'activity_id'   => 'required|integer|exists:activities,id',
            'draft_deed_id' => 'required|integer|exists:draft_deeds,id',
            'page'          => 'required|integer|min:1',
            'x'             => 'required|numeric|min:0',
            'y'             => 'required|numeric|min:0',
            'width'         => 'required|numeric|min:1',
            'height'        => 'required|numeric|min:1',
            'confirm'       => 'sometimes|boolean',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $valid->errors(),
            ], 422);
        }

        $data = $valid->validated();

        // Pastikan signature record sudah ada & milik user ini
        $sig = Signature::where([
            'activity_id'   => $data['activity_id'],
            'draft_deed_id' => $data['draft_deed_id'],
            'user_id'       => $user->id,
        ])->first();

        if (!$sig) {
            return response()->json(['success' => false, 'message' => 'Signature belum diupload (capture terlebih dahulu).'], 404);
        }

        $sig->page   = (int)$data['page'];
        $sig->x      = (float)$data['x'];
        $sig->y      = (float)$data['y'];
        $sig->width  = (float)$data['width'];
        $sig->height = (float)$data['height'];

        if (filter_var($data['confirm'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $sig->signed_at = now();
            // audit
            $sig->meta = array_merge($sig->meta ?? [], [
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
            ]);
        }

        $sig->save();

        return response()->json([
            'success' => true,
            'message' => 'Penempatan tanda tangan disimpan',
            'data'    => $sig,
        ], 200);
    }

    /**
     * POST /signatures/finalize/{activity}
     * Ambil semua signatures yang punya image_url + placement, stamp ke PDF draft,
     * upload PDF hasil (replace draft->file), lalu set track.status_sign='done'.
     */
    public function finalize(Request $request, $activityId)
    {
        $user = $request->user();
        $activity = Activity::with(['draft', 'track'])->find($activityId);

        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }

        // Otorisasi: notaris / admin
        if ($user->role_id !== 1 && $activity->user_notaris_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak berhak finalisasi'], 403);
        }

        $draft = $activity->draft;
        if (!$draft || !$draft->file) {
            return response()->json(['success' => false, 'message' => 'Draft/PDF belum tersedia'], 422);
        }

        // Ambil signatures dengan gambar + koordinat lengkap + signed_at (opsional: wajib signed_at)
        $sigs = Signature::where('activity_id', $activity->id)
            ->where('draft_deed_id', $draft->id)
            ->whereNotNull('image_url')
            ->whereNotNull('page')
            ->whereNotNull('x')->whereNotNull('y')
            ->whereNotNull('width')->whereNotNull('height')
            ->get();

        if ($sigs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Belum ada tanda tangan yang siap ditempatkan'], 422);
        }

        // Download PDF sumber
        $srcTmp = tempnam(sys_get_temp_dir(), 'pdfsrc_');
        file_put_contents($srcTmp, file_get_contents($draft->file));

        // Siapkan output
        $dstTmp = tempnam(sys_get_temp_dir(), 'pdfout_');

        try {
            $fpdi = new Fpdi();

            $pageCount = $fpdi->setSourceFile($srcTmp);
            for ($p = 1; $p <= $pageCount; $p++) {
                $tpl = $fpdi->importPage($p);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl, 0, 0);

                // letakkan semua signature utk halaman ini
                foreach ($sigs as $sig) {
                    if ((int)$sig->page !== $p) continue;

                    // FPDI origin = kiri-ATAS, kita simpan origin kiri-BAWA → konversi:
                    $x = (float)$sig->x;
                    $w = (float)$sig->width;
                    $h = (float)$sig->height;
                    $yTop = $size['height'] - (float)$sig->y - $h;

                    // Download sementara PNG signature
                    $sigTmp = tempnam(sys_get_temp_dir(), 'sig_') . '.png';
                    file_put_contents($sigTmp, file_get_contents($sig->image_url));

                    $fpdi->Image($sigTmp, $x, $yTop, $w, $h, 'PNG');
                    @unlink($sigTmp);
                }
            }

            // simpan file
            $fpdi->Output($dstTmp, 'F');

            // Upload ke Cloudinary
            // Hapus file lama jika mau replace
            if (!empty($draft->file_path)) {
                try {
                    Cloudinary::destroy($draft->file_path);
                } catch (\Throwable $e) {
                    Log::warning($e->getMessage());
                }
            }

            $folder   = "enotaris/activities/{$activity->id}/signed";
            $filename = 'signed_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload($dstTmp, [
                'folder'        => $folder . '/',
                'public_id'     => $filename,
                'overwrite'     => true,
                'resource_type' => 'auto',
            ]);

            $draft->file      = $upload->getSecurePath();
            $draft->file_path = $publicId;
            $draft->save();

            // update track
            if ($activity->track) {
                $activity->track->status_sign = 'done';
                $activity->track->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil difinalisasi (ditandatangani)',
                'data'    => [
                    'file'      => $draft->file,
                    'file_path' => $draft->file_path,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Finalize sign error', ['message' => $e->getMessage(), 'activity' => $activityId]);
            return response()->json(['success' => false, 'message' => 'Gagal finalisasi: ' . $e->getMessage()], 500);
        } finally {
            if (file_exists($srcTmp)) @unlink($srcTmp);
            if (file_exists($dstTmp)) @unlink($dstTmp);
        }
    }
}
