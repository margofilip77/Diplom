<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Package;

class ServiceController extends Controller
{
    public function index()
    {
        // Витягуємо всі пакети разом із їхніми послугами
        $packages = Package::with('services')->get();

        // Витягуємо всі послуги (для додаткової секції)
        $services = Service::all();

        return view('services.index', compact('packages', 'services'));
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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'region_id' => 'required|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'range' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'image' => 'required|image|max:2048', // Фото обов’язкове, макс. 2MB
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Послугу додано!');
    }
}
