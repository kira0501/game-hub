<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'login',
        'phone',
        'email',
        'password',
        'xp',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'xp' => 'integer',
    ];

    public function completedLessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_progress')
            ->withPivot(['is_completed', 'earned_xp', 'completed_at'])
            ->wherePivot('is_completed', true)
            ->withTimestamps();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
