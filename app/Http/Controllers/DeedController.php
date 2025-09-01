<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeedController extends Controller
{
    /**
     * GET /deeds
     * Query opsional: search (by name/description), per_page
     */
    public function index(Request $request)
    {
        $q        = $request->query('search');
        $perPage  = (int)($request->query('per_page', 10));
        $perPage  = $perPage > 0 ? $perPage : 10;

        $query = Deed::with('requirements');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $deeds = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar akta berhasil diambil',
            'data'    => $deeds->items(),
            'meta'    => [
                'current_page' => $deeds->currentPage(),
                'per_page'     => $deeds->perPage(),
                'total'        => $deeds->total(),
                'last_page'    => $deeds->lastPage(),
            ]
        ], 200);
    }

    /**
     * GET /deeds/{id}
     */
    public function show($id)
    {
        $deed = Deed::with(['requirements', 'activities', 'mainValueDeeds'])->find($id);

        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail akta berhasil diambil',
            'data'    => $deed
        ], 200);
    }

    /**
     * POST /deeds
     * Body: name, description, total_client (int >=1)
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name'         => 'required|string|max:255|unique:deeds,name',
            'description'  => 'required|string|max:255',
            'total_client' => 'required|integer|min:1|max:10',
        ], [
            'name.required'         => 'Nama akta wajib diisi.',
            'name.string'           => 'Nama akta harus berupa teks.',
            'name.max'              => 'Nama akta maksimal 255 karakter.',
            'name.unique'           => 'Nama akta sudah digunakan.',
            'description.required'  => 'Deskripsi wajib diisi.',
            'description.string'    => 'Deskripsi harus berupa teks.',
            'description.max'       => 'Deskripsi maksimal 255 karakter.',
            'total_client.required' => 'Jumlah penghadap wajib diisi.',
            'total_client.integer'  => 'Jumlah penghadap harus berupa angka.',
            'total_client.min'      => 'Jumlah penghadap minimal 1.',
            'total_client.max'      => 'Jumlah penghadap maksimal 10.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $deed = Deed::create($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Akta berhasil dibuat',
            'data'    => $deed,
        ], 201);
    }

    /**
     * PUT /deeds/{id}
     * Body: name, description, total_client
     */
    public function update(Request $request, $id)
    {
        $deed = Deed::find($id);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'         => 'required|string|max:255|unique:deeds,name,' . $deed->id,
            'description'  => 'required|string|max:255',
            'total_client' => 'required|integer|min:1|max:10',
        ], [
            'name.required'         => 'Nama akta wajib diisi.',
            'name.string'           => 'Nama akta harus berupa teks.',
            'name.max'              => 'Nama akta maksimal 255 karakter.',
            'name.unique'           => 'Nama akta sudah digunakan.',
            'description.required'  => 'Deskripsi wajib diisi.',
            'description.string'    => 'Deskripsi harus berupa teks.',
            'description.max'       => 'Deskripsi maksimal 255 karakter.',
            'total_client.required' => 'Jumlah penghadap wajib diisi.',
            'total_client.integer'  => 'Jumlah penghadap harus berupa angka.',
            'total_client.min'      => 'Jumlah penghadap minimal 1.',
            'total_client.max'      => 'Jumlah penghadap maksimal 10.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        foreach (['name', 'description', 'total_client'] as $f) {
            if (array_key_exists($f, $data)) {
                $deed->{$f} = $data[$f];
            }
        }

        $deed->save();

        return response()->json([
            'success' => true,
            'message' => 'Akta berhasil diperbarui',
            'data'    => $deed
        ], 200);
    }

    /**
     * DELETE /deeds/{id}
     * (hapus juga relasi langsung)
     */
    public function destroy($id)
    {
        $deed = Deed::with(['requirements', 'activities', 'mainValueDeeds'])->find($id);

        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        try {
            DB::transaction(function () use ($deed) {
                $deed->requirements()->delete();
                $deed->mainValueDeeds()->delete();
                $deed->activities()->delete();
                $deed->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Akta beserta relasinya berhasil dihapus',
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
