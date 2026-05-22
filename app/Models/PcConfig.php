<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PcConfig extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'cpu', 'gpu', 'ram', 'storage', 'os', 'notes'];

    protected function casts(): array
    {
        return [
            'ram' => 'integer',
            'storage' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
