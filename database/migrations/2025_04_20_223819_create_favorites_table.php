<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Унікальний індекс, щоб уникнути дублювання
            $table->unique(['user_id', 'accommodation_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}