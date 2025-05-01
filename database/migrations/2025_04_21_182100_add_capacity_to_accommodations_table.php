<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCapacityToAccommodationsTable extends Migration
{
    public function up()
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->integer('capacity')->nullable()->after('price_per_night'); // Додаємо поле capacity
        });
    }

    public function down()
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
}
