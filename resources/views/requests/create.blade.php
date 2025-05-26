@extends('layouts.app')

@section('title', 'Nouvelle Demande')

@section('content')
    <div class="header-row">
        <h1>Nouvelle Demande de Matériau</h1>
        <div class="header-actions">
            <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary">Retour</a>
            <div class="user-info">
                <span class="mr-2">Dr. Sarah Johnson</span>
                <span class="badge badge-primary">Chef de Service - Chirurgie</span>
            </div>
        </div>
    </div>

    <div class="activity-table">
        <form method="POST" action="{{ route('requests.store') }}">
            @csrf
            <div class="form-group">
                <label for="material_id">Matériau</label>
                <select name="material_id" id="material_id" class="form-control" required>
                    <option value="">Sélectionnez un matériau</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->material_id }}">{{ $material->material_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="supplier_id">Fournisseur</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Sélectionnez un fournisseur</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantité</label>
                <input type="number" name="quantity" id="quantity" class="form-control" required min="1">
            </div>
            <div class="form-group">
                <label for="cost">Coût (€)</label>
                <input type="number" name="cost" id="cost" class="form-control" required min="0" step="0.01">
            </div>
            <div class="form-group">
                <label for="purchase_date">Date d'Achat</label>
                <input type="date" name="purchase_date" id="purchase_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="delivery_date">Date de Livraison</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Soumettre la Demande</button>
        </form>
    </div>
@endsection