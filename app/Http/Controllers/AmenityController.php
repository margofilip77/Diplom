<?php

namespace App\Http\Controllers;

use App\Models\Amenity; // Модель для зручностей
use App\Models\Accommodation; // Модель для помешкання
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function show($id)
    {
        // Отримуємо помешкання з його зручностями та категоріями
        $accommodation = Accommodation::with('amenities.category')->findOrFail($id);

        // Повертаємо дані в представлення
        return view('details', compact('accommodation'));
    }
}
