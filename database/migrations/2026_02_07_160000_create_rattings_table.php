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
        Schema::create('rattings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('wisata_id');
            $table->tinyInteger('ratting');
            $table->text('ulasan')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('wisata_id')
                ->references('id')
                ->on('wisata');

            $table->unique(['user_id', 'wisata_id'], 'user_wisata_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rattings');
    }
};
