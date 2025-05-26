@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
    <div class="header-row">
        <h1>Vue d'Ensemble du Tableau de Bord - {{ $service->service_name ?? 'Aucun Service' }}</h1>
        <div class="header-actions">
            <a href="{{ route('requests.create') }}" class="btn btn-primary mr-2">+ Demander des Matériaux</a>
            <a href="{{ route('reports.download') }}" class="btn btn-outline-secondary mr-2">Télécharger le Rapport</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">Déconnexion</button>
            </form>
            <div class="user-info">
                <span class="mr-2">Dr. Sarah Johnson</span>
                <span class="badge badge-primary">Chef de Service - {{ $service->service_name ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Expired Materials Section -->
    <div class="expired-materials-section mt-4">
        @if ($expiredMaterials->isNotEmpty())
            <div class="alert alert-danger">
                <h4>Matériaux Expirés</h4>
                <ul>
                    @foreach ($expiredMaterials as $material)
                        @if (isset($material['id']) && !empty($material['id']))
                            <li>
                                {{ $material['material_name'] }} :
                                <span class="text-danger">Expiré depuis {{ abs($material['days_left']) }} jours</span>
                                (Date : {{ $material['expiration_date'] }})
                                <form action="{{ route('dashboard.dispose', $material['id']) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger ml-2">Jeter</button>
                                </form>
                                <form action="{{ route('dashboard.return', $material['id']) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning ml-2">Retourner</button>
                                </form>
                            </li>
                        @else
                            <li>
                                {{ $material['material_name'] }} : ID manquant ou invalide (Date : {{ $material['expiration_date'] }})
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @else
            <div class="alert alert-info">
                Aucun matériau expiré trouvé. Vérifiez la base de données pour les enregistrements dans <code>expiration_dates</code> avec <code>expiration_date < '{{ \Carbon\Carbon::now('GMT')->toDateString() }}'</code> et <code>alert_status = 'active'</code> pour <code>service_id = {{ session('service_id') ?? 'inconnu' }}</code>.
            </div>
        @endif
    </div>

    <!-- Alerts Section (Near Expiration and Low Stock) -->
    @if ($expirationAlerts->isNotEmpty() || $lowStockAlerts->isNotEmpty())
        <div class="alerts-section mt-4">
            @if ($expirationAlerts->isNotEmpty())
                <div class="alert alert-warning">
                    <h4>Alerte : Matériaux proches de l'expiration</h4>
                    <ul>
                        @foreach ($expirationAlerts as $alert)
                            <li>
                                {{ $alert['material_name'] }} :
                                @if ($alert['alert_type'] == 'critical')
                                    <span class="text-danger">Expire dans {{ $alert['days_left'] }} jours</span>
                                @else
                                    <span class="text-warning">Expire dans {{ $alert['days_left'] }} jours</span>
                                @endif
                                (Date : {{ $alert['expiration_date'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($lowStockAlerts->isNotEmpty())
                <div class="alert alert-danger">
                    <h4>Alerte : Stock Bas</h4>
                    <ul>
                        @foreach ($lowStockAlerts as $alert)
                            <li>{{ $alert['material_name'] }} : {{ $alert['quantity'] }} (Seuil : {{ $alert['threshold'] }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-info mt-4">
            Aucune alerte pour le moment.
        </div>
    @endif

    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <h3>{{ $currentStock }}</h3>
                <p>Stock Actuel</p>
                <p class="text-success">↑ 12% depuis le mois dernier</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>{{ $nearExpiry }}</h3>
                <p>Proche de l'Expiration</p>
                <p class="text-warning">↑ 8% depuis la semaine dernière</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>{{ $approvedRequests }}</h3>
                <p>Demandes Approuvées</p>
                <p class="text-success">↑ 24% depuis le mois dernier</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h3>{{ $rejectedRequests }}</h3>
                <p>Demandes Rejetées</p>
                <p class="text-danger">↑ 5% depuis le mois dernier</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="chart-card">
                <h4>Distribution des Types de Matériaux</h4>
                <div class="chart-container">
                    <canvas id="materialTypesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h4>Tendances d'Utilisation Mensuelles</h4>
                <div class="chart-container">
                    <canvas id="monthlyUsageChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="activity-table">
        <h4>Activité Récente</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activité</th>
                    <th>Matériau</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentActivities as $activity)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($activity->date)->format('M d, h:i A') }}</td>
                        <td>{{ $activity->movement_type == 'entry' ? 'Demande de Matériau' : 'Mise à Jour du Stock' }}</td>
                        <td>{{ optional($activity->material)->material_name ?? 'Matériau non trouvé' }}</td>
                        <td>
                            <span class="badge {{ $activity->movement_type == 'entry' ? 'badge-success' : 'badge-warning' }}">
                                {{ $activity->movement_type == 'entry' ? 'Approuvé' : 'Mis à Jour' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('activities.index') }}" class="float-right">Voir Tout</a>
    </div>
@endsection

@section('scripts')
    <script>
        const materialTypesCtx = document.getElementById('materialTypesChart').getContext('2d');
        new Chart(materialTypesCtx, {
            type: 'pie',
            data: {
                labels: ['Médicaments', 'Fournitures Chirurgicales', 'Outils Diagnostiques', 'EPI', 'Autres'],
                datasets: [{
                    data: [
                        {{ $materialTypes['medical'] ?? 0 }},
                        {{ $materialTypes['consumable'] ?? 0 }},
                        {{ $materialTypes['non_medical'] ?? 0 }},
                        0, 0
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                    borderWidth: 1,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 },
                        padding: 10
                    }
                }
            }
        });

        const monthlyUsageCtx = document.getElementById('monthlyUsageChart').getContext('2d');
        new Chart(monthlyUsageCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Utilisation des Matériaux',
                    data: [
                        {{ $monthlyUsage[1] ?? 0 }},
                        {{ $monthlyUsage[2] ?? 0 }},
                        {{ $monthlyUsage[3] ?? 0 }},
                        {{ $monthlyUsage[4] ?? 0 }},
                        {{ $monthlyUsage[5] ?? 0 }},
                        {{ $monthlyUsage[6] ?? 0 }}
                    ],
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 12 },
                            padding: 10
                        },
                        grid: { color: '#e9ecef' }
                    },
                    x: {
                        ticks: {
                            font: { size: 12 },
                            padding: 10
                        },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#333',
                        titleFont: { size: 12 },
                        bodyFont: { size: 11 },
                        padding: 10
                    }
                }
            }
        });
    </script>
@endsection