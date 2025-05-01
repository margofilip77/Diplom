<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Видаляємо зовнішній ключ
            $table->dropForeign('services_city_id_foreign');

            // Змінюємо тип стовпця settlement на string
            $table->string('settlement', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Повертаємо тип стовпця назад на integer
            $table->integer('settlement')->change();

            // Відновлюємо зовнішній ключ (якщо потрібно для відкоту)
            $table->foreign('settlement')
                  ->references('id')
                  ->on('cities')
                  ->onDelete('cascade');
        });
    }
};