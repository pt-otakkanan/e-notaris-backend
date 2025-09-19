<?php
// app/Models/Signature.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'activity_id',
        'draft_deed_id',
        'user_id',
        'image_url',
        'image_path',
        'page',
        'x',
        'y',
        'width',
        'height',
        'signed_at',
        'meta',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'meta'      => 'array',
        'x' => 'float',
        'y' => 'float',
        'width' => 'float',
        'height' => 'float',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function draft()
    {
        return $this->belongsTo(DraftDeed::class, 'draft_deed_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
