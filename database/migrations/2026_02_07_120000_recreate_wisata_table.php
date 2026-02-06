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
        Schema::dropIfExists('wisata');

        Schema::create('wisata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 150);
            $table->string('slug', 160);
            $table->unsignedInteger('kategori_id');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('deskripsi');
            $table->json('fasilitas');
            $table->string('jam_buka', 50)->nullable();
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->integer('jml_rating')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisata');

        Schema::create('wisata', function (Blueprint $table) {
            $table->unsignedInteger('id', true);
            $table->string('nama', 50);
        });
    }
};
