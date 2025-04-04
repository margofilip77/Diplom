<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('accommodation_meal_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained('accommodations')->onDelete('cascade');
            $table->foreignId('meal_option_id')->constrained('meal_options')->onDelete('cascade');
            $table->integer('price'); // Ціна задається власником
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('accommodation_meal_option');
    }
};
