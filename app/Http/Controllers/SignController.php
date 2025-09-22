<?php // app/Http/Controllers/SignController.php
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DraftDeed;
use App\Models\Signature as SignatureModel;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\Fpdi;

class SignController extends Controller
{
    public function apply(Request $request, $activityId)
    {
        try {
            $user = $request->user();

            // 1) Ambil activity + draft + otorisasi
            $activity = Activity::with(['notaris', 'clients.identity', 'draft'])
                ->find($activityId);

            if (!$activity || !$activity->draft) {
                return response()->json(['success' => false, 'message' => 'Activity/draft tidak ditemukan'], 404);
            }

            // Otorisasi sederhana: admin (role_id=1) atau notaris pemilik aktivitas atau klien yang terdaftar
            $allowed = $user->role_id === 1
                || $activity->user_notaris_id === $user->id
                || $activity->clients->contains('id', $user->id);

            if (!$allowed) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }

            // 2) Validasi payload
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
            ]);

            // 3) Ambil PDF sumber
            $sourceUrl = $data['source_pdf'] ?? $activity->draft->file;
            if (!$sourceUrl) {
                return response()->json(['success' => false, 'message' => 'PDF sumber tidak tersedia'], 422);
            }

            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

            $tmpPdf = $tmpDir . '/sign_src_' . $activity->draft->id . '_' . time() . '.pdf';
            $bin = Http::timeout(60)->get($sourceUrl)->body();
            file_put_contents($tmpPdf, $bin);

            // 4) Siapkan FPDI
            $pdf = new Fpdi('P', 'mm'); // orientasi akan ikut template
            $pageCount = $pdf->setSourceFile($tmpPdf);

            // 5) Proses tiap halaman
            // Kelompokkan placement by page
            $byPage = [];
            foreach ($data['placements'] as $p) {
                $byPage[$p['page']][] = $p;
            }

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId); // ['width','height','orientation']
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);

                if (!empty($byPage[$pageNo])) {
                    foreach ($byPage[$pageNo] as $pl) {
                        // 6) Ambil sumber gambar
                        $imgTmp = null;
                        if ($pl['kind'] === 'image') {
                            $signUser = null;
                            if (!empty($pl['source_user_id'])) {
                                $signUser = User::with('identity')->find($pl['source_user_id']);
                            } else {
                                // fallback: user yang sedang login
                                $signUser = $user->loadMissing('identity');
                            }
                            $imgUrl = $signUser?->identity?->file_sign;
                            if (!$imgUrl) {
                                // tetap lanjut tapi skip placement ini
                                Log::warning("Signature image missing for user_id={$pl['source_user_id']}");
                                continue;
                            }
                            $imgTmp = $this->downloadToTmp($imgUrl, 'sig_img_');
                        } else {
                            // kind = draw (data URL)
                            $dataUrl = $pl['image_data_url'] ?? null;
                            if (!$dataUrl || !str_starts_with($dataUrl, 'data:image/')) {
                                continue;
                            }
                            $imgTmp = $this->saveDataUrl($dataUrl, 'sig_draw_');
                        }

                        if (!$imgTmp) continue;

                        // 7) Konversi rasio → mm
                        $x = (float)$pl['x_ratio'] * $size['width'];
                        $y = (float)$pl['y_ratio'] * $size['height'];
                        $w = (float)$pl['w_ratio'] * $size['width'];
                        $h = (float)$pl['h_ratio'] * $size['height'];

                        // 8) Stamp gambar (FPDF::Image), koordinat dalam mm
                        // NB: Image bisa pakai w & h; kalau salah satu 0 maka proporsional.
                        // Kita set keduanya supaya sesuai area.
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

            // 9) Simpan hasil sementara
            $outFile = $tmpDir . '/signed_' . $activity->draft->id . '_' . time() . '.pdf';
            $pdf->Output($outFile, 'F');

            // 10) Upload Cloudinary (folder signed)
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

                $upload = Cloudinary::upload($outFile, [
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

            // (opsional) simpan placements ke DB
            foreach ($data['placements'] as $p) {
                SignatureModel::create([
                    'draft_deed_id'  => $activity->draft->id,
                    'activity_id'    => $activity->id,
                    'user_id'        => $p['source_user_id'] ?? $user->id,
                    'page'           => $p['page'],
                    'kind'           => $p['kind'],
                    'x_ratio'        => $p['x_ratio'],
                    'y_ratio'        => $p['y_ratio'],
                    'w_ratio'        => $p['w_ratio'],
                    'h_ratio'        => $p['h_ratio'],
                    'image_data_url' => $p['kind'] === 'draw' ? ($p['image_data_url'] ?? null) : null,
                    // kamu juga bisa simpan source_image_url utk audit
                ]);
            }

            // (opsional) update track status
            // jika semua pihak sudah sign → set status_sign = 'done'
            // $activity->track->update([...]);

            return response()->json([
                'success' => true,
                'message' => 'TTD berhasil diterapkan',
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

    private function downloadToTmp(string $url, string $prefix): ?string
    {
        try {
            $bin = Http::timeout(30)->get($url)->body();
            $path = storage_path('app/tmp/' . $prefix . uniqid() . '.png');
            file_put_contents($path, $bin);
            return $path;
        } catch (\Throwable $e) {
            Log::warning("downloadToTmp failed: " . $e->getMessage());
            return null;
        }
    }

    private function saveDataUrl(string $dataUrl, string $prefix): ?string
    {
        try {
            if (!preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) return null;
            $ext = strtolower($m[1] ?? 'png');
            $bin = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1));
            $path = storage_path('app/tmp/' . $prefix . uniqid() . '.' . $ext);
            file_put_contents($path, $bin);
            return $path;
        } catch (\Throwable $e) {
            Log::warning("saveDataUrl failed: " . $e->getMessage());
            return null;
        }
    }
}
