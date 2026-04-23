<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pieslēgties - Shithead</title>
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
    <style>
        :root {
            --bg: #0f0f23;
            --bg-secondary: #1a1a2e;
            --accent: #00d4ff;
            --text: #ffffff;
            --muted: #94a3b8;
            --border: rgba(148, 163, 184, 0.18);
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg) 0%, var(--bg-secondary) 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            font-size: 2rem;
            margin: 0 0 0.5rem;
            background: linear-gradient(45deg, var(--accent), #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-header p {
            color: var(--muted);
            margin: 0;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text);
        }

        .form-group input {
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            color: var(--text);
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(0, 212, 255, 0.2);
        }

        .auth-button {
            padding: 0.75rem;
            background: linear-gradient(45deg, var(--accent), #00ff88);
            border: none;
            border-radius: 8px;
            color: var(--bg);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .auth-button:hover {
            transform: translateY(-2px);
        }

        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .auth-links a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-links a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Shithead</h1>
            <p>Pieslēdzies savam kontam</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">E-pasts</label>
                <input type="email" name="email" id="email" placeholder="Ievadi e-pastu" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label for="password">Parole</label>
                <input type="password" name="password" id="password" placeholder="Ievadi paroli" required>
            </div>

            <button type="submit" class="auth-button">Ieiet</button>
        </form>

        <div class="auth-links">
            <p>Nav konta? <a href="/register">Reģistrēties</a></p>
        </div>
    </div>
</body>
</html>
