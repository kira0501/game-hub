<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('cpu_min');
            $table->string('cpu_rec');
            $table->string('gpu_min');
            $table->string('gpu_rec');
            $table->unsignedSmallInteger('ram_min');
            $table->unsignedSmallInteger('ram_rec');
            $table->unsignedSmallInteger('storage_min');
            $table->unsignedSmallInteger('storage_rec');
            $table->string('os_min');
            $table->string('os_rec');
            $table->string('directx_min')->nullable();
            $table->string('directx_rec')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_requirements');
    }
};
