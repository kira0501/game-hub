<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $genre = $this->route('genre');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('genres', 'name')->ignore($genre)],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('genres', 'slug')->ignore($genre)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->slug && $this->name) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
