<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Token;
use App\Models\Activity;
use App\Models\Identity;
use Laravel\Sanctum\HasApiTokens;
use App\Models\DocumentRequirement;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
        'verify_key',
        'expired_key',
        'email_verified_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verify_key',
        'expired_key',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function identity()
    {
        return $this->hasOne(Identity::class);
    }

    public function documentRequirements()
    {
        return $this->hasMany(DocumentRequirement::class);
    }


    public function notarisActivities()
    {
        return $this->hasMany(Activity::class, 'user_notaris_id');
    }

    public function firstClientActivities()
    {
        return $this->hasMany(Activity::class, 'first_client_id');
    }

    public function secondClientActivities()
    {
        return $this->hasMany(Activity::class, 'second_client_id');
    }
}
