<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientActivity extends Model
{
    use HasFactory;

    protected $table = 'client_activity';

    protected $fillable = [
        'user_id',
        'activity_id',
        'status_approval',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
