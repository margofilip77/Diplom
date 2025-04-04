<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->text('rules')->nullable()->after('detailed_description');
            $table->text('guests')->nullable()->after('rules');
            $table->text('cancellation_policy')->nullable()->after('guests');
        });
    }

    public function down()
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropColumn(['rules', 'guests', 'cancellation_policy']);
        });
    }
};
