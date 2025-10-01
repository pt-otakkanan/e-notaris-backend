<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class TemplateController extends Controller
{
    /** =====================
     *  Util: Query visibilitas
     *  - Admin: semua
     *  - Notaris: milik sendiri + milik admin
     * ======================*/
    private function visibleTemplatesQuery($user)
    {
        if ($user->isAdmin()) {
            return Template::query(); // semua
        }

        // Notaris: own OR admin-owned
        return Template::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhereIn('user_id', function ($sub) {
                    $sub->select('id')->from('users')->where('role_id', 1); // admin
                });
        });
    }

    /** =====================
     *  Util: Cek boleh edit?
     *  - Admin: boleh semua
     *  - Notaris: hanya milik sendiri
     * ======================*/
    private function canModify($user, Template $tpl): bool
    {
        if ($user->isAdmin()) return true;
        if ($user->isNotaris() && $tpl->user_id === $user->id) return true;
        return false;
    }

    public function importDocx(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimetypes:application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'max:10240'], // 10MB
        ], [
            'file.required'  => 'File .docx wajib diunggah.',
            'file.mimetypes' => 'Format harus .docx.',
            'file.max'       => 'Ukuran maksimal 10MB.',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data'    => $valid->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $path = $file->store('tmp/docx', 'local');

            $full = Storage::disk('local')->path($path);
            $phpWord = IOFactory::load($full, 'Word2007');

            $writer = IOFactory::createWriter($phpWord, 'HTML');

            ob_start();
            $writer->save('php://output');
            $html = ob_get_clean();

            Storage::disk('local')->delete($path);

            return response()->json([
                'success' => true,
                'message' => 'Konversi berhasil',
                'data'    => ['html' => $html],
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonversi file: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }

    public function all(Request $request)
    {
        $user    = Auth::user();
        $min     = (bool) $request->query('min', false);
        $columns = $min ? ['id', 'name'] : ['*'];

        $templates = $this->visibleTemplatesQuery($user)
            ->with('user:id,name,role_id')
            ->orderBy('created_at', 'desc')
            ->get($columns);

        return response()->json([
            'success' => true,
            'message' => 'Daftar template berhasil diambil',
            'data'    => $templates,
            'meta'    => ['count' => $templates->count()],
        ], 200);
    }

    public function index(Request $request)
    {
        $user    = Auth::user();
        $q       = $request->query('search', $request->query('q'));
        $perPage = max((int) $request->query('per_page', 10), 1);

        $query = $this->visibleTemplatesQuery($user)->with('user:id,name,role_id');

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('custom_value', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar template berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ], 200);
    }

    public function show($id)
    {
        $user = Auth::user();

        $tpl = $this->visibleTemplatesQuery($user)->with('user:id,name,role_id')->find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan atau tidak punya akses',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail template berhasil diambil',
            'data'    => $tpl,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validasi = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:150'],
            'custom_value' => ['required', 'string'],
        ], [
            'name.required'         => 'Nama template wajib diisi.',
            'custom_value.required' => 'Isi template wajib diisi.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();
        $data['user_id'] = $user->id; // lock ke pemilik pembuat

        $tpl = Template::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dibuat',
            'data'    => $tpl,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tpl  = Template::find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if (!$this->canModify($user, $tpl)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengubah template ini',
                'data'    => null,
            ], 403);
        }

        $validasi = Validator::make($request->all(), [
            'name'         => ['sometimes', 'required', 'string', 'max:150'],
            'custom_value' => ['sometimes', 'required', 'string'],
            // user_id tidak boleh diubah lewat API
        ], [
            'name.required'         => 'Nama template wajib diisi.',
            'custom_value.required' => 'Isi template wajib diisi.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $tpl->update($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil diperbarui',
            'data'    => $tpl,
        ], 200);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $tpl  = Template::find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        if (!$this->canModify($user, $tpl)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak menghapus template ini',
                'data'    => null,
            ], 403);
        }

        $tpl->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus',
            'data'    => null,
        ], 200);
    }

    private function canViewTemplate($user, Template $tpl): bool
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) return true;
        if (method_exists($user, 'isNotaris') && $user->isNotaris()) {
            // pemilik admin atau milik sendiri
            $owner = $tpl->user()->select('id', 'role_id')->first();
            if (!$owner) return false;
            return (int)$owner->role_id === 1 || (int)$tpl->user_id === (int)$user->id;
        }
        // fallback: batasi ke owner saja
        return (int)$tpl->user_id === (int)$user->id;
    }

    /**
     * POST /templates/{id}/render-pdf
     * Body:
     * - html_rendered (opsional) -> HTML final siap cetak
     * - html (opsional)          -> alternatif, kalau tidak ada html_rendered
     * - pdf_options (opsional)   -> { page_size, orientation, margins_mm{top,right,bottom,left}, font_family, font_size_pt, show_page_numbers, page_number_h_align, page_number_v_align }
     * - filename (opsional)      -> nama file tanpa .pdf
     * - upload (bool, opsional)  -> kalau true, upload ke Cloudinary & balikan URL
     */ public function renderPdf(Request $request, $id)
    {
        try {
            // 1) Ambil template + pemilik
            $tpl = Template::with('user:id,role_id,name')->find($id);
            if (!$tpl) {
                return response()->json(['success' => false, 'message' => 'Template tidak ditemukan.'], 404);
            }

            // 1b) Otorisasi: Admin semua; Notaris milik sendiri atau milik Admin
            $user = $request->user();
            $roleId = (int) ($user->role_id ?? 0);
            $ownerRole = (int) ($tpl->user?->role_id ?? 0);

            $canView = false;
            if ($roleId === 1) {
                $canView = true; // Admin
            } elseif ($roleId === 3) {
                $canView = ($tpl->user_id === $user->id) || ($ownerRole === 1); // Notaris: own or Admin owner
            }
            if (!$canView) {
                return response()->json(['success' => false, 'message' => 'Anda tidak berhak.'], 403);
            }

            // 2) Ambil HTML FINAL (html_rendered -> html -> custom_value)
            $htmlRendered = (string) $request->input('html_rendered', (string) $request->input('html', ''));
            if (!trim($htmlRendered)) {
                $htmlRendered = (string) ($tpl->custom_value ?? '');
            }
            if (!trim($htmlRendered)) {
                return response()->json(['success' => false, 'message' => 'HTML kosong.'], 422);
            }

            // 2b) Validasi token belum terganti
            $findUnreplacedTokens = function (string $html) {
                $tokens = [];
                if (preg_match_all('/\{\{\s*([A-Za-z0-9_]+)\s*\}\}/', $html, $m1)) {
                    $tokens = array_merge($tokens, $m1[1]);
                }
                if (preg_match_all('/(?<!\{)\{([A-Za-z0-9_]+)\}(?!\})/', $html, $m2)) {
                    $tokens = array_merge($tokens, $m2[1]);
                }
                return array_values(array_unique($tokens));
            };
            $unreplaced = $findUnreplacedTokens($htmlRendered);
            if (!empty($unreplaced)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ada variabel yang belum terganti: ' . implode(', ', $unreplaced),
                    'errors'  => ['unknown_tokens' => $unreplaced],
                    'data'    => ['unknown_tokens' => $unreplaced, 'count' => count($unreplaced)],
                ], 422);
            }

            // 3) Opsi PDF (defaults + sanitize)
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
            $fs = (int) ($o['font_size_pt'] ?? 12);
            if ($fs < 8 || $fs > 24) $fs = 12;

            // 4) Map font & margin → pt
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

            $MM_TO_PT = 2.83465;
            $mt = round($o['margins_mm']['top']    * $MM_TO_PT) . 'pt';
            $mr = round($o['margins_mm']['right']  * $MM_TO_PT) . 'pt';
            $mb = round($o['margins_mm']['bottom'] * $MM_TO_PT) . 'pt';
            $ml = round($o['margins_mm']['left']   * $MM_TO_PT) . 'pt';

            // 5) CSS minimum untuk Dompdf
            $css = <<<CSS
@page { size: {$o['page_size']} {$o['orientation']}; margin: {$mt} {$mr} {$mb} {$ml}; }
body { font-family: {$fontStack}; font-size: {$fs}pt; line-height: 1.6; color:#000; margin:0; padding:0; }
h1,h2,h3,h4,h5,h6{ margin:0 0 10px; font-weight:bold; }
p{ margin:0 0 8px; text-align:justify; }
ul,ol{ margin:0 0 12px 22px; padding:0; }
li{ margin-bottom: 4px; }
.ql-align-center{text-align:center;} .ql-align-right{text-align:right;}
.ql-align-left{text-align:left;} .ql-align-justify{text-align:justify;}
strong,b{ font-weight:bold; } em,i{ font-style:italic; } u{ text-decoration:underline; }
CSS;

            $fullHtml = <<<HTML
<!doctype html>
<html>
<head><meta charset="utf-8"><style>{$css}</style></head>
<body class="preserve-space">{$htmlRendered}</body>
</html>
HTML;

            // 6) Render Dompdf
            $dompdf = Pdf::loadHTML($fullHtml)
                ->setPaper(strtolower($o['page_size']), $o['orientation'])
                ->setOptions([
                    'isRemoteEnabled'      => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled'         => false,
                ])
                ->getDomPDF();

            $dompdf->render();

            // 7) Nomor halaman opsional
            $showNums = filter_var($o['show_page_numbers'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($showNums) {
                $canvas  = $dompdf->get_canvas();
                $w       = $canvas->get_width();
                $h       = $canvas->get_height();
                $metrics = $dompdf->getFontMetrics();
                $font    = $metrics->get_font('helvetica', 'normal');
                $size    = $fs;
                $text    = "{PAGE_NUM}";
                $textWidth = method_exists($metrics, 'getTextWidth')
                    ? $metrics->getTextWidth($text, $font, $size)
                    : $metrics->get_text_width($text, $font, $size);

                $padPt = 8 * $MM_TO_PT;

                $hAlign = strtolower($o['page_number_h_align'] ?? 'right');
                $vAlign = strtolower($o['page_number_v_align'] ?? 'bottom');

                if ($hAlign === 'left')      $x = 0 + $padPt;
                elseif ($hAlign === 'center') $x = $w / 2;
                else                          $x = $w - $textWidth - $padPt;

                $y = ($vAlign === 'top') ? (0 + $padPt + $size) : ($h - $padPt);

                $canvas->page_text($x, $y, $text, $font, $size, [0, 0, 0]);
            }

            // 8) Simpan sementara, upload ke Cloudinary (retry), hapus lama
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
            $filename = trim((string)$request->input('filename', 'template_pdf_' . now()->format('YmdHis')));
            $tmpFile  = $tmpDir . '/' . $filename . '.pdf';
            file_put_contents($tmpFile, $dompdf->output());

            try {
                // Hapus file lama di Cloudinary jika ada
                if (!empty($tpl->file_path)) {
                    try {
                        Cloudinary::destroy($tpl->file_path);
                    } catch (\Exception $e) {
                        Log::warning('Cloudinary destroy failed (template): ' . $e->getMessage());
                    }
                }

                $folder   = "enotaris/templates/{$tpl->id}";
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
                        Log::warning("Cloudinary upload percobaan " . ($i + 1) . " gagal: " . $e->getMessage());
                        if ($i === $maxRetries - 1) throw $e;
                        sleep(pow(2, $i)); // backoff
                    }
                }

                if (!$upload) {
                    throw new \Exception('Upload gagal setelah semua percobaan.');
                }

                // 9) Simpan URL & public_id ke DB
                $tpl->file      = $upload->getSecurePath(); // URL https
                $tpl->file_path = $publicId;                // public_id untuk delete
                $tpl->save();

                return response()->json([
                    'success' => true,
                    'message' => 'PDF berhasil dibuat & diunggah.',
                    'data'    => [
                        'template_id' => $tpl->id,
                        'file'        => $tpl->file,
                        'file_path'   => $tpl->file_path,
                        'filename'    => $filename . '.pdf',
                        'updated_at'  => $tpl->updated_at,
                    ],
                ], 200);
            } finally {
                if (file_exists($tmpFile)) @unlink($tmpFile);
            }
        } catch (\Throwable $e) {
            Log::error('Template PDF Error', [
                'message'     => $e->getMessage(),
                'file'        => $e->getFile(),
                'line'        => $e->getLine(),
                'template_id' => $id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage(),
            ], 500);
        }
    }
}
