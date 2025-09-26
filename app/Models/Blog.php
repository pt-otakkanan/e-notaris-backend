<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    // karena tabelnya 'blog' (singular)
    protected $table = 'blog';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',      // URL
        'image_path', // public_id
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
