<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
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
    ];

    protected function casts(): array
    {
        return [
            'ram_min' => 'integer',
            'ram_rec' => 'integer',
            'storage_min' => 'integer',
            'storage_rec' => 'integer',
        ];
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
