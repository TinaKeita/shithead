<h1>High Score</h1>

@php
    $userWins = \App\Models\Score::whereHas('user', function($q){ $q->where('name', '!=', 'AI'); })->count();
    $aiWins = \App\Models\Score::whereHas('user', function($q){ $q->where('name', 'AI'); })->count();
@endphp

<div style="margin-bottom:16px;">
    <b>Tavas uzvaras:</b> {{ $userWins }}<br>
    <b>Datora uzvaras:</b> {{ $aiWins }}
</div>
<table border="1" cellpadding="6">
    <tr>
        <th>Lietotājs</th>
        <th>Punkti</th>
        <th>Datums</th>
    </tr>
    @foreach($scores as $score)
        <tr>
            <td>{{ $score->user->name }}</td>
            <td>{{ $score->score }}</td>
            <td>{{ $score->created_at->format('Y-m-d H:i') }}</td>
        </tr>
    @endforeach
</table>
<a href="/">Atpakaļ</a>
@auth
    <br><a href="/my-scores">Mana statistika</a>
@endauth
