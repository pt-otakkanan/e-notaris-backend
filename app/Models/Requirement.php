<?php

namespace App\Models;

use App\Models\Deed;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'name',
        'is_file'
    ];

    protected $casts = [
        'is_file' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'requirement_id');
    }
}
