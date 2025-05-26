<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpirationDate;

class ExpirationController extends Controller
{
    public function index()
    {
        $serviceId = 2; // Chirurgie
        $expirations = ExpirationDate::where('service_id', $serviceId)
            ->with(['material'])
            ->get();

        return view('expirations.index', compact('expirations'));
    }
}