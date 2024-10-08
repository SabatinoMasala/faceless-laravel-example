<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('series');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('language');
            $table->string('title')->nullable();
            $table->string('voice_over_path')->nullable();
            $table->string('video_path')->nullable();
            $table->float('duration_in_seconds')->nullable();
            $table->json('voice_over_transcription')->nullable();
            $table->json('voice_over_chunks')->nullable();
            $table->longText('creative_direction')->nullable();
            $table->string('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
