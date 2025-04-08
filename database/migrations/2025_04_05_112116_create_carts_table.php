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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->json('guests_count'); // Зберігаємо як JSON для гнучкості
            $table->string('accommodation_photo')->nullable(); // Додаємо колонку для фото
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
