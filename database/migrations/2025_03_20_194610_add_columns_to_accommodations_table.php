<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('accommodations', function (Blueprint $table) {
        $table->string('settlement', 255)->nullable();
        $table->string('region', 255)->nullable();
        $table->text('detailed_description')->nullable();
    });
}

public function down()
{
    Schema::table('accommodations', function (Blueprint $table) {
        $table->dropColumn('settlement');
        $table->dropColumn('region');
        $table->dropColumn('detailed_description');
    });
}

};
