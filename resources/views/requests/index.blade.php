@extends('layouts.app')

@section('title', 'Demandes')

@section('content')
    <div class="header-row">
        <h1>Liste des Demandes</h1>
        <div class="header-actions">
            <a href="{{ route('requests.create') }}" class="btn btn-primary mr-2">+ Demander des Matériaux</a>
            <div class="user-info">
                <span class="mr-2">Dr. Sarah Johnson</span>
                <span class="badge badge-primary">Chef de Service - Chirurgie</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="activity-table">
        <table class="table">
            <thead>
                <tr>
                    <th>Matériau</th>
                    <th>Fournisseur</th>
                    <th>Quantité</th>
                    <th>Coût</th>
                    <th>Date d'Achat</th>
                    <th>Date de Livraison</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ optional($request->material)->material_name ?? 'Matériau non trouvé' }}</td>
                        <td>{{ optional($request->supplier)->supplier_name ?? 'Fournisseur non trouvé' }}</td>
                        <td>{{ $request->quantity }}</td>
                        <td>{{ $request->cost }} €</td>
                        <td>{{ \Carbon\Carbon::parse($request->purchase_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($request->delivery_date)->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection