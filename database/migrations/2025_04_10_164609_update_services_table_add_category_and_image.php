<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('image')->nullable(); // Шлях до фото послуги
            $table->foreignId('category_id')->nullable()->constrained('service_categories')->onDelete('set null'); // Зв’язок із категоріями
            $table->dropColumn('category'); // Видаляємо старе поле category, якщо воно було
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
