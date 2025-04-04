<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('accommodation_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->onDelete('cascade'); // Зв'язок з помешканням
            $table->string('photo_path'); // Шлях до фото
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('accommodation_photos');
    }
};
