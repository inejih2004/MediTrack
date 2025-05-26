<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\MovementHistory;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        $serviceId = 2; // Chirurgie
        $stocks = Stock::where('service_id', $serviceId)->with('material')->get();
        $movements = MovementHistory::where('service_id', $serviceId)->with(['material'])->get();

        return view('reports.index', compact('stocks', 'movements'));
    }

    public function download()
    {
        $serviceId = 2; // Chirurgie
        $stocks = Stock::where('service_id', $serviceId)->with('material')->get();
        $movements = MovementHistory::where('service_id', $serviceId)->with(['material'])->get();

        $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('reports.pdf', compact('stocks', 'movements'));
        return $pdf->download('rapport_chirurgie_' . now()->format('Ymd') . '.pdf');
    }
}