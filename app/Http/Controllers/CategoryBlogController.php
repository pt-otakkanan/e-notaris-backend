<?php

namespace App\Http\Controllers;

use App\Models\CategoryBlog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CategoryBlogController extends Controller
{
    // GET /category-blogs/all (?min=true)
    public function all(Request $request)
    {
        $min     = (bool) $request->query('min', false);
        $columns = $min ? ['id', 'name'] : ['*'];

        $items = CategoryBlog::orderBy('name', 'asc')->get($columns);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'data'    => $items,
            'meta'    => ['count' => $items->count()],
        ], 200);
    }

    // GET /category-blogs (?search= / ?q=, ?per_page=)
    public function index(Request $request)
    {
        $q       = $request->query('search', $request->query('q'));
        $perPage = max((int) $request->query('per_page', 10), 1);

        $query = CategoryBlog::query();

        if ($q) {
            $query->where('name', 'like', "%{$q}%");
        }

        $items = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'data'    => $items->items(),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ], 200);
    }

    // GET /category-blogs/{id}
    public function show($id)
    {
        $item = CategoryBlog::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kategori berhasil diambil',
            'data'    => $item,
        ], 200);
    }

    // POST /category-blogs
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:120', 'unique:category_blogs,name'],
            ],
            [
                'name.required' => 'Nama kategori wajib diisi.',
                'name.unique'   => 'Nama kategori sudah digunakan.',
                'name.max'      => 'Nama kategori maksimal 120 karakter.',
            ],
            ['name' => 'nama kategori']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $item = CategoryBlog::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dibuat',
            'data'    => $item,
        ], 201);
    }

    // POST /category-blogs/update/{id}
    public function update(Request $request, $id)
    {
        $item = CategoryBlog::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:120',
                    Rule::unique('category_blogs', 'name')->ignore($item->id),
                ],
            ],
            [
                'name.required' => 'Nama kategori wajib diisi.',
                'name.unique'   => 'Nama kategori sudah digunakan.',
                'name.max'      => 'Nama kategori maksimal 120 karakter.',
            ],
            ['name' => 'nama kategori']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Proses validasi gagal',
                'data'    => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (array_key_exists('name', $data)) {
            $item->name = $data['name'];
        }

        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data'    => $item,
        ], 200);
    }

    // DELETE /category-blogs/{id}
    public function destroy($id)
    {
        $item = CategoryBlog::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan',
                'data'    => null,
            ], 404);
        }

        // Jika FK pivot blog_category diset cascadeOnDelete, baris pivot akan terhapus otomatis.
        // Jika tidak, kamu bisa detach manual:
        // $item->blogs()->detach();

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus',
            'data'    => null,
        ], 200);
    }
}
