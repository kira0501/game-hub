<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('currency', 8)->default('RUB');
            $table->boolean('is_available')->default(true)->index();
            $table->string('external_url')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['game_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_prices');
    }
};
