<?php

namespace App\Models;

use App\Models\User;
use App\Models\DeedRequirementTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Deed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_notaris_id',
    ];


    /** Relasi */
    // public function requirements()
    // {
    //     return $this->hasMany(Requirement::class, 'deed_id');
    // }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'deed_id');
    }

    public function mainValueDeeds()
    {
        return $this->hasMany(MainValueDeed::class, 'deed_id');
    }

    public function notaris()
    {
        return $this->belongsTo(User::class, 'user_notaris_id');
    }
    /** Helper opsional */
    public function requiredClientsCount(): int
    {
        return max(1, (int) $this->total_client);
    }

    public function requirements()
    {
        return $this->hasMany(DeedRequirementTemplate::class, 'deed_id');
    }
}
