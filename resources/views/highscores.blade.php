<!doctype html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>High Score</title>
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
</head>
<body>
    <div class="page">
        <div class="topbar">
            <div class="brand">
                <h1>High Score</h1>
                <p>Tava statistika</p>
            </div>
            <div class="top-actions">
                <a href="/" class="button secondary">Atpakaļ</a>
                @auth
                    <span class="pill">Ielogojies kā: {{ auth()->user()->name }}</span>
                    <form method="POST" action="/logout" style="display:inline;">
                        @csrf
                        <button type="submit" class="button secondary">Izlogoties</button>
                    </form>
                @else
                    <a href="/login" class="button secondary">Ielogoties</a>
                @endauth
            </div>
        </div>

        @php
            $userGames = 0;
            $userWins = 0;
            if (Auth::check()) {
                $userGames = Auth::user()->games_played;
                $userWins = Auth::user()->games_won;
            }
        @endphp

        @auth
        <div class="panel" style="max-width:400px;margin:0 auto 32px auto;text-align:center;">
            <h2>Tava statistika</h2>
            <p style="font-size:1.3rem;margin:18px 0 0 0;">
                <b>Tu esi uzvarējis {{ $userWins }} no {{ $userGames }} spēlēm.</b>
            </p>
        </div>
        @endauth
    </div>
</body>
</html>
