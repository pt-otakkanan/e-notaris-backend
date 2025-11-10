<?php

namespace App\Models;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_notaris_id',
        'requirement_id',
        'requirement_name',
        'is_file_snapshot',
        'user_id',
        'value',
        'file',
        'file_path',
        'status_approval'
    ];

    protected $casts = [
        'status_approval' => 'string',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_notaris_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
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

    public function deedRequirementTemplate()
    {
        return $this->belongsTo(DeedRequirementTemplate::class);
    }
}
