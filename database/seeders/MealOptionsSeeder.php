<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealOptionsSeeder extends Seeder {
    public function run() {
        DB::table('meal_options')->insert([
            ['name' => 'Триразове харчування'],
            ['name' => 'Сніданки'],
            ['name' => 'Обіди'],
            ['name' => 'Вечері'],
            ['name' => 'Без харчування'],
        ]);
    }
}
