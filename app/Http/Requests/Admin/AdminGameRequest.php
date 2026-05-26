<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $game = $this->route('game');

        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', Rule::unique('games', 'slug')->ignore($game)],
            'description' => ['required', 'string', 'min:30'],
            'cover' => ['nullable', 'string', 'max:500'],
            'cover_file' => ['nullable', 'image', 'max:4096'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'hero_image_file' => ['nullable', 'image', 'max:4096'],
            'carousel_image' => ['nullable', 'string', 'max:500'],
            'carousel_image_file' => ['nullable', 'image', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:6144'],
            'gallery_urls' => ['nullable', 'string'],
            'video_files' => ['nullable', 'array'],
            'video_files.*' => ['file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'remove_media' => ['nullable', 'array'],
            'remove_media.*' => ['integer', 'exists:game_media,id'],
            'trailer_url' => ['nullable', 'string', 'max:1200'],
            'developer' => ['nullable', 'string', 'max:150'],
            'publisher' => ['nullable', 'string', 'max:150'],
            'release_date' => ['nullable', 'date'],
            'metacritic_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'user_score_avg' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'is_active' => ['nullable', 'boolean'],
            'play_features' => ['array'],
            'play_features.*' => ['string', Rule::in([
                'single_player',
                'pvp_online',
                'pvp_splitscreen',
                'coop_online',
                'coop_splitscreen',
                'shared_splitscreen',
                'achievements',
                'in_app_purchases',
                'cloud',
                'remote_play_together',
                'family_sharing',
                'accessibility',
            ])],
            'controller_support' => ['required', Rule::in(['none', 'partial', 'full'])],
            'supports_xbox_controller' => ['nullable', 'boolean'],
            'supports_playstation_controller' => ['nullable', 'boolean'],
            'developer_recommends_controller' => ['nullable', 'boolean'],
            'genres' => ['array'],
            'genres.*' => ['exists:genres,id'],
            'cpu_min' => ['required', 'string', 'max:150'],
            'cpu_rec' => ['required', 'string', 'max:150'],
            'gpu_min' => ['required', 'string', 'max:150'],
            'gpu_rec' => ['required', 'string', 'max:150'],
            'ram_min' => ['required', 'integer', 'min:2', 'max:256'],
            'ram_rec' => ['required', 'integer', 'min:2', 'max:256'],
            'storage_min' => ['required', 'integer', 'min:1', 'max:10000'],
            'storage_rec' => ['required', 'integer', 'min:1', 'max:10000'],
            'os_min' => ['required', 'string', 'max:120'],
            'os_rec' => ['required', 'string', 'max:120'],
            'directx_min' => ['nullable', 'string', 'max:50'],
            'directx_rec' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->slug && $this->title) {
            $this->merge(['slug' => Str::slug($this->title)]);
        }
    }
}
