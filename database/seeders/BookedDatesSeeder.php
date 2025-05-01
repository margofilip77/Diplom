<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accommodation;
use Carbon\Carbon;

class BookedDatesSeeder extends Seeder
{
    public function run()
    {
        // Отримуємо перші 30 помешкань
        $accommodations = Accommodation::take(30)->get();

        foreach ($accommodations as $accommodation) {
            // Генеруємо 2-5 випадкових періодів для кожного помешкання
            $numberOfBookings = rand(2, 5);

            for ($i = 0; $i < $numberOfBookings; $i++) {
                // Випадкова дата початку в межах 2025 року
                $startDate = Carbon::create(2025, rand(1, 12), rand(1, 28));
                // Випадкова тривалість перебування (від 2 до 7 днів)
                $duration = rand(2, 7);
                $endDate = $startDate->copy()->addDays($duration);

                // Додаємо запис у таблицю booked_dates
                $accommodation->bookedDates()->create([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
    }
}