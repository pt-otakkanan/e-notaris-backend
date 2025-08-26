<?php

namespace App\Models;

use App\Models\Deed;
use App\Models\User;
use App\Models\Schedule;
use App\Models\DraftDeed;
use App\Models\DocumentRequirement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activity';

    protected $fillable = [
        'name',
        'deed_id',
        'user_notaris_id',
        'activity_notaris_id',
        'first_client_id',
        'second_client_id',
        'status_approval',
        'first_client_approval',
        'second_client_approval',
        'tracking_code'
    ];

    protected $casts = [
        'status_approval' => 'string',
        'first_client_approval' => 'string',
        'second_client_approval' => 'string',
    ];

    public function deed()
    {
        return $this->belongsTo(Deed::class);
    }

    public function notaris()
    {
        return $this->belongsTo(User::class, 'user_notaris_id');
    }

    public function firstClient()
    {
        return $this->belongsTo(User::class, 'first_client_id');
    }

    public function secondClient()
    {
        return $this->belongsTo(User::class, 'second_client_id');
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

    // Scopes
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
}
