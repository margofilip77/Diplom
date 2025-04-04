<?php
// database/migrations/{timestamp}_add_image_to_cart_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageToCartItemsTable extends Migration
{
    /**
     * Виконати міграцію.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Додати поле для зображення
            $table->string('image')->nullable();
        });
    }

    /**
     * Відкотити міграцію.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Видалити поле зображення
            $table->dropColumn('image');
        });
    }
}
