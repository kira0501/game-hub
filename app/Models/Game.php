<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
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
        'play_features',
        'controller_support',
        'supports_xbox_controller',
        'supports_playstation_controller',
        'developer_recommends_controller',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'metacritic_score' => 'integer',
            'user_score_avg' => 'decimal:1',
            'play_features' => 'array',
            'supports_xbox_controller' => 'boolean',
            'supports_playstation_controller' => 'boolean',
            'developer_recommends_controller' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->play_features ?? [], true);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function media()
    {
        return $this->hasMany(GameMedia::class)->orderBy('sort_order');
    }

    public function galleryMedia()
    {
        return $this->hasMany(GameMedia::class)->where('role', 'gallery')->orderBy('sort_order');
    }

    public function prices()
    {
        return $this->hasMany(GamePrice::class);
    }

    public function systemRequirement()
    {
        return $this->hasOne(SystemRequirement::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
