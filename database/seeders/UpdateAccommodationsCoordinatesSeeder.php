<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Accommodation;

class UpdateAccommodationsCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $coordinates = [
            'Верховина' => ['latitude' => 48.1519, 'longitude' => 24.8237],
            'Космач' => ['latitude' => 48.3333, 'longitude' => 24.8167],
            'Татарів' => ['latitude' => 48.3467, 'longitude' => 24.5833],
            'Виженка' => ['latitude' => 48.2333, 'longitude' => 25.1833],
            'Непоротове' => ['latitude' => 48.4167, 'longitude' => 25.6167],
            'Путила' => ['latitude' => 47.9833, 'longitude' => 25.0833],
            'Пульмо' => ['latitude' => 51.5167, 'longitude' => 23.6667],
            'Згорани' => ['latitude' => 51.3833, 'longitude' => 23.7500],
            'Гаразджа' => ['latitude' => 50.6167, 'longitude' => 25.3167],
            'Пересопниця' => ['latitude' => 50.6667, 'longitude' => 26.0833],
            'Дермань' => ['latitude' => 50.3833, 'longitude' => 26.3167],
            'Хрінники' => ['latitude' => 50.7333, 'longitude' => 26.3167],
            'Коропець' => ['latitude' => 48.9333, 'longitude' => 25.3833],
            'Бучач' => ['latitude' => 49.0667, 'longitude' => 25.4000],
            'Микулинці' => ['latitude' => 49.4000, 'longitude' => 25.6167],
            'Чигирин' => ['latitude' => 49.0833, 'longitude' => 32.6667],
            'Мельники' => ['latitude' => 48.9167, 'longitude' => 32.3167],
            'Канів' => ['latitude' => 49.7518, 'longitude' => 31.4697],
            'Великі Сорочинці' => ['latitude' => 50.0333, 'longitude' => 33.9500],
            'Опішня' => ['latitude' => 49.9667, 'longitude' => 34.6167],
            'Диканька' => ['latitude' => 49.8167, 'longitude' => 34.5333],
            'Лютіж' => ['latitude' => 50.6833, 'longitude' => 30.3833],
            'Трипілля' => ['latitude' => 50.1167, 'longitude' => 30.7833],
            'Проців' => ['latitude' => 50.2167, 'longitude' => 30.8167],
            'Яноші' => ['latitude' => 48.1667, 'longitude' => 22.3167],
            'Тухля' => ['latitude' => 48.9167, 'longitude' => 23.4667],
            'Славське' => ['latitude' => 48.8500, 'longitude' => 23.4500],
            'Поляна' => ['latitude' => 48.6167, 'longitude' => 22.9667],
            'Колочава' => ['latitude' => 48.4333, 'longitude' => 23.7000],
        ];

        foreach ($coordinates as $settlement => $coords) {
            Accommodation::where('settlement', $settlement)->update([
                'latitude' => $coords['latitude'],
                'longitude' => $coords['longitude'],
            ]);
        }
    }
}
