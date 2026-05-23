<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePrice extends Model
{
    use HasFactory;

    const CREATED_AT = null;

    protected $fillable = [
        'game_id',
        'store_id',
        'price',
        'discount_percent',
        'currency',
        'is_available',
        'external_url',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'is_available' => 'boolean',
            'updated_at' => 'datetime',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
