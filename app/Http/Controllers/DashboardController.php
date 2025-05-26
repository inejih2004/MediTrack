<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\ExpirationDate;
use App\Models\MovementHistory;
use App\Models\Material;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Retrieve service_id and role from session
        $serviceId = session('service_id');
        $role = session('role');

        // Log session data for debugging
        Log::info('Dashboard Session Data', ['service_id' => $serviceId, 'role' => $role]);

        // Handle stock manager
        if ($role === 'stock_manager') {
            return view('stock_manager.dashboard');
        }

        // If admin, redirect to admin dashboard
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // If accountant, redirect to accountant dashboard
        if ($role === 'accountant') {
            return view('accountant.dashboard');
        }

        // For service_head, ensure they have a service_id
        if (!$serviceId) {
            Log::error('No service_id found for user', ['role' => $role]);
            return redirect()->route('login')->with('error', 'Aucun service associé à cet utilisateur.');
        }

        // Fetch data for the specific service
        $currentStock = Stock::where('service_id', $serviceId)->sum('quantity');
        $nearExpiry = ExpirationDate::where('service_id', $serviceId)
            ->where('alert_status', 'near_expiration')
            ->count();
        $approvedRequests = MovementHistory::where('service_id', $serviceId)
            ->where('reason', 'Received from supplier')
            ->count();
        $rejectedRequests = MovementHistory::where('service_id', $serviceId)
            ->where('reason', 'Loaned to Chirurgie')
            ->count();

        $materialTypes = Material::selectRaw('type, COUNT(*) as count')
            ->whereIn('material_id', Stock::where('service_id', $serviceId)->pluck('material_id'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $monthlyUsage = MovementHistory::selectRaw('MONTH(date) as month, SUM(quantity) as total')
            ->where('service_id', $serviceId)
            ->where('movement_type', 'consumption')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $recentActivities = MovementHistory::with(['material', 'service'])
            ->where('service_id', $serviceId)
            ->whereHas('material')
            ->whereHas('service')
            ->orderBy('date', 'desc')
            ->take(3)
            ->get();

        $service = Service::find($serviceId);

        // Fetch near-expiration alerts (within 30 days before to 14 days after)
        $currentDate = Carbon::now('GMT');
        $expirationAlerts = ExpirationDate::where('service_id', $serviceId)
            ->where('alert_status', 'active')
            ->whereBetween('expiration_date', [
                $currentDate->copy()->subDays(30),
                $currentDate->copy()->addDays(14)
            ])
            ->where('expiration_date', '>=', $currentDate)
            ->with('material')
            ->get()
            ->map(function ($item) use ($currentDate) {
                $expirationDate = Carbon::parse($item->expiration_date);
                $daysLeft = $currentDate->diffInDays($expirationDate, false);
                $alertType = $daysLeft <= 14 ? 'critical' : 'warning';
                return [
                    'id' => $item->expiration_id,
                    'material_id' => $item->material_id,
                    'material_name' => $item->material ? $item->material->material_name : 'Matériau inconnu',
                    'expiration_date' => $item->expiration_date,
                    'days_left' => $daysLeft,
                    'alert_type' => $alertType
                ];
            });

        // Fetch expired materials (expiration_date < current date)
        $expiredMaterialsQuery = ExpirationDate::where('service_id', $serviceId)
            ->where('alert_status', 'active')
            ->where('expiration_date', '<', $currentDate)
            ->with('material');
        Log::info('Expired Materials Query', ['query' => $expiredMaterialsQuery->toSql(), 'bindings' => $expiredMaterialsQuery->getBindings()]);
        $expiredMaterials = $expiredMaterialsQuery->get()
            ->map(function ($item) use ($currentDate) {
                $expirationDate = Carbon::parse($item->expiration_date);
                $daysLeft = $currentDate->diffInDays($expirationDate, false);
                return [
                    'id' => $item->expiration_id, // Use expiration_id explicitly
                    'material_id' => $item->material_id ?? null,
                    'material_name' => $item->material ? $item->material->material_name : 'Matériau inconnu',
                    'expiration_date' => $item->expiration_date,
                    'days_left' => $daysLeft
                ];
            });

        // Log expired materials for debugging
        Log::info('Expired Materials', ['count' => $expiredMaterials->count(), 'data' => $expiredMaterials->toArray()]);

        // Generate low stock alerts
        $lowStockAlerts = Stock::where('service_id', $serviceId)
            ->where('quantity', '<', \DB::raw('threshold'))
            ->with('material')
            ->get()
            ->map(function ($item) {
                return [
                    'material_name' => $item->material ? $item->material->material_name : 'Matériau inconnu',
                    'quantity' => $item->quantity,
                    'threshold' => $item->threshold
                ];
            });

        return view('dashboard', compact(
            'currentStock',
            'nearExpiry',
            'approvedRequests',
            'rejectedRequests',
            'materialTypes',
            'monthlyUsage',
            'recentActivities',
            'service',
            'expirationAlerts',
            'lowStockAlerts',
            'expiredMaterials'
        ));
    }

    public function disposeMaterial(Request $request, $expirationId)
    {
        $expiration = ExpirationDate::findOrFail($expirationId);
        $serviceId = session('service_id');

        // Verify the expiration belongs to the user's service
        if ($expiration->service_id != $serviceId) {
            Log::warning('Unauthorized dispose attempt', ['expiration_id' => $expirationId, 'service_id' => $serviceId]);
            return redirect()->route('dashboard')->with('error', 'Action non autorisée.');
        }

        // Update expiration status
        $expiration->alert_status = 'disposed';
        $expiration->save();

        // Find the stock entry
        $stock = Stock::where('service_id', $serviceId)
            ->where('material_id', $expiration->material_id)
            ->first();

        if ($stock) {
            $quantity = $stock->quantity;
            $stock->quantity = 0;
            $stock->save();

            // Log the disposal in movement history
            MovementHistory::create([
                'material_id' => $expiration->material_id,
                'service_id' => $serviceId,
                'movement_type' => 'disposal',
                'quantity' => $quantity,
                'date' => Carbon::now('GMT'),
                'reason' => 'Matériau Expiré - Jeté'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Matériau expiré jeté avec succès.');
    }

    public function returnMaterial(Request $request, $expirationId)
    {
        $expiration = ExpirationDate::findOrFail($expirationId);
        $serviceId = session('service_id');

        // Verify the expiration belongs to the user's service
        if ($expiration->service_id != $serviceId) {
            Log::warning('Unauthorized return attempt', ['expiration_id' => $expirationId, 'service_id' => $serviceId]);
            return redirect()->route('dashboard')->with('error', 'Action non autorisée.');
        }

        // Update expiration status
        $expiration->alert_status = 'returned';
        $expiration->save();

        // Find the stock entry
        $stock = Stock::where('service_id', $serviceId)
            ->where('material_id', $expiration->material_id)
            ->first();

        if ($stock) {
            $quantity = $stock->quantity;
            $stock->quantity = 0;
            $stock->save();

            // Log the return in movement history
            MovementHistory::create([
                'material_id' => $expiration->material_id,
                'service_id' => $serviceId,
                'movement_type' => 'return',
                'quantity' => $quantity,
                'date' => Carbon::now('GMT'),
                'reason' => 'Matériau Expiré - Retourné au Fournisseur'
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Matériau expiré retourné avec succès.');
    }
}