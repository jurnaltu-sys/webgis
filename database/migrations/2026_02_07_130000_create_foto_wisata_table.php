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
        Schema::create('foto_wisata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wisata_id');
            $table->string('url', 255);
            $table->tinyInteger('is_cover')->default(0);

            $table->foreign('wisata_id')
                ->references('id')
                ->on('wisata')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_wisata');
    }
};
