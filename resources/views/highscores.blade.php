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

        @auth
        <div class="panel stats-panel">
            <h2>Tava statistika</h2>
            <p class="stat-value">
                <b>Uzvaras:</b> {{ $currentUserStats['games_won'] }}
                <br>
                <b>Zaudējumi:</b> {{ $currentUserStats['games_lost'] }}
                <br>
                <b>Kopā spēles:</b> {{ $currentUserStats['games_played'] }}
            </p>
            <p class="stat-place">
                <b>Tava vieta:</b> #{{ $currentUserPlace ?? '—' }}
            </p>
        </div>
        @endauth

        <div class="panel leaderboard-panel">
            <h2>Top spēlētāji</h2>
            <div class="table-wrap">
                <table class="highscore-table">
                    <thead>
                        <tr>
                            <th>Vieta</th>
                            <th>Spēlētājs</th>
                            <th>Uzvaras</th>
                            <th>Zaudējumi</th>
                            <th>Spēles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaders as $index => $user)
                            @php
                                $losses = max(0, $user->games_played - $user->games_won);
                            @endphp
                            <tr class="{{ Auth::check() && Auth::id() === $user->id ? 'highlight-row' : '' }}">
                                <td class="rank">{{ $index + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->games_won }}</td>
                                <td>{{ $losses }}</td>
                                <td>{{ $user->games_played }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
