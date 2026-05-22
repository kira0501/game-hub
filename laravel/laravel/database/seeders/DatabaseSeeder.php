<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@rulang.test'],
            [
                'fullname' => 'Администратор RuLang',
                'login' => 'admin',
                'phone' => '+70000000000',
                'password' => Hash::make('password'),
                'xp' => 0,
                'role' => 'admin',
            ]
        );

        $alphabet = Course::firstOrCreate(
            ['title' => 'Русский алфавит'],
            [
                'level' => 'A1',
                'description' => 'Буквы, звуки и первые слова для начинающих.',
                'is_published' => true,
            ]
        );

        $grammar = Course::firstOrCreate(
            ['title' => 'Базовая грамматика'],
            [
                'level' => 'A2',
                'description' => 'Род, число, падежи и простые предложения.',
                'is_published' => true,
            ]
        );

        $alphabet->lessons()->firstOrCreate(
            ['title' => 'Буква А'],
            [
                'content' => 'Буква А обозначает звук [а]. Примеры: Анна, адрес, аптека.',
                'task_question' => 'Введите первую букву слова Анна.',
                'correct_answer' => 'А',
                'xp_reward' => 10,
                'sort_order' => 1,
                'is_published' => true,
            ]
        );

        $grammar->lessons()->firstOrCreate(
            ['title' => 'Род существительных'],
            [
                'content' => 'В русском языке существительные имеют мужской, женский или средний род.',
                'task_question' => 'Какого рода слово книга?',
                'correct_answer' => 'женский',
                'xp_reward' => 15,
                'sort_order' => 1,
                'is_published' => true,
            ]
        );
    }
}
