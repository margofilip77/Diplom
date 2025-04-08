<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('cart_accommodation', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_id')->constrained('carts')->onDelete('cascade');
        $table->foreignId('accommodation_id')->constrained('accommodations');
        $table->date('checkin_date');
        $table->date('checkout_date');
        $table->json('guests_count');
        $table->string('accommodation_photo');
        $table->decimal('price', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_accommodation');
    }
};
