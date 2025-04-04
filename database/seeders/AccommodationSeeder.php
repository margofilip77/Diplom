<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccommodationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accommodations')->insert([
            [
                'name' => 'Готель Сонячний берег',
                'location' => 'Київ, Україна',
                'price_per_night' => 1500,
                'description' => 'Затишний готель у центрі Києва з чудовими видами на місто.',
                'image' => 'images/1.jpg', // Додайте шлях до фото
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Вілла Лазурний Берег',
                'location' => 'Одеса, Україна',
                'price_per_night' => 2500,
                'description' => 'Розкішна вілла біля моря, ідеальна для відпочинку.',
                'image' => 'images/2.jpg', // Додайте шлях до фото
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Апартаменти Green Life',
                'location' => 'Львів, Україна',
                'price_per_night' => 1200,
                'description' => 'Стильні апартаменти в історичному центрі Львова.',
                'image' => 'images/3.jpg', // Додайте шлях до фото
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

