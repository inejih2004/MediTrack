<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovementHistory;

class ActivityController extends Controller
{
    public function index()
    {
        $serviceId = 2; // Chirurgie
        $activities = MovementHistory::where('service_id', $serviceId)
            ->with(['material', 'service'])
            ->orderBy('date', 'desc')
            ->get();

        return view('activities.index', compact('activities'));
    }
}