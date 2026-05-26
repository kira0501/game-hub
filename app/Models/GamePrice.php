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
        'previous_price',
        'discount_percent',
        'price_changed_at',
        'currency',
        'is_available',
        'external_url',
        'updated_at',
        'last_checked_at',
        'auto_update_error',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'previous_price' => 'decimal:2',
            'discount_percent' => 'integer',
            'price_changed_at' => 'datetime',
            'is_available' => 'boolean',
            'updated_at' => 'datetime',
            'last_checked_at' => 'datetime',
        ];
    }

    public function getPriceDroppedAttribute(): bool
    {
        return $this->previous_price !== null
            && $this->price !== null
            && (float) $this->price < (float) $this->previous_price;
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
