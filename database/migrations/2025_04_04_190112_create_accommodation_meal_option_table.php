<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('accommodation_meal_option', function (Blueprint $table) {
            $table->id();
            // Зв'язок з таблицею accommodations
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            // Зв'язок з таблицею meal_options
            $table->foreignId('meal_option_id')->constrained('meal_options')->onDelete('cascade');
            // Ціна для конкретного варіанту харчування
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accommodation_meal_option');
    }
};
