<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        $store = $this->route('store');

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('stores', 'name')->ignore($store)],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('stores', 'slug')->ignore($store)],
            'logo' => ['nullable', 'string', 'max:500'],
            'base_url' => ['required', 'url', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->slug && $this->name) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
