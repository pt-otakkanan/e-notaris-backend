<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_client', // ganti is_double_client -> total_client
    ];

    protected $casts = [
        'total_client' => 'integer',
    ];

    /** Relasi */
    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'deed_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'deed_id');
    }

    public function mainValueDeeds()
    {
        return $this->hasMany(MainValueDeed::class, 'deed_id');
    }

    /** Helper opsional */
    public function requiredClientsCount(): int
    {
        return max(1, (int) $this->total_client);
    }
}
