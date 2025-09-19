<?php

namespace App\Models;

use App\Models\DraftDeed;
use App\Models\Signature;
use App\Models\ClientDraft;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activity';

    protected $fillable = [
        'name',
        'deed_id',
        'track_id',
        'user_notaris_id',
        'activity_notaris_id',
        'tracking_code',
    ];

    // tampilkan status_approval (virtual) di JSON
    protected $appends = ['status_approval'];

    /** ================== Relasi ================== */
    public function deed()
    {
        return $this->belongsTo(Deed::class);
    }

    public function notaris()
    {
        return $this->belongsTo(User::class, 'user_notaris_id');
    }

    public function track()
    {
        return $this->belongsTo(Track::class, 'track_id');
    }

    public function requirements()
    {
        return $this->hasMany(Requirement::class, 'activity_id');
    }

    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'activity_notaris_id');
    }

    public function draft()
    {
        return $this->hasOne(DraftDeed::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Records pivot client_activity (punya kolom: user_id, activity_id, status_approval, order, timestamps)
     * Urutkan default berdasarkan pivot.order (asc), fallback id.
     */
    public function clientActivities()
    {
        return $this->hasMany(ClientActivity::class, 'activity_id')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Daftar user klien (penghadap) melalui pivot client_activity.
     * Sertakan kolom pivot 'status_approval' dan 'order', dan urut default oleh pivot.order.
     */
    // app/Models/Activity.php
    public function clients()
    {
        return $this->belongsToMany(User::class, 'client_activity', 'activity_id', 'user_id')
            ->withPivot(['status_approval', 'order'])   // <— tambahkan 'order'
            ->withTimestamps();
    }


    /** ===== Virtual global status dari pivot ===== */
    public function getStatusApprovalAttribute(): string
    {
        // Jika ada yang rejected → rejected
        $anyRejected = $this->clientActivities()
            ->where('status_approval', 'rejected')
            ->exists();

        if ($anyRejected) {
            return 'rejected';
        }

        // Total klien yang dibutuhkan (deed->total_client)
        $needed = (int) optional($this->deed)->total_client ?: 1;

        // Semua approved?
        $approvedCount = $this->clientActivities()
            ->where('status_approval', 'approved')
            ->count();

        return $approvedCount >= $needed ? 'approved' : 'pending';
    }

    /** ===== Scopes berdasar pivot (bukan kolom langsung) ===== */
    public function scopeApproved($q)
    {
        // approved jika TIDAK ada yang != approved
        return $q->whereDoesntHave('clientActivities', function ($h) {
            $h->where('status_approval', '!=', 'approved');
        });
    }

    public function scopeRejected($q)
    {
        return $q->whereHas('clientActivities', function ($h) {
            $h->where('status_approval', 'rejected');
        });
    }

    public function scopePending($q)
    {
        // ada minimal satu pending dan tidak ada rejected
        return $q->whereHas('clientActivities', function ($h) {
            $h->where('status_approval', 'pending');
        })
            ->whereDoesntHave('clientActivities', function ($h) {
                $h->where('status_approval', 'rejected');
            });
    }

    public function scopeWithClientStatus($query, string $status)
    {
        return $query->whereHas('clientActivities', function ($q) use ($status) {
            $q->where('status_approval', $status);
        });
    }

    public function clientDrafts()
    {
        // Activity (id) -> DraftDeed(activity_id) -> ClientDraft(draft_deed_id)
        return $this->hasManyThrough(
            ClientDraft::class,   // model tujuan
            DraftDeed::class,     // model perantara
            'activity_id',        // FK di DraftDeed yang mengarah ke Activity
            'draft_deed_id',      // FK di ClientDraft yang mengarah ke DraftDeed
            'id',                 // PK di Activity
            'id'                  // PK di DraftDeed
        );
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
}
