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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Зв'язок з користувачем
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade'); // Зв'язок з помешканням
            $table->string('accommodation_photo')->nullable(); // Фото помешкання
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->integer('pets')->default(0);
            $table->json('meal_options')->nullable(); // Вибрані варіанти харчування
            $table->decimal('total_price', 10, 2); // Загальна сума
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
