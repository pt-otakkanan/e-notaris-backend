<?php

namespace App\Http\Controllers;

use App\Models\Deed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeedController extends Controller
{
    // GET /deeds
    public function index(Request $request)
    {
        $user    = $request->user();
        $q       = $request->query('search');
        $perPage = max(1, (int) $request->query('per_page', 10));

        $query = $user->role_id === 1
            ? Deed::query()
            : Deed::where('user_notaris_id', $user->id);

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $deeds = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar akta berhasil diambil.',
            'data'    => $deeds->items(),
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
        $deed = Deed::with(['activities', 'mainValueDeeds'])->find($id);

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
            'name'        => 'required|string|max:255|unique:deeds,name',
            'description' => 'required|string|max:255',
        ], [
            'name.required'        => 'Nama akta wajib diisi.',
            'name.unique'          => 'Nama akta sudah digunakan.',
            'name.max'             => 'Nama akta maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
        ]);

        $validasi->setAttributeNames([
            'name' => 'Nama Akta',
            'description' => 'Deskripsi',
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

        $deed = Deed::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Akta berhasil dibuat.',
            'data'    => $deed,
        ], 201);
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
            'name'        => 'required|string|max:255|unique:deeds,name,' . $deed->id,
            'description' => 'required|string|max:255',
        ], [
            'name.required'        => 'Nama akta wajib diisi.',
            'name.unique'          => 'Nama akta sudah digunakan.',
            'name.max'             => 'Nama akta maksimal 255 karakter.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.max'      => 'Deskripsi maksimal 255 karakter.',
        ]);

        $validasi->setAttributeNames([
            'name' => 'Nama Akta',
            'description' => 'Deskripsi',
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

        $deed->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Akta berhasil diperbarui.',
            'data'    => $deed
        ], 200);
    }

    // DELETE /deeds/{id}
    public function destroy($id)
    {
        $deed = Deed::with(['activities', 'mainValueDeeds'])->find($id);
        if (!$deed) {
            return response()->json([
                'success' => false,
                'message' => 'Akta tidak ditemukan.',
                'data'    => null
            ], 404);
        }

        try {
            DB::transaction(function () use ($deed) {
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
