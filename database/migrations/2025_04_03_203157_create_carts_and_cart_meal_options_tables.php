<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    // Створюємо таблицю carts
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');  // Зовнішній ключ на користувача
        $table->foreignId('accommodation_id')->constrained()->onDelete('cascade');  // Зовнішній ключ на помешкання
        $table->integer('guests')->default(1);  // Кількість гостей
        $table->date('checkin_date');
        $table->date('checkout_date');
        $table->decimal('total_price', 8, 2);  // Загальна ціна для цього елемента
        $table->string('photo_url')->nullable();  // Додаємо стовпець для шляху до фото
        $table->timestamps();
    });

    // Створюємо таблицю cart_meal_options
    Schema::create('cart_meal_options', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_id')->constrained()->onDelete('cascade');  // Зовнішній ключ на кошик
        $table->foreignId('meal_option_id')->constrained()->onDelete('cascade');  // Зовнішній ключ на тип харчування
        $table->integer('guests_count');  // Кількість гостей, що обрали цей тип харчування
        $table->timestamps();
    });
}

public function down()
{
    // Видаляємо таблиці при скасуванні міграції
    Schema::dropIfExists('cart_meal_options');
    Schema::dropIfExists('carts');
}

};
