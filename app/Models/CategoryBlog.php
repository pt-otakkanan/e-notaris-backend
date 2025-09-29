<?php

namespace App\Models;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryBlog extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function blogs()
    {
        // sebutkan nama pivot khusus: 'blog_category'
        return $this->belongsToMany(Blog::class, 'blog_category')
            ->withTimestamps();
    }
}
