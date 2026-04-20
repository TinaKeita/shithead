
<!doctype html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shithead — Highscore</title>
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
</head>
<body>
<div class="page">
    <div class="topbar">
        <div class="brand">
            <h1>High Score</h1>
        </div>
        <div class="top-actions">
            <a href="/game" class="button secondary">Uz spēli</a>
            <a href="/" class="button secondary">Sākums</a>
            <a href="/highscores" class="button secondary">Highscore</a>
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
    $playerCount = request('player_count');
    $users = \App\Models\User::where('name', '!=', 'AI')->get();
    $userStats = $users->map(function($user) use ($playerCount) {
        $games = \App\Models\Score::where('user_id', $user->id)
            ->when($playerCount, function($q) use ($playerCount) { return $q->where('player_count', $playerCount); })
            ->count();
        $wins = \App\Models\Score::where('user_id', $user->id)
            ->when($playerCount, function($q) use ($playerCount) { return $q->where('player_count', $playerCount); })
            ->where('score', 1)
            ->count();
        return $games > 0 ? [
            'name' => $user->name,
            'games' => $games,
            'wins' => $wins,
        ] : null;
    })->filter()->sortByDesc('wins');
@endphp

    <div class="panel" style="margin-bottom:16px; max-width: 600px;">
        <form method="get" style="margin-bottom: 12px;">
            <label for="player_count">Filtrēt pēc spēlētāju skaita:</label>
            <select name="player_count" id="player_count" onchange="this.form.submit()">
                <option value="" @if(!$playerCount)selected @endif>Visi</option>
                <option value="2" @if($playerCount==2)selected @endif>2 spēlētāji</option>
                <option value="3" @if($playerCount==3)selected @endif>3 spēlētāji</option>
            </select>
        </form>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background: var(--panel-soft);">
                    <th style="padding:8px 12px; border-bottom:1px solid var(--border);">Lietotājs</th>
                    <th style="padding:8px 12px; border-bottom:1px solid var(--border);">Uzvaras</th>
                    <th style="padding:8px 12px; border-bottom:1px solid var(--border);">Spēles</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userStats as $stat)
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:8px 12px;">{{ $stat['name'] }}</td>
                        <td style="padding:8px 12px;">{{ $stat['wins'] }}</td>
                        <td style="padding:8px 12px;">{{ $stat['games'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="padding:12px; text-align:center; color:var(--muted);">Nav neviena spēlētāja ar spēlēm šajā filtrā.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Noņemta spēļu rezultātu tabula, atstāta tikai lietotāju statistika --}}
</div>
</body>
</html>
