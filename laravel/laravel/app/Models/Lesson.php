<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'task_question',
        'correct_answer',
        'xp_reward',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'xp_reward' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function completedByUsers()
    {
        return $this->belongsToMany(User::class, 'lesson_progress')
            ->withPivot(['is_completed', 'earned_xp', 'completed_at'])
            ->withTimestamps();
    }
}
