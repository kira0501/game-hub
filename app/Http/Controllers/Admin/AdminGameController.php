<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminGameRequest;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminGameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::query()
            ->with('genres')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.(string) $request->string('q').'%';

                $query->where(function ($query) use ($term) {
                    $query->where('title', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhere('developer', 'like', $term)
                        ->orWhere('publisher', 'like', $term)
                        ->orWhereHas('genres', fn ($genre) => $genre->where('name', 'like', $term));
                });
            })
            ->latest();

        return view('admin.games.index', [
            'games' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.games.form', [
            'game' => new Game(),
            'genres' => Genre::orderBy('name')->get(),
            'media' => collect(),
        ]);
    }

    public function store(AdminGameRequest $request)
    {
        $data = $request->validated();
        $game = Game::create($this->gameData($data));
        $game->genres()->sync($data['genres'] ?? []);
        $game->systemRequirement()->create($this->requirementsData($data));
        $this->syncMedia($request, $game);

        return redirect()->route('admin.games.index')->with('status', 'Игра создана.');
    }

    public function edit(Game $game)
    {
        return view('admin.games.form', [
            'game' => $game->load('systemRequirement', 'genres'),
            'genres' => Genre::orderBy('name')->get(),
            'media' => $game->media()->get(),
        ]);
    }

    public function update(AdminGameRequest $request, Game $game)
    {
        $data = $request->validated();
        $game->update($this->gameData($data));
        $game->genres()->sync($data['genres'] ?? []);
        $game->systemRequirement()->updateOrCreate(['game_id' => $game->id], $this->requirementsData($data));
        $this->syncMedia($request, $game);

        return redirect()->route('admin.games.index')->with('status', 'Игра обновлена.');
    }

    public function destroy(Game $game)
    {
        $game->delete();

        return back()->with('status', 'Игра удалена.');
    }

    private function gameData(array $data): array
    {
        $base = collect($data)->only([
            'title',
            'slug',
            'description',
            'cover',
            'hero_image',
            'carousel_image',
            'trailer_url',
            'developer',
            'publisher',
            'release_date',
            'metacritic_score',
            'user_score_avg',
            'controller_support',
        ]);

        if ($base->has('trailer_url')) {
            $base['trailer_url'] = $this->normalizeMediaUrl((string) $base['trailer_url']);
        }

        foreach ([
            'cover_file' => 'cover',
            'hero_image_file' => 'hero_image',
            'carousel_image_file' => 'carousel_image',
        ] as $input => $column) {
            if (request()->hasFile($input)) {
                $base[$column] = Storage::disk('public')->url(request()->file($input)->store('games', 'public'));
            }
        }

        return $base->merge([
            'play_features' => $data['play_features'] ?? [],
            'supports_xbox_controller' => (bool) ($data['supports_xbox_controller'] ?? false),
            'supports_playstation_controller' => (bool) ($data['supports_playstation_controller'] ?? false),
            'developer_recommends_controller' => (bool) ($data['developer_recommends_controller'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ])->all();
    }

    private function requirementsData(array $data): array
    {
        return collect($data)->only([
            'cpu_min',
            'cpu_rec',
            'gpu_min',
            'gpu_rec',
            'ram_min',
            'ram_rec',
            'storage_min',
            'storage_rec',
            'os_min',
            'os_rec',
            'directx_min',
            'directx_rec',
        ])->all();
    }

    private function syncMedia(AdminGameRequest $request, Game $game): void
    {
        if ($request->filled('remove_media')) {
            $game->media()->whereIn('id', $request->input('remove_media', []))->delete();
        }

        $order = (int) $game->media()->max('sort_order');

        foreach ($request->file('gallery_images', []) as $file) {
            $game->media()->create([
                'type' => 'image',
                'role' => 'gallery',
                'url' => Storage::disk('public')->url($file->store('games/gallery', 'public')),
                'sort_order' => ++$order,
            ]);
        }

        foreach ($request->file('video_files', []) as $file) {
            $game->media()->create([
                'type' => 'video',
                'role' => 'gallery',
                'url' => Storage::disk('public')->url($file->store('games/videos', 'public')),
                'sort_order' => ++$order,
            ]);
        }

        foreach (preg_split('/\R+/', (string) $request->input('gallery_urls')) as $url) {
            $url = $this->normalizeMediaUrl(trim($url));
            if ($url === '') {
                continue;
            }

            $game->media()->create([
                'type' => $this->isVideoUrl($url) ? 'video' : 'image',
                'role' => 'gallery',
                'url' => $url,
                'sort_order' => ++$order,
            ]);
        }

        $trailerUrl = $this->normalizeMediaUrl((string) $request->input('trailer_url'));

        if ($trailerUrl !== '' && ! $game->media()->where('url', $trailerUrl)->exists()) {
            $game->media()->create([
                'type' => 'video',
                'role' => 'gallery',
                'url' => $trailerUrl,
                'sort_order' => ++$order,
            ]);
        }
    }

    private function normalizeMediaUrl(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/<iframe\b[^>]*\bsrc=["\']([^"\']+)["\']/i', $value, $matches)) {
            $value = html_entity_decode($matches[1]);
        }

        if (preg_match('~rutube\.ru/video/([A-Za-z0-9_-]+)~i', $value, $matches)) {
            return 'https://rutube.ru/play/embed/'.$matches[1];
        }

        if (preg_match('~rutube\.ru/play/embed/([A-Za-z0-9_-]+)~i', $value, $matches)) {
            return 'https://rutube.ru/play/embed/'.$matches[1];
        }

        return $value;
    }

    private function isVideoUrl(string $url): bool
    {
        return preg_match('/\.(mp4|webm|ogg)(\?|$)/i', $url)
            || str_contains($url, 'rutube.ru/play/embed/')
            || str_contains($url, 'youtube.com')
            || str_contains($url, 'youtu.be');
    }
}
