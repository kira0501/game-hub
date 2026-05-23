<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMedia extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'type', 'role', 'url', 'sort_order'];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
