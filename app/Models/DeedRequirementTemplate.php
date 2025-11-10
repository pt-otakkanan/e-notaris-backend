<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeedRequirementTemplate extends Model
{
    use HasFactory;

    protected $table = 'deed_requirement_templates';

    protected $fillable = [
        'deed_id',
        'name',
        'is_file',
        'is_active',
        'default_value',
    ];

    protected $casts = [
        'is_file' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function deed()
    {
        return $this->belongsTo(Deed::class, 'deed_id');
    }
}
