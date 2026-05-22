<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rating' => fake()->numberBetween(6, 10),
            'text' => fake()->randomElement([
                'Отличная игра для вечера: хорошая атмосфера, приятный темп и много деталей.',
                'Геймплей затягивает, оптимизация нормальная, но местами хочется больше разнообразия.',
                'Сильная визуальная часть и понятный прогресс. Для демонстрации проекта смотрится убедительно.',
                'Понравились механики и стиль. Цена особенно хорошая на распродаже.',
            ]),
            'status' => 'approved',
            'likes' => fake()->numberBetween(0, 40),
            'dislikes' => fake()->numberBetween(0, 8),
        ];
    }
}
