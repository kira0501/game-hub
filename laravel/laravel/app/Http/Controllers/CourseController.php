<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)
            ->withCount('lessons')
            ->orderBy('level')
            ->orderBy('title')
            ->get();

        return view('courses', compact('courses'));
    }

    public function show(Course $course)
    {
        abort_unless($course->is_published || auth()->user()->isAdmin(), 404);

        $course->load(['lessons' => function ($query) {
            $query->where('is_published', true)->orderBy('sort_order');
        }]);

        return view('course', compact('course'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);

        Course::create($data);

        return redirect()->route('admin')->with('status', 'Курс добавлен.');
    }
}
