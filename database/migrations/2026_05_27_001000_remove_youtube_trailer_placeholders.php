<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('games')
            ->where('trailer_url', 'like', 'https://www.youtube.com/embed?listType=search%')
            ->update(['trailer_url' => null]);

        DB::table('game_media')
            ->where('type', 'video')
            ->where('url', 'like', 'https://www.youtube.com/embed?listType=search%')
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
