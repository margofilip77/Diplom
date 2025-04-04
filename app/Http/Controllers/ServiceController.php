<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all(); // Отримання всіх послуг із БД
        return view('services.index', compact('services'));
    }
    

    // Метод для відображення окремої послуги
    public function show($id)
    {
        // Знайти послугу за ID
        $service = Service::find($id);
    
        if (!$service) {
            // Якщо послуга не знайдена, перенаправляємо на сторінку послуг із повідомленням
            return redirect()->route('services')->with('error', 'Послуга не знайдена');
        }
    
        // Повертаємо представлення для конкретної послуги
        return view('service-show', compact('service'));
    }
    
    
}

