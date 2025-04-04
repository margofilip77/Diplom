<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddColumnsToAccommodationTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement('
            ALTER TABLE accommodations
            ADD COLUMN children TEXT DEFAULT NULL,
            ADD COLUMN beds TEXT DEFAULT NULL,
            ADD COLUMN age_restrictions TEXT DEFAULT NULL,
            ADD COLUMN pets_allowed TEXT DEFAULT NULL,
            ADD COLUMN payment_options TEXT DEFAULT NULL,
            ADD COLUMN parties_allowed TEXT DEFAULT NULL;
        ');
    }
}
