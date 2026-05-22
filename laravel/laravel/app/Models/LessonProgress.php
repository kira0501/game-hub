<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
        'earned_xp',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'earned_xp' => 'integer',
        'completed_at' => 'datetime',
    ];
}
