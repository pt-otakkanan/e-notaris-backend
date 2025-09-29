<?php

namespace App\Models;

use App\Models\User;
use App\Models\CategoryBlog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'image_path',
        'title',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        // nama model CategoryBlog + pivot 'blog_category'
        return $this->belongsToMany(CategoryBlog::class, 'blog_category')
            ->withTimestamps();
    }
}
