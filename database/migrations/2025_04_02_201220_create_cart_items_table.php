<?php
// database/migrations/xxxx_xx_xx_create_cart_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained();
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->integer('guest_count');
            $table->foreignId('meal_option_id')->constrained();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
