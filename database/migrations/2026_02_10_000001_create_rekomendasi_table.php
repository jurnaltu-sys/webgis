<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_wisata');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_wisata')->references('id')->on('wisata')->onDelete('cascade');
            $table->unique(['id_user', 'id_wisata']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
