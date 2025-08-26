<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Deed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    /**
     * GET /requirements
     * Opsional: filter by deed_id, search, pagination
     */
    public function index(Request $request)
    {
        $q        = $request->query('search');
        $deedId   = $request->query('deed_id');
        $perPage  = (int)($request->query('per_page', 10));
        $perPage  = $perPage > 0 ? $perPage : 10;

        $query = Requirement::query()->with('deed');

        if ($deedId) {
            $query->where('deed_id', $deedId);
        }

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $requirements = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar persyaratan berhasil diambil',
            'data'    => $requirements->items(),
            'meta'    => [
                'current_page' => $requirements->currentPage(),
                'per_page'     => $requirements->perPage(),
                'total'        => $requirements->total(),
                'last_page'    => $requirements->lastPage(),
            ]
        ], 200);
    }

    /**
     * GET /requirements/{id}
     */
    public function show($id)
    {
        $requirement = Requirement::with('deed')->find($id);

        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail persyaratan berhasil diambil',
            'data'    => $requirement
        ], 200);
    }

    /**
     * POST /requirements
     * Body: deed_id, name, is_file
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'deed_id' => 'required|exists:deeds,id',
            'name'    => 'required|string|max:255',
            'is_file' => 'required|boolean',
        ], [
            'deed_id.required' => 'Akta wajib dipilih.',
            'deed_id.exists'   => 'Akta tidak valid.',
            'name.required'    => 'Nama persyaratan wajib diisi.',
            'name.string'      => 'Nama persyaratan harus berupa teks.',
            'name.max'         => 'Nama persyaratan maksimal 255 karakter.',
            'is_file.required' => 'Tipe persyaratan wajib diisi.',
            'is_file.boolean'  => 'Tipe persyaratan harus berupa boolean.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $requirement = Requirement::create($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dibuat',
            'data'    => $requirement,
        ], 201);
    }

    /**
     * PUT /requirements/{id}
     */
    public function update(Request $request, $id)
    {
        $requirement = Requirement::find($id);
        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'deed_id' => 'required|exists:deeds,id',
            'name'    => 'required|string|max:255',
            'is_file' => 'required|boolean',
        ], [
            'deed_id.required' => 'Akta wajib dipilih.',
            'deed_id.exists'   => 'Akta tidak valid.',
            'name.required'    => 'Nama persyaratan wajib diisi.',
            'name.string'      => 'Nama persyaratan harus berupa teks.',
            'name.max'         => 'Nama persyaratan maksimal 255 karakter.',
            'is_file.required' => 'Tipe persyaratan wajib diisi.',
            'is_file.boolean'  => 'Tipe persyaratan harus berupa boolean.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $requirement->update($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil diperbarui',
            'data'    => $requirement
        ], 200);
    }

    /**
     * DELETE /requirements/{id}
     */
    public function destroy($id)
    {
        $requirement = Requirement::find($id);
        if (!$requirement) {
            return response()->json([
                'success' => false,
                'message' => 'Persyaratan tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $requirement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Persyaratan berhasil dihapus',
            'data'    => null
        ], 200);
    }
}
