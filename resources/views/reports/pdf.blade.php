<!DOCTYPE html>
<html>
<head>
    <title>Rapport - Chirurgie</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f7fa; }
    </style>
</head>
<body>
    <h1>Rapport - Service de Chirurgie</h1>
    <p>Date: {{ now()->format('d M Y') }}</p>

    <h2>Stock Actuel</h2>
    <table>
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

    <h2>Historique des Mouvements</h2>
    <table>
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
</body>
</html>