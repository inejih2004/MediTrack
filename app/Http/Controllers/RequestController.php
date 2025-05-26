<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\MovementHistory;

class RequestController extends Controller
{
    public function index()
    {
        $serviceId = 2; // Chirurgie
        $requests = Purchase::where('service_id', $serviceId)
            ->with(['material', 'supplier'])
            ->get();

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $materials = Material::all();
        $suppliers = Supplier::all();
        return view('requests.create', compact('materials', 'suppliers'));
    }

    public function store(Request $request)
    {
        $serviceId = 2; // Chirurgie
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,material_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'quantity' => 'required|integer|min:1',
            'cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'delivery_date' => 'required|date|after_or_equal:purchase_date',
        ]);

        $purchase = Purchase::create([
            'service_id' => $serviceId,
            'material_id' => $validated['material_id'],
            'supplier_id' => $validated['supplier_id'],
            'quantity' => $validated['quantity'],
            'cost' => $validated['cost'],
            'purchase_date' => $validated['purchase_date'],
            'delivery_date' => $validated['delivery_date'],
            'invoice_number' => 'INV' . rand(100, 999),
        ]);

        // Update stock
        $stock = \App\Models\Stock::where('service_id', $serviceId)
            ->where('material_id', $validated['material_id'])
            ->first();
        if ($stock) {
            $stock->quantity += $validated['quantity'];
            $stock->save();
        } else {
            \App\Models\Stock::create([
                'service_id' => $serviceId,
                'material_id' => $validated['material_id'],
                'quantity' => $validated['quantity'],
                'threshold' => 50, // Default threshold
            ]);
        }

        // Log movement
        MovementHistory::create([
            'material_id' => $validated['material_id'],
            'service_id' => $serviceId,
            'movement_type' => 'entry',
            'quantity' => $validated['quantity'],
            'date' => now(),
            'reason' => 'Received from supplier',
        ]);

        return redirect()->route('requests.index')->with('success', 'Demande créée avec succès.');
    }
}