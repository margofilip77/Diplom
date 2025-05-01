<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        // Витягуємо всі пакети разом із їхніми послугами
        $packages = Package::with('services')->get();

        return view('packages.index', compact('packages'));
    }

    /**
     * Детальна сторінка пакета
     */
    public function show(Package $package)
    {
        $package->load('services');
        return view('packages.show', compact('package'));
    }
}