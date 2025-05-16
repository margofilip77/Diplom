<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_meal_option', function (Blueprint $table) {
            $table->integer('guests_count')->default(1)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('booking_meal_option', function (Blueprint $table) {
            $table->dropColumn('guests_count');
        });
    }
};
