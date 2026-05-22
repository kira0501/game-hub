<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => Str::title($title),
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'cover' => 'https://images.unsplash.com/photo-1556438064-2d7646166914?auto=format&fit=crop&w=900&q=80',
            'trailer_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
            'developer' => fake()->company(),
            'publisher' => fake()->company(),
            'release_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'metacritic_score' => fake()->numberBetween(65, 96),
            'user_score_avg' => fake()->randomFloat(1, 6.5, 9.6),
            'is_active' => true,
        ];
    }
}
