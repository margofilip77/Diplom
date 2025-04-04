<?php

namespace Database\Seeders;

use App\Models\Service;

Service::create([
    'title' => 'Екскурсія в Карпати',
    'description' => 'Неймовірна подорож до Карпат з гідом.',
    'price' => 1200,
]);

Service::create([
    'title' => 'Подорож до Львова',
    'description' => 'Оглядова екскурсія по Львову.',
    'price' => 800,
]);
