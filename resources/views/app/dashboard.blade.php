<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f7fafc;
            min-height: 100vh;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px 0;
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-name {
            color: #2d3748;
            font-weight: 600;
        }

        .btn-logout {
            padding: 10px 20px;
            background: #f56565;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #e53e3e;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome-card h1 {
            font-size: 32px;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #718096;
            font-size: 16px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .info-card h2 {
            font-size: 20px;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            min-width: 120px;
        }

        .info-value {
            color: #2d3748;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
            border: 1px solid #68d391;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">DEVOIR MADAME BELINGA</div>
            <div class="navbar-user">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-logout">Déconnexion</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="welcome-card">
            <h1>Bienvenue, {{ Auth::user()->name }} ! 👋</h1>
            <p>Vous êtes maintenant connecté à votre espace personnel.</p>
        </div>

        <div class="info-card">
            <h2>Informations du compte</h2>
            <div class="info-item">
                <span class="info-label">Nom :</span>
                <span class="info-value">{{ Auth::user()->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email :</span>
                <span class="info-value">{{ Auth::user()->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Membre depuis :</span>
                <span class="info-value">{{ Auth::user()->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</body>
</html>