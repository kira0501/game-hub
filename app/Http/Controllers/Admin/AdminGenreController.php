<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminGenreRequest;
use App\Models\Genre;

class AdminGenreController extends Controller
{
    public function index()
    {
        return view('admin.genres.index', [
            'genres' => Genre::withCount('games')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.genres.form', ['genre' => new Genre()]);
    }

    public function store(AdminGenreRequest $request)
    {
        Genre::create($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Жанр создан.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.form', compact('genre'));
    }

    public function update(AdminGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return redirect()->route('admin.genres.index')->with('status', 'Жанр обновлен.');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return back()->with('status', 'Жанр удален.');
    }
}
