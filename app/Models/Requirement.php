<?php

namespace App\Models;

use App\Models\Deed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'deed_id',
        'name',
        'is_file'
    ];

    protected $casts = [
        'is_file' => 'boolean',
    ];

    public function deed()
    {
        return $this->belongsTo(Deed::class);
    }
}
