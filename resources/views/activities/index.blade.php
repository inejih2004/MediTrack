@extends('layouts.app')

@section('title', 'Activités')

@section('content')
    <div class="header-row">
        <h1>Toutes les Activités</h1>
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
                    <th>Date</th>
                    <th>Activité</th>
                    <th>Matériau</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
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
    </div>
@endsection