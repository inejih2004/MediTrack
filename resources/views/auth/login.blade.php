<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Connexion - MediTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f5f9fc; font-family: 'Cairo', sans-serif; }
        .login-box { max-width: 420px; margin: 80px auto; background-color: white; border-radius: 16px; box-shadow: 0 0 15px rgba(0,0,0,0.1); padding: 40px 30px; }
        .form-label { font-weight: bold; }
    </style>
</head>
<body>
<div class="login-box">
    <h2 class="text-center mb-4">🔐 Connexion</h2>
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </div>
        <div class="text-center">
            <a href="" class="text-decoration-none">Mot de passe oublié ?</a>
        </div>
    </form>
</div>
</body>
</html>