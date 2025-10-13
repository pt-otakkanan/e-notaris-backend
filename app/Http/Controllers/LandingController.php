<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Models\Setting;
use App\Models\Activity;
use App\Models\Template;
use App\Models\DraftDeed;
use Illuminate\Support\Str;
use App\Models\CategoryBlog;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * GET /landing/statistics
     * Hitung langsung dari DB (tanpa cache).
     */
    public function statistics(Request $request)
    {
        $stats = [
            'projects' => (int) Activity::query()->count(),
            'notaries' => (int) User::query()->where('role_id', 3)->count(),
            'clients'  => (int) User::query()->where('role_id', 2)->count(),
            'deeds'    => (int) DraftDeed::query()->count(), // <- pakai DraftDeed
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik berhasil diambil',
            'data'    => $stats,
        ], 200);
    }

    public function templates(Request $request)
    {
        $q     = $request->query('q');
        $limit = max(1, (int) $request->query('limit', 12));

        $items = Template::query()
            ->select(['id', 'name', 'description', 'logo'])
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->latest('id')
            ->limit($limit)
            ->get();

        // Normalisasi field untuk FE (title/desc/icon_url)
        $data = $items->map(function ($t) {
            return [
                'id'        => $t->id,
                'title'     => $t->name,
                'desc'      => $t->description ?: 'Template akta.',
                'icon_url'  => $t->logo,   // bisa null, FE handle fallback ikon
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar template berhasil diambil',
            'data'    => $data,
            'meta'    => ['count' => $data->count()],
        ], 200);
    }

    // GET /landing/blogs?q=&categories=Kat1,Kat2&per_page=&page=
    public function blogs(Request $request)
    {
        $q        = trim($request->query('q', ''));
        $catsIn   = $request->query('categories'); // array atau string "A,B"
        $perPage  = max(1, (int) $request->query('per_page', 9));

        // Normalisasi kategori (array of names)
        if (is_string($catsIn)) {
            $categories = array_values(array_filter(array_map('trim', explode(',', $catsIn))));
        } elseif (is_array($catsIn)) {
            $categories = array_values(array_filter(array_map('trim', $catsIn)));
        } else {
            $categories = [];
        }

        $query = Blog::query()
            ->with([
                'categories:id,name',
                'user:id,name',
            ])
            ->orderBy('created_at', 'desc');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (!empty($categories)) {
            // OR semantics: punya salah satu kategori yang dipilih
            $query->whereHas('categories', function ($h) use ($categories) {
                $h->whereIn('name', $categories);
            });
        }

        $items = $query->paginate($perPage);

        // Shape untuk FE
        $data = collect($items->items())->map(function (Blog $b) {
            return [
                'id'         => $b->id,
                'title'      => $b->title,
                'date'       => optional($b->created_at)->format('d M Y'),
                'author'     => optional($b->user)->name ?? 'enotaris',
                'excerpt'    => Str::limit(strip_tags((string) $b->description), 140),
                'categories' => $b->categories->pluck('name')->values()->all(),
                'cover'      => $b->image, // URL (mis. Cloudinary)
                // sesuaikan jika punya slug:
                'href'       => "/blog/{$b->id}",
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar blog berhasil diambil',
            'data'    => $data,
            'meta'    => [
                'current_page' => $items->currentPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
                'last_page'    => $items->lastPage(),
            ],
        ]);
    }

    // GET /landing/blog-categories
    public function blogCategories(Request $request)
    {
        $cats = CategoryBlog::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(function ($c) {
                return ['id' => $c->id, 'name' => $c->name];
            });

        return response()->json([
            'success' => true,
            'message' => 'Kategori blog berhasil diambil',
            'data'    => $cats,
            'meta'    => ['count' => $cats->count()],
        ]);
    }

    public function latestBlogs(Request $request)
    {
        $limit = max(1, (int) $request->query('limit', 6));

        $items = Blog::query()
            ->with(['categories:id,name', 'user:id,name'])
            ->latest('created_at')
            ->limit($limit)
            ->get();

        $data = $items->map(function (Blog $b) {
            return [
                'id'         => $b->id,
                'title'      => $b->title,
                'date'       => optional($b->created_at)->format('d M Y'),
                'author'     => optional($b->user)->name ?? 'enotaris',
                'excerpt'    => Str::limit(strip_tags((string) $b->description), 140),
                'categories' => $b->categories->pluck('name')->values()->all(),
                'cover'      => $b->image,        // URL gambar (Cloudinary/local)
                'href'       => "/blog/{$b->id}", // sesuaikan jika pakai slug
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Blog terbaru berhasil diambil',
            'data'    => $data,
            'meta'    => ['count' => $data->count()],
        ]);
    }
    public function settings(Request $request)
    {
        $s = Setting::query()->latest('id')->first();

        // Boleh pakai default ringan bila kosong
        $data = $s ? [
            'logo'        => $s->logo,
            'favicon'     => $s->favicon,
            'telepon'     => $s->telepon,
            'facebook'    => $s->facebook,
            'instagram'   => $s->instagram,
            'twitter'     => $s->twitter,
            'linkedin'    => $s->linkedin,
            'title_hero'  => $s->title_hero,
            'desc_hero'   => $s->desc_hero,
            'desc_footer' => $s->desc_footer,
        ] : [
            'logo'        => null,
            'favicon'     => null,
            'telepon'     => null,
            'facebook'    => null,
            'instagram'   => null,
            'twitter'     => null,
            'linkedin'    => null,
            'title_hero'  => 'Solusi Digital Notaris',
            'desc_hero'   => 'Kelola akta, jadwal, dan tanda tangan dengan mudah.',
            'desc_footer' => 'Platform e-Notaris untuk kerja lebih cepat.',
        ];

        return response()->json([
            'success' => true,
            'message' => 'Setting berhasil diambil',
            'data'    => $data,
        ], 200);
    }
}
