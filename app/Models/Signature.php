<?php // app/Models/Signature.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'draft_deed_id',
        'activity_id',
        'user_id',
        'page',
        'kind',
        'x_ratio',
        'y_ratio',
        'w_ratio',
        'h_ratio',
        'image_data_url',
        'source_image_url',
    ];

    public function draft()
    {
        return $this->belongsTo(DraftDeed::class, 'draft_deed_id');
    }
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
