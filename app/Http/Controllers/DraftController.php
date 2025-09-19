<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\DraftDeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// Sesuaikan namespace Cloudinary yg kamu pakai:
use Illuminate\Support\Facades\Storage;
// App\Http\Controllers\DraftController.php
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf; // pastikan facade aktif
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class DraftController extends Controller
{
    /**
     * GET /draft
     * Query: search (by activity name / tracking_code), per_page
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $q       = $request->query('search');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        // Join activity agar bisa filter kepemilikan notaris & pencarian
        $query = DraftDeed::with(['activity:id,name,tracking_code,user_notaris_id'])
            ->join('activities', 'draft_deeds.activity_id', '=', 'activities.id')
            ->select('draft_deeds.*');

        // Notaris hanya melihat draft di activity miliknya
        if ($user->role_id !== 1) { // 1 = admin
            $query->where('activities.user_notaris_id', $user->id);
        }

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('activities.name', 'like', "%{$q}%")
                    ->orWhere('activities.tracking_code', 'like', "%{$q}%");
            });
        }

        $drafts = $query->orderBy('draft_deeds.created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar draft berhasil diambil',
            'data'    => $drafts->items(),
            'meta'    => [
                'current_page' => $drafts->currentPage(),
                'per_page'     => $drafts->perPage(),
                'total'        => $drafts->total(),
                'last_page'    => $drafts->lastPage(),
            ],
        ], 200);
    }

    /**
     * GET /draft/{id}
     */
    public function show(Request $request, $id)
    {
        $user  = $request->user();
        $draft = DraftDeed::with([
            'activity' => function ($q) {
                $q->with(['deed', 'notaris', 'clients.identity', 'clientActivities']);
            }
        ])->find($id);

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak mengakses draft ini',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail draft berhasil diambil',
            'data'    => $draft,
        ], 200);
    }

    /**
     * POST /draft
     * Body:
     *  - activity_id (required, milik notaris ini)
     *  - custom_value_template (nullable, long html)
     *  - reading_schedule (nullable, date)
     *  - status_approval (optional: pending/approved/rejected)
     *  - file (optional upload: pdf/doc/docx/jpg/jpeg/png/webp)
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'activity_id'           => 'required|exists:activities,id',
            'custom_value_template' => 'nullable|string',
            'reading_schedule'      => 'nullable|date',
            'status_approval'       => 'sometimes|in:pending,approved,rejected',
            'file'                  => 'sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $user     = $request->user();
        $activity = Activity::find($request->input('activity_id'));
        if (!$activity) {
            return response()->json(['success' => false, 'message' => 'Aktivitas tidak ditemukan'], 404);
        }
        if ($user->role_id !== 1 && $activity->user_notaris_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Tidak berhak menambahkan draft untuk aktivitas ini'], 403);
        }

        $data = $validasi->validated();

        // Upload file (opsional)
        $fileUrl  = null;
        $filePath = null;

        if ($request->hasFile('file')) {
            $folder   = "enotaris/activities/{$activity->id}/drafts";
            $filename = 'draft_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto', // dukung pdf/img/doc
                ]
            );

            $fileUrl  = $upload->getSecurePath();
            $filePath = $publicId; // simpan public_id untuk destroy
        }

        $draft = DraftDeed::create([
            'activity_id'           => $activity->id,
            'custom_value_template' => $data['custom_value_template'] ?? null,
            'reading_schedule'      => $data['reading_schedule'] ?? null,
            'status_approval'       => $data['status_approval'] ?? 'pending',
            'file'                  => $fileUrl,
            'file_path'             => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil dibuat',
            'data'    => $draft->load('activity:id,name,tracking_code'),
        ], 201);
    }

    /**
     * POST /draft/update/{id}  (atau PUT /draft/{id})
     */
    public function update(Request $request, $id)
    {
        $draft = DraftDeed::with('activity')->find($id);
        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak mengubah draft ini',
            ], 403);
        }

        $validasi = Validator::make($request->all(), [
            'custom_value_template' => 'sometimes|nullable|string',
            'reading_schedule'      => 'sometimes|nullable|date',
            'status_approval'       => 'sometimes|in:pending,approved,rejected',
            'file'                  => 'sometimes|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:10240',
            'clear_file'            => 'sometimes|boolean',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        // Update kolom biasa
        foreach (['custom_value_template', 'reading_schedule', 'status_approval'] as $f) {
            if (array_key_exists($f, $data)) {
                $draft->{$f} = $data[$f];
            }
        }

        // Handle file
        if (($data['clear_file'] ?? false) === true) {
            if (!empty($draft->file_path)) {
                Cloudinary::destroy($draft->file_path);
            }
            $draft->file = null;
            $draft->file_path = null;
        }

        if ($request->hasFile('file')) {
            if (!empty($draft->file_path)) {
                Cloudinary::destroy($draft->file_path);
            }

            $folder   = "enotaris/activities/{$draft->activity_id}/drafts";
            $filename = 'draft_' . now()->format('YmdHis');
            $publicId = "{$folder}/{$filename}";

            $upload = Cloudinary::upload(
                $request->file('file')->getRealPath(),
                [
                    'folder'        => $folder . '/',
                    'public_id'     => $filename,
                    'overwrite'     => true,
                    'resource_type' => 'auto',
                ]
            );

            $draft->file      = $upload->getSecurePath();
            $draft->file_path = $publicId;
        }

        $draft->save();

        // reset status approval semua klien ke pending
        if ($draft->clientDrafts()->exists()) {
            $draft->clientDrafts()->update(['status_approval' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil diperbarui',
            'data'    => $draft->load('activity:id,name,tracking_code'),
        ], 200);
    }


    /**
     * DELETE /draft/{id}
     */
    public function destroy(Request $request, $id)
    {
        $draft = DraftDeed::with('activity')->find($id);

        if (!$draft) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan',
            ], 404);
        }

        $user = $request->user();
        if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak berhak menghapus draft ini',
            ], 403);
        }

        try {
            DB::transaction(function () use ($draft) {
                if (!empty($draft->file_path)) {
                    Cloudinary::destroy($draft->file_path);
                }
                $draft->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Draft berhasil dihapus',
                'data'    => null,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function renderPdf(Request $request, $id)
    {
        try {
            // 1) Ambil draft + otorisasi
            $draft = DraftDeed::with('activity')->find($id);
            if (!$draft) {
                return response()->json(['success' => false, 'message' => 'Draft tidak ditemukan'], 404);
            }

            $user = $request->user();
            if ($user->role_id !== 1 && $draft->activity?->user_notaris_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Tidak berhak'], 403);
            }

            // 2) Ambil HTML FINAL dari request (prioritas: html_rendered → html → DB)
            $htmlRendered = (string) $request->input(
                'html_rendered',
                (string) $request->input('html', '')
            );
            if (!trim($htmlRendered)) {
                $htmlRendered = (string) ($draft->custom_value_template ?? '');
            }
            if (!trim($htmlRendered)) {
                return response()->json(['success' => false, 'message' => 'HTML kosong'], 422);
            }

            // Opsional: tolak jika masih ada token yang belum terganti
            if (preg_match('/\{\{[^}]+\}\}/', $htmlRendered) || preg_match('/\{[a-z0-9_]+\}/i', $htmlRendered)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ada variabel yang tidak sesuai'
                ], 422);
            }

            // 3) PDF options (default + validasi)
            $pdfOptions = (array) $request->input('pdf_options', []);
            $defaults = [
                'page_size'            => 'A4',
                'orientation'          => 'portrait',
                'margins_mm'           => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
                'font_family'          => 'times',  // times|arial|helvetica|calibri|georgia|garamond|cambria
                'font_size_pt'         => 12,
                'show_page_numbers'    => false,
                'page_number_h_align'  => 'right',  // left|center|right
                'page_number_v_align'  => 'bottom', // top|bottom
            ];
            $o = array_merge($defaults, $pdfOptions);

            $allowedSizes  = ['A3', 'A4', 'A5', 'Letter', 'Legal', 'Folio'];
            $allowedOrient = ['portrait', 'landscape'];
            $allowedFonts  = ['times', 'arial', 'helvetica', 'calibri', 'georgia', 'garamond', 'cambria'];

            if (!in_array($o['page_size'], $allowedSizes, true))    $o['page_size'] = 'A4';
            if (!in_array($o['orientation'], $allowedOrient, true)) $o['orientation'] = 'portrait';
            if (!in_array($o['font_family'], $allowedFonts, true))  $o['font_family'] = 'times';

            foreach (['top', 'right', 'bottom', 'left'] as $side) {
                if (!isset($o['margins_mm'][$side]) || !is_numeric($o['margins_mm'][$side])) {
                    $o['margins_mm'][$side] = 20;
                }
                $o['margins_mm'][$side] = max(0, min(50, (int) $o['margins_mm'][$side]));
            }
            $fs = (int) $o['font_size_pt'];
            if ($fs < 8 || $fs > 24) $fs = 12;

            // 4) Map font & konversi margin
            $fontMap = [
                'times'     => '"Times New Roman", serif',
                'arial'     => 'Arial, sans-serif',
                'helvetica' => 'Helvetica, Arial, sans-serif',
                'calibri'   => 'Calibri, sans-serif',
                'georgia'   => 'Georgia, serif',
                'garamond'  => 'Garamond, serif',
                'cambria'   => 'Cambria, serif',
            ];
            $fontStack = $fontMap[$o['font_family']] ?? $fontMap['times'];

            // 1 mm ≈ 2.83465 pt
            $MM_TO_PT = 2.83465;
            $mt = round($o['margins_mm']['top']    * $MM_TO_PT) . 'pt';
            $mr = round($o['margins_mm']['right']  * $MM_TO_PT) . 'pt';
            $mb = round($o['margins_mm']['bottom'] * $MM_TO_PT) . 'pt';
            $ml = round($o['margins_mm']['left']   * $MM_TO_PT) . 'pt';

            // 5) CSS minimal (tanpa header/footer/custom CSS lain)
            $css = <<<CSS
@page { size: {$o['page_size']} {$o['orientation']}; margin: {$mt} {$mr} {$mb} {$ml}; }
body { font-family: {$fontStack}; font-size: {$fs}pt; line-height: 1.6; color:#000; margin:0; padding:0; }
h1,h2,h3,h4,h5,h6{ margin:0 0 10px; font-weight:bold; }
p{ margin:0 0 8px; text-align:justify; }
ul,ol{ margin:0 0 12px 22px; padding:0; }
li{ margin-bottom: 4px; }
table { width:100%; border-collapse:collapse; margin:0; }
td,th{ border:1px solid #000; padding:6px 8px; text-align:left; }
th{ font-weight:bold; background:#f5f5f5; }
.ql-align-center{text-align:center;} .ql-align-right{text-align:right;}
.ql-align-left{text-align:left;} .ql-align-justify{text-align:justify;}
.preserve-space{ white-space:pre-wrap; tab-size:4; }
strong,b{ font-weight:bold; } em,i{ font-style:italic; } u{ text-decoration:underline; }
CSS;

            $fullHtml = <<<HTML
<!doctype html>
<html>
<head><meta charset="utf-8"><style>{$css}</style></head>
<body class="preserve-space">{$htmlRendered}</body>
</html>
HTML;

            // 6) Dompdf instance & render
            $dompdf = Pdf::loadHTML($fullHtml)
                ->setPaper(strtolower($o['page_size']), $o['orientation'])
                ->setOptions([
                    'isRemoteEnabled'     => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled'        => false,
                ])
                ->getDomPDF();

            $dompdf->render();

            // 7) Nomor halaman: angka saja, di tepi kertas (di luar margin)
            $showNums = filter_var($o['show_page_numbers'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($showNums) {
                $canvas = $dompdf->get_canvas();
                $w = $canvas->get_width();
                $h = $canvas->get_height();

                $metrics = $dompdf->getFontMetrics();
                $font = $metrics->get_font('helvetica', 'normal'); // aman & tersedia
                $size = $fs; // samakan dengan body; bisa juga pakai 9–11

                $text = "{PAGE_NUM}";

                // Lebar teks (kompatibel dengan berbagai versi dompdf)
                $textWidth = method_exists($metrics, 'getTextWidth')
                    ? $metrics->getTextWidth($text, $font, $size)
                    : $metrics->get_text_width($text, $font, $size);

                // 8 mm dari TEPI kertas
                $padPt = 8 * $MM_TO_PT;

                $hAlign = strtolower($o['page_number_h_align'] ?? 'right');   // left|center|right
                $vAlign = strtolower($o['page_number_v_align'] ?? 'bottom');  // top|bottom

                // Hitung X
                if ($hAlign === 'left') {
                    $x = 0 + $padPt;
                } elseif ($hAlign === 'center') {
                    $x = $w / 2;
                } else { // right
                    $x = $w - $textWidth - $padPt;
                }

                // Hitung Y (ingat baseline text)
                if ($vAlign === 'top') {
                    $y = 0 + $padPt + $size; // sedikit turun dari tepi atas
                } else { // bottom
                    $y = $h - $padPt;       // di atas tepi bawah
                }

                $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);
            }

            // 8) Simpan sementara
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
            $tmpFile = $tmpDir . '/draft_' . $draft->id . '_' . time() . '.pdf';
            file_put_contents($tmpFile, $dompdf->output());

            // 9) Upload ke Cloudinary (dengan retry sederhana)
            try {
                if (!empty($draft->file_path)) {
                    try {
                        Cloudinary::destroy($draft->file_path);
                    } catch (\Exception $e) {
                        Log::warning('Cloudinary destroy failed: ' . $e->getMessage());
                    }
                }

                $folder   = "enotaris/activities/{$draft->activity_id}/drafts";
                $filename = 'draft_pdf_' . now()->format('YmdHis');
                $publicId = "{$folder}/{$filename}";

                $maxRetries = 3;
                $upload = null;
                for ($i = 0; $i < $maxRetries; $i++) {
                    try {
                        $upload = Cloudinary::upload($tmpFile, [
                            'folder'        => $folder . '/',
                            'public_id'     => $filename,
                            'overwrite'     => true,
                            'resource_type' => 'auto',
                            'timeout'       => 60,
                        ]);
                        break;
                    } catch (\Exception $e) {
                        Log::warning("Cloudinary upload attempt " . ($i + 1) . " failed: " . $e->getMessage());
                        if ($i === $maxRetries - 1) throw $e;
                        sleep(pow(2, $i));
                    }
                }

                if ($upload) {
                    $draft->file      = $upload->getSecurePath();
                    $draft->file_path = $publicId;
                    $draft->save();
                } else {
                    throw new \Exception('Upload gagal setelah semua percobaan');
                }
            } finally {
                if (file_exists($tmpFile)) @unlink($tmpFile);
            }

            // 10) Response
            return response()->json([
                'success' => true,
                'message' => 'PDF berhasil dibuat & diunggah',
                'data'    => [
                    'id'         => $draft->id,
                    'file'       => $draft->file,
                    'file_path'  => $draft->file_path,
                    'updated_at' => $draft->updated_at,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('PDF Generation Error', [
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'draft_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
