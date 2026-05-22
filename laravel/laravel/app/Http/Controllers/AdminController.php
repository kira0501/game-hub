<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')->latest()->get();
        $users = User::latest()->get();

        return view('admin', compact('courses', 'users'));
    }
}
