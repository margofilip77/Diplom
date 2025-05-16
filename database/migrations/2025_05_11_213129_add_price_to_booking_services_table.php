<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToBookingServicesTable extends Migration
{
    public function up()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('service_id'); // Додаємо поле для ціни
        });
    }

    public function down()
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}