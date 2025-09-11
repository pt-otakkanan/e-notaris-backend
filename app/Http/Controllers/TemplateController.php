<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    public function all(Request $request)
    {
        $min     = (bool) $request->query('min', false);
        $columns = $min ? ['id', 'name'] : ['*']; // << ubah ke name

        $templates = Template::orderBy('created_at', 'desc')->get($columns);

        return response()->json([
            'success' => true,
            'message' => 'Daftar template berhasil diambil',
            'data'    => $templates,
            'meta'    => ['count' => $templates->count()],
        ], 200);
    }

    public function index(Request $request)
    {
        $q       = $request->query('search', $request->query('q'));
        $perPage = max((int) $request->query('per_page', 10), 1);

        $query = Template::query();

        if ($q) {
            $query->where('name', 'like', "%{$q}%")      // << cari ke name
                ->orWhere('custom_value', 'like', "%{$q}%");
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
        $tpl = Template::find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan',
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
        $validasi = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:150'], // << tambahkan name
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

        $tpl = Template::create($validasi->validated());

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dibuat',
            'data'    => $tpl,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $tpl = Template::find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $validasi = Validator::make($request->all(), [
            'name'         => ['sometimes', 'required', 'string', 'max:150'],
            'custom_value' => ['sometimes', 'required', 'string'],
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
        $tpl = Template::find($id);

        if (!$tpl) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $tpl->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus',
            'data'    => null,
        ], 200);
    }
}
