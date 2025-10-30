<?php // app/Http/Controllers/SignController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Track;
use setasign\Fpdi\Fpdi;
use App\Models\Activity;
use App\Models\DraftDeed;
use App\Models\Signature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Signature as SignatureModel;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class SignController extends Controller
{

    public function apply(Request $request, $activityId)
    {
        try {
            $user = $request->user();

            $activity = Activity::with(['notaris', 'clients.identity', 'draft', 'track'])
                ->find($activityId);

            if (!$activity || !$activity->draft) {
                return response()->json(['success' => false, 'message' => 'Activity/draft tidak ditemukan'], 404);
            }

            if (($activity->track?->status_sign ?? null) === 'done') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen sudah ditandai selesai. TTD tidak dapat diubah lagi.'
                ], 422);
            }

            $allowed = $user->role_id === 1
                || $activity->user_notaris_id === $user->id
                || $activity->clients->contains('id', $user->id);

            if (!$allowed) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }

            $data = $request->validate([
                'source_pdf' => ['nullable', 'string'],
                'placements' => ['required', 'array', 'min:1'],
                'placements.*.page' => ['required', 'integer', 'min:1'],
                'placements.*.kind' => ['required', 'in:image,draw'],
                'placements.*.x_ratio' => ['required', 'numeric', 'between:0,1'],
                'placements.*.y_ratio' => ['required', 'numeric', 'between:0,1'],
                'placements.*.w_ratio' => ['required', 'numeric', 'between:0,1'],
                'placements.*.h_ratio' => ['required', 'numeric', 'between:0,1'],
                'placements.*.source_user_id' => ['nullable', 'integer'],
                'placements.*.image_data_url' => ['nullable', 'string'],
                'placements.*.is_initial' => ['nullable', 'boolean'], // NEW
            ]);

            $sourceUrl = $data['source_pdf'] ?? $activity->draft->file;
            if (!$sourceUrl) {
                return response()->json(['success' => false, 'message' => 'PDF sumber tidak tersedia'], 422);
            }

            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

            $tmpPdf = $tmpDir . '/sign_src_' . $activity->draft->id . '_' . time() . '.pdf';
            $bin = Http::timeout(60)->get($sourceUrl)->body();
            file_put_contents($tmpPdf, $bin);

            $pdf = new \setasign\Fpdi\Fpdi('P', 'mm');
            $pageCount = $pdf->setSourceFile($tmpPdf);

            $byPage = [];
            foreach ($data['placements'] as $p) {
                $byPage[$p['page']][] = $p;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);

                if (!empty($byPage[$pageNo])) {
                    foreach ($byPage[$pageNo] as $pl) {
                        $imgTmp = null;
                        if ($pl['kind'] === 'image') {
                            $signUser = !empty($pl['source_user_id'])
                                ? User::with('identity')->find($pl['source_user_id'])
                                : $user->loadMissing('identity');

                            // NEW: pilih sumber sesuai is_initial
                            $imgUrl = !empty($pl['is_initial'])
                                ? ($signUser?->identity?->file_initial)
                                : ($signUser?->identity?->file_sign);

                            if (!$imgUrl) {
                                Log::warning("Signature/Initial image missing for user_id=" . ($pl['source_user_id'] ?? $user->id));
                                continue;
                            }
                            $imgTmp = $this->downloadToTmp($imgUrl, 'sig_img_');
                        } else {
                            $dataUrl = $pl['image_data_url'] ?? null;
                            if (!$dataUrl || !str_starts_with($dataUrl, 'data:image/')) {
                                continue;
                            }
                            $imgTmp = $this->saveDataUrl($dataUrl, 'sig_draw_');
                        }

                        if (!$imgTmp) continue;

                        $x = (float)$pl['x_ratio'] * $size['width'];
                        $y = (float)$pl['y_ratio'] * $size['height'];
                        $w = (float)$pl['w_ratio'] * $size['width'];
                        $h = (float)$pl['h_ratio'] * $size['height'];

                        try {
                            $pdf->Image($imgTmp, $x, $y, $w, $h);
                        } catch (\Throwable $e) {
                            Log::warning("Stamp image failed page={$pageNo}: " . $e->getMessage());
                        } finally {
                            if (file_exists($imgTmp)) @unlink($imgTmp);
                        }
                    }
                }
            }

            $outFile = $tmpDir . '/signed_' . $activity->draft->id . '_' . time() . '.pdf';
            $pdf->Output($outFile, 'F');

            try {
                if (!empty($activity->draft->file_ttd_path)) {
                    try {
                        Cloudinary::destroy($activity->draft->file_ttd_path);
                    } catch (\Exception $e) {
                        Log::warning('destroy old signed failed: ' . $e->getMessage());
                    }
                }

                $folder   = "enotaris/activities/{$activity->id}/signed";
                $filename = 'signed_pdf_' . now()->format('YmdHis');
                $publicId = "{$folder}/{$filename}";

                $upload = \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::upload($outFile, [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                    'timeout'       => 60,
                ]);

                $activity->draft->update([
                    'file_ttd'      => $upload->getSecurePath(),
                    'file_ttd_path' => $publicId,
                ]);
            } finally {
                if (file_exists($outFile)) @unlink($outFile);
                if (file_exists($tmpPdf)) @unlink($tmpPdf);
            }

            // (opsional) simpan placements ke DB (tambahkan kolom is_initial jika ada)
            foreach ($data['placements'] as $p) {
                Signature::create([
                    'draft_deed_id'  => $activity->draft->id,
                    'activity_id'    => $activity->id,
                    'user_id'        => $p['source_user_id'] ?? $user->id,
                    'page'           => $p['page'],
                    'kind'           => $p['kind'],
                    'x_ratio'        => $p['x_ratio'],
                    'y_ratio'        => $p['y_ratio'],
                    'w_ratio'        => $p['w_ratio'],
                    'h_ratio'        => $p['h_ratio'],
                    'is_initial'     => (bool)($p['is_initial'] ?? false),
                    'image_data_url' => $p['kind'] === 'draw' ? ($p['image_data_url'] ?? null) : null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'TTD/Paraf berhasil diterapkan',
                'data' => [
                    'file'       => $activity->draft->file_ttd,
                    'file_path'  => $activity->draft->file_ttd_path,
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Sign apply error', ['msg' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json(['success' => false, 'message' => 'Gagal menerapkan TTD: ' . $e->getMessage()], 500);
        }
    }

    public function resetTtd(Request $request, $activityId)
    {
        try {
            $user = $request->user();

            // Ambil activity + draft + track
            $activity = Activity::with(['draft', 'track'])->find($activityId);
            if (!$activity || !$activity->draft) {
                return response()->json(['success' => false, 'message' => 'Activity/draft tidak ditemukan'], 404);
            }

            // 🔒 hanya admin/notaris
            $allowed = $user->role_id === 1 || $activity->user_notaris_id === $user->id;
            if (!$allowed) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }

            // 🔒 jika sudah selesai → tidak boleh reset
            if (($activity->track?->status_sign ?? null) === 'done') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen sudah ditandai selesai. Tidak dapat direset.'
                ], 422);
            }

            // Hapus file_ttd lama di Cloudinary (jika ada)
            if (!empty($activity->draft->file_ttd_path)) {
                try {
                    Cloudinary::destroy($activity->draft->file_ttd_path);
                } catch (\Exception $e) {
                    Log::warning('Cloudinary destroy (file_ttd) gagal: ' . $e->getMessage());
                }
            }

            // Null-kan field hasil TTD
            $activity->draft->file_ttd = null;
            $activity->draft->file_ttd_path = null;
            $activity->draft->save();

            return response()->json([
                'success' => true,
                'message' => 'File TTD telah direset ke file awal.',
                'data' => [
                    'file'       => $activity->draft->file,       // file sumber (tanpa TTD)
                    'file_ttd'   => $activity->draft->file_ttd,   // null
                    'file_path'  => $activity->draft->file_path,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Reset TTD error', ['msg' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset file TTD: ' . $e->getMessage()], 500);
        }
    }


    protected function downloadToTmp(string $url, string $prefix = 'img_'): ?string
    {
        try {
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
            $path = $tmpDir . '/' . $prefix . uniqid() . '.png';
            $bin = \Illuminate\Support\Facades\Http::timeout(30)->get($url)->body();
            file_put_contents($path, $bin);
            return $path;
        } catch (\Throwable $e) {
            Log::warning("downloadToTmp fail: " . $e->getMessage());
            return null;
        }
    }

    protected function saveDataUrl(string $dataUrl, string $prefix = 'img_'): ?string
    {
        try {
            if (!str_starts_with($dataUrl, 'data:image/')) return null;
            [$meta, $base] = explode(',', $dataUrl, 2);
            $ext = 'png';
            if (str_contains($meta, 'image/jpeg')) $ext = 'jpg';
            $bin = base64_decode($base);

            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
            $path = $tmpDir . '/' . $prefix . uniqid() . '.' . $ext;
            file_put_contents($path, $bin);
            return $path;
        } catch (\Throwable $e) {
            Log::warning("saveDataUrl fail: " . $e->getMessage());
            return null;
        }
    }



    public function markDone(Request $request, $activityId)
    {
        try {
            $user = $request->user();

            // Ambil activity + relasi track + draft
            $activity = Activity::with(['notaris', 'track', 'draft'])->find($activityId);
            if (!$activity) {
                return response()->json(['success' => false, 'message' => 'Activity tidak ditemukan'], 404);
            }

            // Otorisasi: admin atau notaris pemilik
            $allowed = $user->role_id === 1 || $activity->user_notaris_id === $user->id;
            if (!$allowed) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }

            // Cek apakah file_ttd sudah ada
            if (!$activity->draft || empty($activity->draft->file_ttd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak bisa menandai selesai. File TTD belum tersedia.'
                ], 422);
            }

            // Ambil/siapkan track record
            $track = $activity->track;

            if (!$track) {
                // buat baru
                $track = new Track();
                $track->status_sign = 'done';
                $track->status_print = 'done';
                $track->sign_completed_at = now();
                $track->save();

                $activity->track()->associate($track);
                $activity->save();
            } else {
                // update existing
                $track->fill([
                    'status_sign'       => 'done',
                    'status_print'      => 'done',
                    'sign_completed_at' => now(),
                ]);
                $track->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Step Sign ditandai selesai',
                'data' => [
                    'activity_id'       => $activity->id,
                    'sign_status'       => $track->status_sign,
                    'sign_completed_at' => $track->sign_completed_at,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Sign markDone error', [
                'message'     => $e->getMessage(),
                'line'        => $e->getLine(),
                'activity_id' => $activityId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai selesai: ' . $e->getMessage(),
            ], 500);
        }
    }
}
