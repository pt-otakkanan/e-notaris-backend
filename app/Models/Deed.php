<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\Requirement;
use App\Models\MainValueDeed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_double_client'
    ];

    protected $casts = [
        'is_double_client' => 'boolean',
    ];

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function mainValueDeeds()
    {
        return $this->hasMany(MainValueDeed::class);
    }
}
