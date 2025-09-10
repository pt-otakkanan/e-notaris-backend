<?php

namespace App\Models;

use App\Models\Identity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientActivity extends Model
{
    use HasFactory;

    protected $table = 'client_activity';

    protected $fillable = [
        'user_id',
        'activity_id',
        'status_approval',
        'order', // ⬅️ tambahkan ini
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function identity()
    {
        return $this->belongsTo(Identity::class);
    }

    // Relasi ke Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
