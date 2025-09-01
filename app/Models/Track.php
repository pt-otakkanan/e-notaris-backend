<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    // Nama tabel (opsional, Laravel otomatis plural "tracks")
    protected $table = 'tracks';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'status_invite',
        'status_respond',
        'status_docs',
        'status_draft',
        'status_schedule',
        'status_sign',
        'status_print',
    ];

    // Kalau butuh relasi ke Activity
    public function activities()
    {
        return $this->hasMany(Activity::class, 'track_id');
    }
}
