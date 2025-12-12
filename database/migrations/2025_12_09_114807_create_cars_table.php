<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::create('cars', function (Blueprint $table) {
        $table->id();
        $table->foreignId('brand_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('image')->nullable(); // Path foto utama
        $table->decimal('price_per_day', 10, 2);
        $table->integer('capacity'); // Jumlah kursi
        $table->string('fuel_type')->default('Petrol');
        $table->string('transmission')->default('Automatic');
        $table->boolean('is_available')->default(true);
        $table->text('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
