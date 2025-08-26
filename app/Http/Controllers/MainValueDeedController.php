<?php

namespace App\Http\Controllers;

use App\Models\MainValueDeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MainValueDeedController extends Controller
{
    /**
     * GET /main-value-deeds
     * Query opsional:
     * - deed_id (int)
     * - search / q (cari di main_value)
     * - per_page (default 10)
     */
    public function index(Request $request)
    {
        $deedId  = $request->query('deed_id');
        $q       = $request->query('search', $request->query('q'));
        $perPage = (int)($request->query('per_page', 10));
        $perPage = $perPage > 0 ? $perPage : 10;

        $query = MainValueDeed::with('deed');

        if ($deedId) {
            $query->where('deed_id', $deedId);
        }

        if ($q) {
            $query->where('main_value', 'like', "%{$q}%");
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar main value akta berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ]
        ], 200);
    }

    /**
     * GET /main-value-deeds/{id}
     */
    public function show($id)
    {
        $mvd = MainValueDeed::with('deed')->find($id);

        if (!$mvd) {
            return response()->json([
                'success' => false,
                'message' => 'Main value akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail main value akta berhasil diambil',
            'data'    => $mvd
        ], 200);
    }

    /**
     * POST /main-value-deeds
     * Body: deed_id, main_value
     * Catatan: main_value harus unik dalam satu deed_id.
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name'       => ['required', 'string', 'max:255'],
            'deed_id'    => ['required', 'integer', 'exists:deeds,id'],
            'main_value' => [
                'required',
                'string',
                'max:255',
                // unik per deed
                Rule::unique('main_value_deeds', 'main_value')->where(function ($q) use ($request) {
                    return $q->where('deed_id', $request->input('deed_id'));
                }),
            ],
        ], [
            'name.required'       => 'Nama wajib diisi.',
            'name.string'         => 'Nama harus berupa teks.',
            'name.max'            => 'Nama maksimal 255 karakter.',
            'deed_id.required'    => 'ID akta wajib diisi.',
            'deed_id.integer'     => 'ID akta tidak valid.',
            'deed_id.exists'      => 'Akta yang dipilih tidak valid.',
            'main_value.required' => 'Main value wajib diisi.',
            'main_value.string'   => 'Main value harus berupa teks.',
            'main_value.max'      => 'Main value maksimal 255 karakter.',
            'main_value.unique'   => 'Main value sudah ada pada akta tersebut.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $mvd = MainValueDeed::create($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Main value akta berhasil dibuat',
            'data'    => $mvd,
        ], 201);
    }

    /**
     * PUT /main-value-deeds/{id}
     */
    public function update(Request $request, $id)
    {
        $mvd = MainValueDeed::find($id);
        if (!$mvd) {
            return response()->json([
                'success' => false,
                'message' => 'Main value akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'       => ['required', 'string', 'max:255'],
            'deed_id'    => ['required', 'integer', 'exists:deeds,id'],
            'main_value' => [
                'required',
                'string',
                'max:255',
                // unik per deed, ignore baris saat ini
                Rule::unique('main_value_deeds', 'main_value')
                    ->where(fn($q) => $q->where('deed_id', $request->input('deed_id')))
                    ->ignore($mvd->id),
            ],
        ], [
            'name.required'       => 'Nama wajib diisi.',
            'name.string'         => 'Nama harus berupa teks.',
            'name.max'            => 'Nama maksimal 255 karakter.',
            'deed_id.required'    => 'ID akta wajib diisi.',
            'deed_id.integer'     => 'ID akta tidak valid.',
            'deed_id.exists'      => 'Akta yang dipilih tidak valid.',
            'main_value.required' => 'Main value wajib diisi.',
            'main_value.string'   => 'Main value harus berupa teks.',
            'main_value.max'      => 'Main value maksimal 255 karakter.',
            'main_value.unique'   => 'Main value sudah ada pada akta tersebut.',
        ]);

        if ($validasi->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validasi->errors(),
            ], 422);
        }

        $data = $validasi->validated();

        foreach (['deed_id', 'main_value'] as $f) {
            if (array_key_exists($f, $data)) {
                $mvd->{$f} = $data[$f];
            }
        }

        $mvd->save();

        return response()->json([
            'success' => true,
            'message' => 'Main value akta berhasil diperbarui',
            'data'    => $mvd
        ], 200);
    }

    /**
     * DELETE /main-value-deeds/{id}
     * (Opsional) Cegah hapus jika masih dipakai modul lain.
     */
    public function destroy($id)
    {
        $mvd = MainValueDeed::find($id);
        if (!$mvd) {
            return response()->json([
                'success' => false,
                'message' => 'Main value akta tidak ditemukan',
                'data'    => null
            ], 404);
        }

        // Tambah blokir relasi jika suatu saat dibutuhkan.
        $mvd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Main value akta berhasil dihapus',
            'data'    => null
        ], 200);
    }
}
