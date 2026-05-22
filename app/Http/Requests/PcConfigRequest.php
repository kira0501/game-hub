<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PcConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:100'],
            'cpu' => ['required', 'string', 'max:150'],
            'gpu' => ['required', 'string', 'max:150'],
            'ram' => ['required', 'integer', 'min:2', 'max:256'],
            'storage' => ['required', 'integer', 'min:10', 'max:10000'],
            'os' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'game_id' => ['nullable', 'exists:games,id'],
        ];
    }
}
