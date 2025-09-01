<?php

namespace App\Models;

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

    /** Relasi */
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

    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class, 'activity_notaris_id');
    }

    public function draftDeeds()
    {
        return $this->hasMany(DraftDeed::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function clientActivities()
    {
        return $this->hasMany(ClientActivity::class, 'activity_id');
    }

    public function clients()
    {
        return $this->belongsToMany(User::class, 'client_activity', 'activity_id', 'user_id')
            ->withPivot('status_approval')
            ->withTimestamps();
    }

    /** ---------- Virtual global status dari pivot ---------- */
    public function getStatusApprovalAttribute(): string
    {
        // kalau belum diload, pakai query ringan
        $anyRejected = $this->clientActivities()
            ->where('status_approval', 'rejected')
            ->exists();

        if ($anyRejected) {
            return 'rejected';
        }

        // Ambil total klien yang seharusnya ada (deed->total_client)
        $needed = (int) optional($this->deed)->total_client ?: 1;

        // semua approved?
        $approvedCount = $this->clientActivities()
            ->where('status_approval', 'approved')
            ->count();

        return $approvedCount >= $needed ? 'approved' : 'pending';
    }

    /** ---------- Scopes berdasar pivot (bukan kolom) ---------- */
    public function scopeApproved($q)
    {
        // approved jika TIDAK ada yang != approved, dan jumlah approved >= needed
        return $q->whereDoesntHave('clientActivities', function ($h) {
            $h->where('status_approval', '!=', 'approved');
        });
        // catatan: kalau mau super akurat, bisa join/whereHas ke deed.total_client juga.
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
}
