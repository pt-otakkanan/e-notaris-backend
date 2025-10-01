<?php

// app/Models/Setting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'logo_path',
        'favicon',
        'favicon_path',
        'telepon',
        'facebook',
        'instagram',
        'twitter',
        'linkedin',
        'title_hero',
        'desc_hero',
        'desc_footer',
    ];
}
