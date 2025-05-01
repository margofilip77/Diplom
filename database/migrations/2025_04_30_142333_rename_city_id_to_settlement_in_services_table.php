<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCityIdToSettlementInServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('city_id', 'settlement');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->renameColumn('settlement', 'city_id');
        });
    }
}