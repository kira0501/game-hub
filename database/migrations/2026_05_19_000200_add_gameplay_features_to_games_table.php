<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->json('play_features')->nullable()->after('user_score_avg');
            $table->string('controller_support')->default('partial')->after('play_features');
            $table->boolean('supports_xbox_controller')->default(false)->after('controller_support');
            $table->boolean('supports_playstation_controller')->default(false)->after('supports_xbox_controller');
            $table->boolean('developer_recommends_controller')->default(false)->after('supports_playstation_controller');
        });

        DB::table('games')->update([
            'play_features' => json_encode(['single_player', 'achievements', 'cloud']),
            'controller_support' => 'partial',
        ]);
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn([
                'play_features',
                'controller_support',
                'supports_xbox_controller',
                'supports_playstation_controller',
                'developer_recommends_controller',
            ]);
        });
    }
};
