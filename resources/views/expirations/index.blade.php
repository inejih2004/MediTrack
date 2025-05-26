@extends('layouts.app')

@section('title', 'Expirations')

@section('content')
    <div class="header-row">
        <h1>Matériaux Proches de l'Expiration</h1>
        <div class="header-actions">
            <a href="{{ route('requests.create') }}" class="btn btn-primary mr-2">+ Demander des Matériaux</a>
            <div class="user-info">
                <span class="mr-2">Dr. Sarah Johnson</span>
                <span class="badge badge-primary">Chef de Service - Chirurgie</span>
            </div>
        </div>
    </div>

    <div class="activity-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Matériau</th>
                    <th>Date d'Expiration</th>
                    <th>Statut</th>
                    <th>Dernière Alerte</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expirations as $expiration)
                    <tr>
                        <td>{{ optional($expiration->material)->material_name ?? 'Matériau non trouvé' }}</td>
                        <td>{{ \Carbon\Carbon::parse($expiration->expiration_date)->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $expiration->alert_status == 'near_expiration' ? 'badge-warning' : ($expiration->alert_status == 'expired' ? 'badge-danger' : 'badge-success') }}">
                                {{ ucfirst(str_replace('_', ' ', $expiration->alert_status)) }}
                            </span>
                        </td>
                        <td>{{ $expiration->last_alert_date ? \Carbon\Carbon::parse($expiration->last_alert_date)->format('d M Y') : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection