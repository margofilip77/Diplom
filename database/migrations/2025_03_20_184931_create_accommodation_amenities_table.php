<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('accommodation_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained('accommodations');
            $table->foreignId('amenity_id')->constrained('amenities');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accommodation_amenities');
    }
};
