<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function avatar(string $filename)
    {
        $filename = basename($filename);
        $paths = [
            public_path('uploads/avatars/'.$filename),
            storage_path('app/public/avatars/'.$filename),
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return response()->file($path);
            }
        }

        abort(404);
    }

    public function edit(Request $request)
    {
        $user = $request->user()->loadCount([
            'favoriteGames',
            'reviews',
            'pcConfigs',
        ]);

        return view('profile.edit', [
            'user' => $user,
            'favoriteGames' => $user->favoriteGames()
                ->with(['genres', 'prices'])
                ->latest('favorites.created_at')
                ->limit(4)
                ->get(),
            'recentReviews' => $user->reviews()
                ->with('game')
                ->latest()
                ->limit(3)
                ->get(),
            'pcConfigs' => $user->pcConfigs()
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email,'.$request->user()->id],
            'avatar' => ['nullable', 'string', 'max:500'],
            'avatar_file' => ['nullable', 'image', 'max:5120'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $directory = public_path('uploads/avatars');
            $filename = (string) Str::uuid().'.'.$file->getClientOriginalExtension();

            File::ensureDirectoryExists($directory);
            $file->move($directory, $filename);

            $data['avatar'] = route('profile.avatar', $filename);
        }

        unset($data['avatar_file']);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $request->user()->update($data);

        return back()->with('status', 'Профиль обновлен.');
    }
}
