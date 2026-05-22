<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', 'exists:games,id'],
            'store_id' => ['required', 'exists:stores,id'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'currency' => ['required', 'string', 'max:8'],
            'is_available' => ['nullable', 'boolean'],
            'external_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
