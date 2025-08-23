<?php

namespace App\Models;

use App\Models\Deed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MainValueDeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'deed_id',
        'name',
        'main_value',
    ];

    public function deed()
    {
        return $this->belongsTo(Deed::class);
    }
}
