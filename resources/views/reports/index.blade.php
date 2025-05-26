@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
    <div class="header-row">
        <h1>Rapports</h1>
        <div class="header-actions">
            <a href="{{ route('requests.create') }}" class="btn btn-primary mr-2">+ Demander des Matériaux</a>
            <a href="{{ route('reports.download') }}" class="btn btn-outline-secondary">Télécharger le Rapport</a>
            <div class="user-info">
                <span class="mr-2">Dr. Sarah Johnson</span>
                <span class="badge badge-primary">Chef de Service - Chirurgie</span>
            </div>
        </div>
    </div>

    <div class="activity-table">
        <h4>Stock Actuel</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Matériau</th>
                    <th>Quantité</th>
                    <th>Seuil</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                    <tr>
                        <td>{{ optional($stock->material)->material_name ?? 'Matériau non trouvé' }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->threshold }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4>Historique des Mouvements</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Matériau</th>
                    <th>Type de Mouvement</th>
                    <th>Quantité</th>
                    <th>Raison</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($movement->date)->format('d M Y, h:i A') }}</td>
                        <td>{{ optional($movement->material)->material_name ?? 'Matériau non trouvé' }}</td>
                        <td>{{ ucfirst($movement->movement_type) }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->reason }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection