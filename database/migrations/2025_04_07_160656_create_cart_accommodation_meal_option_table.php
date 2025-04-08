<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('cart_accommodation_meal_option', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_accommodation_id')->constrained('cart_accommodation')->onDelete('cascade');
        $table->foreignId('meal_option_id')->constrained('meal_options');
        $table->integer('guests_count');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_accommodation_meal_option');
    }
};
