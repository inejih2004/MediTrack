<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediTrack - @yield('title')</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 20px 15px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar h2 {
            font-size: 1.6em;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 1px;
        }

        .sidebar p {
            font-size: 0.85em;
            margin: 0 0 30px;
            text-align: center;
            opacity: 0.8;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1em;
            border-radius: 5px;
            margin: 5px 0;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1em;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 0.8em;
            opacity: 0.7;
        }

        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 250px;
            z-index: 1001;
            background-color: #2c3e50;
            border: none;
            color: white;
            padding: 10px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            display: none;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
            background-color: #f8f9fc;
            transition: margin-left 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 70px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .header-row h1 {
            font-size: 1.8em;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header-actions .btn {
            font-size: 0.9em;
            padding: 8px 16px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .header-actions .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-actions .user-info {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-actions .badge-primary {
            background-color: #3498db;
            padding: 5px 10px;
            font-size: 0.9em;
        }

        .stat-card {
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 1.8em;
            font-weight: bold;
            margin: 0;
            color: #2c3e50;
        }

        .stat-card p {
            margin: 5px 0 0;
            font-size: 0.9em;
            color: #666;
        }

        .chart-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .chart-card h4 {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .chart-container {
            position: relative;
            width: 100%;
            height: 250px;
        }

        .activity-table {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .activity-table h4 {
            font-size: 1.2em;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .activity-table a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        .activity-table a:hover {
            text-decoration: underline;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .sidebar.collapsed {
                transform: translateX(0);
                width: 250px;
            }

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.collapsed {
                margin-left: 250px;
            }

            .header-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .stat-card {
                margin-bottom: 15px;
            }

            .chart-container {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="sidebar">
        <div class="text-center">
            <h2>MediTrack</h2>
            <p>Portail des Services Hospitaliers</p>
        </div>
        <nav>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> <span>Tableau de Bord</span>
            </a>
            <a href="{{ route('materials.index') }}" class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i> <span>Matériaux</span>
            </a>
            <a href="{{ route('requests.index') }}" class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> <span>Demandes</span>
            </a>
            <a href="{{ route('expirations.index') }}" class="nav-link {{ request()->routeIs('expirations.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> <span>Expirations</span>
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> <span>Rapports</span>
            </a>
        </nav>
        <div class="footer">
            <p>© 2023 Système Hospitalier</p>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        }
    </script>
    @yield('scripts')
</body>
</html>