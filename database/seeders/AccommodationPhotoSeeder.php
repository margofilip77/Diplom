<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use App\Models\AccommodationPhoto;
use Illuminate\Support\Facades\Storage;

class AccommodationPhotoSeeder extends Seeder {
    public function run() {
        // Отримуємо всі помешкання
        $accommodations = Accommodation::all();

        foreach ($accommodations as $accommodation) {
            // Додаємо випадкову кількість фото (від 1 до 5)
            $photoCount = rand(1, 5);

            for ($i = 0; $i < $photoCount; $i++) {
                AccommodationPhoto::create([
                    'accommodation_id' => $accommodation->id,
                    'photo_path' => 'accommodations/sample' . rand(1, 10) . '.jpg' // Імітація фото
                ]);
            }
        }
    }
}

