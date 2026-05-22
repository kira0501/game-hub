<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        abort_unless($lesson->is_published || auth()->user()->isAdmin(), 404);

        $lesson->load('course');

        return view('lesson', compact('lesson'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'task_question' => ['nullable', 'string', 'max:255'],
            'correct_answer' => ['nullable', 'string', 'max:255'],
            'xp_reward' => ['required', 'integer', 'min:0', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);

        $course->lessons()->create($data);

        return redirect()->route('admin')->with('status', 'Урок добавлен.');
    }

    public function complete(Request $request, Lesson $lesson)
    {
        $request->validate([
            'answer' => ['nullable', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $isCorrect = true;

        if ($lesson->correct_answer) {
            $isCorrect = mb_strtolower(trim($request->input('answer', ''))) === mb_strtolower(trim($lesson->correct_answer));
        }

        if (! $isCorrect) {
            return back()
                ->withInput()
                ->withErrors(['answer' => 'Ответ неверный. Правильный ответ: ' . $lesson->correct_answer]);
        }

        $progress = LessonProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => false,
                'earned_xp' => 0,
            ]
        );

        if (! $progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'earned_xp' => $lesson->xp_reward,
                'completed_at' => now(),
            ]);

            $user->increment('xp', $lesson->xp_reward);
        }

        return redirect()->route('profile')->with('status', 'Урок завершен. XP начислены.');
    }
}
