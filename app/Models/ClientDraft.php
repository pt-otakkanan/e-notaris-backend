<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientDraft extends Model
{
    use HasFactory;

    protected $table = 'client_drafts';

    protected $fillable = [
        'user_id',
        'draft_deed_id',   // kalau kolommu masih 'draft_id', ganti key di relasi di bawah
        'status_approval',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke DraftDeed
    public function draftDeed()
    {
        return $this->belongsTo(DraftDeed::class, 'draft_deed_id'); // pakai 'draft_id' kalau belum di-rename
    }
        
}
