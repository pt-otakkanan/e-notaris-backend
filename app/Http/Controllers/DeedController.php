<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DeedRequirementTemplate;
use Illuminate\Support\Facades\Validator;

class DeedController extends Controller
{
    // GET /deeds
    // GET /deeds
    public function index(Request $request)
    {
        $user    = $request->user();
        $q       = $request->query('search');
        $perPage = max(1, (int) $request->query('per_page', 10));

        // eager load requirements (default templates) — only select needed fields
        $query = Deed::with(['requirements' => function ($qreq) {
            $qreq->select(['id', 'deed_id', 'name', 'is_file', 'is_active']);
        }])->where('user_notaris_id', $user->id);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $deeds = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // map items if you want to control shape
        $items = collect($deeds->items())->map(function ($d) {
            // $d is a Deed model (with relation loaded)
            return [
                'id' => $d->id,
                'user_notaris_id' => $d->user_notaris_id,
                'name' => $d->name,
                'description' => $d->description,
                'created_at' => $d->created_at,
                'updated_at' => $d->updated_at,
                'requirements' => $d->requirements->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'name' => $r->name,
                        'is_file' => (bool) $r->is_file,
                        'is_active' => (bool) $r->is_active,
                    ];
                })->values(),
            ];
        })->all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar akta berhasil diambil.',
            'data'    => $items,
            'meta'    => [
                'current_page' => $deeds->currentPage(),
                'per_page'     => $deeds->perPage(),
                'total'        => $deeds->total(),
                'last_page'    => $deeds->lastPage(),
            ]
        ], 200);
    }


    // GET /deeds/{id}
    public function show($id)
    {
        // eager-load requirements (default templates) plus relations yang diperlukan
        $deed = Deed::with(['activities', 'mainValueDeeds', 'requirements'])->find($id);

        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail akta berhasil diambil.',
            'data'    => $deed
        ], 200);
    }

    // POST /deeds
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'requirements' => 'sometimes|array',
            'requirements.*.name' => 'required_with:requirements|string|max:255',
            'requirements.*.is_file' => 'sometimes|boolean',
            'requirements.*.is_active' => 'sometimes|boolean',
        ], [
            'name.required'        => 'Nama akta wajib diisi.',
            'name.max'             => 'Nama akta maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
        ]);

        $validasi->setAttributeNames([
            'name' => 'Nama Akta',
            'description' => 'Deskripsi',
            'requirements' => 'Persyaratan Default',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal.',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();
        $data['user_notaris_id'] = $request->user()->id;

        try {
            // return the created deed from the transaction to avoid null issues
            $deed = DB::transaction(function () use ($data) {
                // create deed
                $deed = Deed::create([
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'user_notaris_id' => $data['user_notaris_id'],
                ]);

                // insert deed requirement templates if provided
                if (!empty($data['requirements']) && is_array($data['requirements'])) {
                    $now = now();
                    $rows = [];
                    foreach ($data['requirements'] as $r) {
                        if (empty($r['name'])) continue;
                        $rows[] = [
                            'deed_id' => $deed->id,
                            'name' => $r['name'],
                            'is_file' => isset($r['is_file']) ? (bool)$r['is_file'] : false,
                            'is_active' => isset($r['is_active']) ? (bool)$r['is_active'] : true,
                            'default_value' => $r['default_value'] ?? null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if (!empty($rows)) {
                        DeedRequirementTemplate::insert($rows);
                    }
                }

                return $deed;
            });

            // eager load requirements for response
            $deed->load('requirements');

            return response()->json([
                'success' => true,
                'message' => 'Akta berhasil dibuat.',
                'data'    => $deed,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akta: ' . $e->getMessage(),
                'data'    => null,
            ], 500);
        }
    }


    // POST /deeds/update/{id}
    public function update(Request $request, $id)
    {
        $deed = Deed::find($id);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'requirements' => 'sometimes|array',
            'requirements.*.name' => 'required_with:requirements|string|max:255',
            'requirements.*.is_file' => 'sometimes|boolean',
            'requirements.*.is_active' => 'sometimes|boolean',
        ], [
            'name.required'        => 'Nama akta wajib diisi.',
            'name.max'             => 'Nama akta maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
        ]);

        $validasi->setAttributeNames([
            'name' => 'Nama Akta',
            'description' => 'Deskripsi',
            'requirements' => 'Persyaratan Default',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal.',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        try {
            DB::transaction(function () use ($deed, $data) {
                // update deed fields
                $deed->update([
                    'name' => $data['name'],
                    'description' => $data['description'],
                ]);

                // sync deed requirement templates if provided
                if (array_key_exists('requirements', $data)) {
                    // simple strategy: hapus semua template lama, lalu insert yang baru
                    DeedRequirementTemplate::where('deed_id', $deed->id)->delete();

                    $rows = [];
                    $now = now();
                    foreach ($data['requirements'] as $r) {
                        if (empty($r['name'])) continue;
                        $rows[] = [
                            'deed_id' => $deed->id,
                            'name' => $r['name'],
                            'is_file' => isset($r['is_file']) ? (bool)$r['is_file'] : false,
                            'is_active' => isset($r['is_active']) ? (bool)$r['is_active'] : true,
                            'default_value' => $r['default_value'] ?? null,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if (!empty($rows)) {
                        DeedRequirementTemplate::insert($rows);
                    }
                }
            });

            // reload relations for response
            $deed->load('requirements');

            return response()->json([
                'success' => true,
                'message' => 'Akta berhasil diperbarui.',
                'data'    => $deed
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui akta: ' . $e->getMessage(),
                'data'    => null
            ], 500);
        }
    }


    // DELETE /deeds/{id}
    public function destroy($id)
    {
        $deed = Deed::with(['activities', 'mainValueDeeds', 'requirements'])->find($id);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        try {
            DB::transaction(function () use ($deed) {
                // hapus default templates dulu (jika ada)
                if (method_exists($deed, 'requirements')) {
                    $deed->requirements()->delete();
                }

                // hapus relasi lain
                $deed->mainValueDeeds()->delete();
                $deed->activities()->delete();
                $deed->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Akta beserta relasinya berhasil dihapus.',
                'data'    => null
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus akta: ' . $e->getMessage(),
            ], 500);
        }
    }
}
