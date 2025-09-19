<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\Signature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DraftDeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'custom_value_template',
        'reading_schedule',
        'status_approval',
        'file',
        'file_path'
    ];

    protected $casts = [
        'reading_schedule' => 'datetime',
        'status_approval'  => 'string',
    ];

    // Relasi ke Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    // Relasi ke ClientDraft
    public function clientDrafts()
    {
        return $this->hasMany(ClientDraft::class, 'draft_deed_id');
        // kalau di tabelmu masih pakai kolom 'draft_id', ganti jadi 'draft_id'
    }

    // ===== Scopes =====
    public function scopePending($query)
    {
        return $query->where('status_approval', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status_approval', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status_approval', 'rejected');
    }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'draft_deed_id');
    }
}
