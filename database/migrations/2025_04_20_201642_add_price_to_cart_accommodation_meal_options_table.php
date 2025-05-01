<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToCartAccommodationMealOptionsTable extends Migration
{
    public function up()
    {
        Schema::table('cart_accommodation_meal_option', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('guests_count'); // Додаємо поле price
        });
    }

    public function down()
    {
        Schema::table('cart_accommodation_meal_option', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}