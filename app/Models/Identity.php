<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Identity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ktp',
        'file_ktp',
        'file_ktp_path',
        'file_kk',
        'file_kk_path',
        'npwp',
        'file_npwp',
        'file_npwp_path',
        'ktp_notaris',
        'file_ktp_notaris',
        'file_ktp_notaris_path',
        'file_sign',
        'file_sign_path',
        'file_photo',
        'file_photo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
