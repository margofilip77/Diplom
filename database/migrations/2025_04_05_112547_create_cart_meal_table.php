<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_meal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('meal_option_id')->constrained()->onDelete('cascade');
            $table->integer('guests_count')->default(1); // Кількість гостей, для яких обрано харчування
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cart_meal');
    }
    
};
