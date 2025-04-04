<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddCheckinCheckoutColumnsSeeder extends Seeder
{
    public function run()
    {
        DB::statement('
            ALTER TABLE accommodations
            ADD COLUMN checkin_time VARCHAR(50) DEFAULT NULL,
            ADD COLUMN checkout_time VARCHAR(50) DEFAULT NULL;
        ');
    }
}

