<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseToSupportMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->text('response')->nullable()->after('message');
            $table->timestamp('responded_at')->nullable()->after('last_viewed_at');
        });
    }

    public function down()
    {
        Schema::table('support_messages', function (Blueprint $table) {
            $table->dropColumn('response');
            $table->dropColumn('responded_at');
        });
    }
}