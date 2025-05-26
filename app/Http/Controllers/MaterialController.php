<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Stock;

class MaterialController extends Controller
{
    public function index()
    {
        $serviceId = 2; // Chirurgie
        $materials = Material::whereIn('material_id', Stock::where('service_id', $serviceId)->pluck('material_id'))
            ->with(['stocks' => function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            }])
            ->get();

        return view('materials.index', compact('materials'));
    }
}