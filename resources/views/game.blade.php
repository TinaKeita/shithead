<!doctype html>
<html lang="lv">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shithead — Spēle</title>
    <link rel="stylesheet" href="{{ asset('css/game.css') }}">
</head>
<body>
    <div class="page">
        <div class="topbar">
            <div class="brand">
                <h1>Shithead</h1>
                <p>Spēle ar kārtīm — izvēlies uzvaru, nevis kaudzi.</p>
            </div>
            <div class="top-actions">
                <span class="pill">Spēlētāji: {{ count($game['players']) }}</span>
                <a href="/" class="button secondary">Atpakaļ</a>
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

        @if(session('message'))
            <div class="notification {{ session('message') !== 'Pretinieks paceļ kaudzi!' ? 'message-error' : '' }}">
                {{ session('message') }}
            </div>
        @endif

        @if($game['currentPlayer'] !== 0)
            <script>
                setTimeout(() => {
                    window.location.href = "/game?pretinieks=1";
                }, 2200);
            </script>
        @endif

        <div class="split">
            <div class="board-card panel">
                <div class="title">Kava</div>
                <div class="content">
                    <div class="card-stack">
                        @foreach($game['pile'] as $index => $card)
                            <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
                                 style="top: {{ $index * 2 }}px; left: {{ $index * 2 }}px;">
                        @endforeach
                    </div>
                    @if(count($game['pile']) > 0)
                        <p class="muted">Augšējā kārts: <strong>{{ end($game['pile'])['value'] }}</strong></p>
                        <p class="muted">Kavas kārtis: <strong>{{ count($game['pile']) }}</strong></p>
                    @else
                        <p class="muted">Kava ir tukša</p>
                    @endif
                </div>
            </div>

            <div class="action-panel panel">
                <div class="title">Tava roka</div>
                <div class="content">
                    <p class="muted">Noklikšķini uz kārts, lai spēlētu.</p>
                    <div class="cards-row">
                        @if($game['currentPlayer'] === 0)
                            @php
                                $hand = $game['players'][0]['hand'];
                                $sortedHand = collect($hand)->sortBy(function($c) { return $c['rank'] ?? 0; });
                                $usedHandIndexes = [];
                            @endphp
                            @foreach($sortedHand as $handIndex => $card)
                                @php
                                    // Atrodi nākamo neatkārtoto indeksu sākotnējā rokā
                                    $realIndex = null;
                                    foreach ($hand as $i => $hCard) {
                                        if ($hCard == $card && !in_array($i, $usedHandIndexes, true)) {
                                            $realIndex = $i;
                                            $usedHandIndexes[] = $i;
                                            break;
                                        }
                                    }
                                    $sameCount = $sortedHand->where('value', $card['value'])->count();
                                @endphp
                                <form method="POST" action="/play" class="card-button">
                                    @csrf
                                    <input type="hidden" name="source" value="hand">
                                    <input type="hidden" name="card" value="{{ $realIndex }}">
                                    <input type="hidden" name="play_count" value="1">
                                    <button type="submit" class="card-button" onclick="return openPlayPopup(this, {{ $sameCount }});">
                                        <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="90" alt="{{ $card['value'] }} {{ $card['suit'] }}">
                                    </button>
                                </form>
                            @endforeach
                        @else
                            <p>Gaidi pretinieka gājienu...</p>
                        @endif
                    </div>

                    <div class="action-buttons">
                        <form method="POST" action="/pickup">
                            @csrf
                            <button type="submit" class="button">Pacelt čupu</button>
                        </form>
                        <a href="/" class="button secondary">Jauna spēle</a>
                        {{-- Noņemam pogu "Ņemt redzamo kārti". Redzamās kārtis būs spēlējamas tieši zemāk --}}
                        @if($game['currentPlayer'] === 0 && count($game['players'][0]['hand']) === 0 && count($game['deck']) === 0 && count($game['players'][0]['tableVisible']) === 0 && count($game['players'][0]['tableHidden']) > 0)
                            <form method="POST" action="/pickup-hidden" style="display:inline;">
                                @csrf
                                <button type="submit" class="button">Ņemt slēpto kārti</button>
                            </form>
                        @endif
                    </div>

                    <div class="status-pill">
                        <span>Slēptās</span> {{ count($game['players'][0]['tableHidden']) }}
                    </div>
                    <div class="status-pill">
                        <span>Redzamās</span> {{ count($game['players'][0]['tableVisible']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="panel">
                <h2>Deck</h2>
                <p>{{ count($game['deck']) }} kārtis</p>
            </div>
            <div class="panel">
                <h2>Pretinieks</h2>
                <div class="content">
                    {{-- Pretinieka roka: tikai skaits --}}
                    <div class="cards-row" style="margin-bottom:6px;">
                        <span class="status-pill">Rokā: {{ count($game['players'][1]['hand'] ?? []) }}</span>
                    </div>
                    {{-- Pretinieka redzamās kārtis (viena rinda, max 3) --}}
                    <div class="cards-row" style="margin-bottom:6px;">
                        @foreach(array_slice($game['players'][1]['tableVisible'] ?? [], 0, 3) as $card)
                            <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="90" alt="{{ $card['value'] }}">
                        @endforeach
                    </div>
                    {{-- Pretinieka slēptās kārtis (viena rinda, max 3) --}}
                    <div class="cards-row">
                        @foreach(array_slice($game['players'][1]['tableHidden'] ?? [], 0, 3) as $card)
                            <img src="{{ asset('cards/back_dark.png') }}" width="90" alt="Slēptā kārts">
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="panel">
                <h2>Gājiens</h2>
                <p>{{ $game['currentPlayer'] === 0 ? 'Tavs gājiens' : 'Pretinieks domā...' }}</p>
            </div>
        </div>

        <div class="board-footer">
            <div class="mini-panel">
                <h3>Redzamās kārtis</h3>
                <div class="cards-row">
                    @if($game['currentPlayer'] === 0 && count($game['players'][0]['hand']) === 0 && count($game['deck']) === 0 && count($game['players'][0]['tableVisible']) > 0)
                        @foreach($game['players'][0]['tableVisible'] as $index => $card)
                            <form method="POST" action="/play" class="card-button">
                                @csrf
                                <input type="hidden" name="source" value="visible">
                                <input type="hidden" name="card" value="{{ $index }}">
                                <input type="hidden" name="play_count" value="1">
                                <button type="submit" class="card-button">
                                    <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="84" alt="{{ $card['value'] }}">
                                </button>
                            </form>
                        @endforeach
                    @else
                        @forelse($game['players'][0]['tableVisible'] as $card)
                            <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="84" alt="{{ $card['value'] }}">
                        @empty
                            <p class="muted">Nav redzamu kāršu</p>
                        @endforelse
                    @endif
                </div>
            </div>

            <div class="mini-panel">
                <h3>Slēptās kārtis</h3>
                <div class="cards-row">
                    @forelse($game['players'][0]['tableHidden'] as $index => $card)
                        <form method="POST" action="/play" class="card-button">
                            @csrf
                            <input type="hidden" name="source" value="hidden">
                            <input type="hidden" name="card" value="{{ $index }}">
                            <input type="hidden" name="play_count" value="1">
                            <button type="submit" class="card-button">
                                <img src="{{ asset('cards/back_dark.png') }}" width="84" alt="Slēptā kārts">
                            </button>
                        </form>
                    @empty
                        <p class="muted">Nav slēptu kāršu</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div id="play-popup" class="popup-overlay" aria-hidden="true">
        <div class="modal-card">
            <h2>Cik vienādas kārtis likt?</h2>
            <div id="popup-btns" class="popup-btns"></div>
            <div class="modal-actions">
                <button type="button" class="button secondary" onclick="closePlayPopup()">Atcelt</button>
            </div>
        </div>
    </div>

    @php
        $winner = null;
        foreach ($game['players'] as $i => $p) {
            if (empty($p['hand']) && empty($p['tableVisible']) && empty($p['tableHidden'])) {
                $winner = $i;
                break;
            }
        }
    @endphp

    @if(!is_null($winner))
        <div id="winner-popup" class="popup-overlay show" aria-hidden="false">
            <div class="modal-card">
                <h2>Uzvarētājs</h2>
                <p>{{ $winner === 0 ? 'Tu uzvarēji!' : 'Pretinieks uzvarēja!' }}</p>
                <div class="modal-actions">
                    <a href="/" class="button">Sākt jaunu spēli</a>
                    <a href="/highscores" class="button secondary">Skatīt rezultātu lapu</a>
                </div>
            </div>
        </div>
    @endif

    <script>
        let pendingPlayForm = null;

        function openPlayPopup(button, sameCount) {
            if (sameCount < 2) {
                button.form.querySelector('input[name="play_count"]').value = '1';
                return true;
            }
            pendingPlayForm = button.form;
            const btns = document.getElementById('popup-btns');
            btns.innerHTML = '';
            for (let i = 1; i <= sameCount; i++) {
                const b = document.createElement('button');
                b.type = 'button';
                b.textContent = i === sameCount ? `Visas (${i})` : i;
                b.className = 'button';
                b.style.padding = '10px 18px';
                b.onclick = () => submitPlayCount(i);
                btns.appendChild(b);
            }
            document.getElementById('play-popup').style.display = 'grid';
            return false;
        }

        function closePlayPopup() {
            document.getElementById('play-popup').style.display = 'none';
            pendingPlayForm = null;
        }

        function submitPlayCount(count) {
            if (!pendingPlayForm) {
                return;
            }

            pendingPlayForm.querySelector('input[name="play_count"]').value = String(count);
            const form = pendingPlayForm;
            closePlayPopup();
            form.submit();
        }
    </script>
</body>
</html>
