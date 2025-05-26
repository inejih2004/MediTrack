@extends('layouts.app')

@section('title', 'Matériaux')

@section('content')
    <div class="header-row">
        <h1>Liste des Matériaux</h1>
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
                    <th>Nom du Matériau</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Quantité en Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materials as $material)
                    <tr>
                        <td>{{ $material->material_name }}</td>
                        <td>{{ ucfirst($material->type) }}</td>
                        <td>{{ $material->description }}</td>
                        <td>{{ $material->stocks->first()->quantity ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection