<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRangeFromServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('range');
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->integer('range')->nullable();
        });
    }
}