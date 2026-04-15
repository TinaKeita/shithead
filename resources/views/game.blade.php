<h1>Spēle</h1>


@if(session('message'))
    @if(session('message') === 'AI paceļ kaudzi!')
        <div id="ai-popup" style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:2000;">
            <div style="background:#fff; border-radius:10px; padding:24px 32px; box-shadow:0 12px 30px rgba(0,0,0,0.2); text-align:center;">
                <h2 style="margin-bottom:16px;">AI paceļ kārtis!</h2>
                <button onclick="document.getElementById('ai-popup').style.display='none'" style="padding:10px 22px; border-radius:6px; background:#2aa12a; color:#fff; border:none; font-size:16px; cursor:pointer;">Labi</button>
            </div>
        </div>
    @else
        <p style="color:red;">{{ session('message') }}</p>
    @endif
@endif

<h3>Deck: {{ count($game['deck']) }}</h3>
<h3>AI kārtis: {{ count($game['players'][1]['hand'] ?? []) }}</h3>

@if($game['currentPlayer'] !== 0)
    <h2>AI domā...</h2>

    <script>
        setTimeout(() => {
            window.location.href = "/game?ai=1";
        }, 3000);
    </script>
@endif

<h2>Tava roka:</h2>

@if($game['currentPlayer'] === 0)

    @foreach($game['players'][0]['hand'] as $index => $card)

        @php
            $sameCount = collect($game['players'][0]['hand'])->where('value', $card['value'])->count();
        @endphp

        <form method="POST" action="/play" style="display:inline;">
            @csrf
            <input type="hidden" name="card" value="{{ $index }}">
            <input type="hidden" name="play_count" value="1">

            <button style="border:none; background:none;" onclick="return openPlayPopup(this, {{ $sameCount }});">
                <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
                     width="80">
            </button>
        </form>

    @endforeach

@else
    <p>Nav tavs gājiens</p>
@endif


@if($game['currentPlayer'] === 0)
    @if(count($game['deck']) > 0 || count($game['players'][0]['hand']) > 0)
        <form method="POST" action="/pickup">
            @csrf
            <button>Pacelt čupu</button>
        </form>
    @else
        @if(count($game['players'][0]['tableVisible']) > 0)
            <h3>Redzamās kārtis:</h3>
            @foreach($game['players'][0]['tableVisible'] as $index => $card)
                <form method="POST" action="/play" style="display:inline;">
                    @csrf
                    <input type="hidden" name="card" value="{{ $index }}">
                    <input type="hidden" name="play_count" value="1">
                    <input type="hidden" name="source" value="visible">
                    <button style="border:none; background:none;">
                        <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="80">
                    </button>
                </form>
            @endforeach
        @endif
        @if(count($game['players'][0]['tableVisible']) === 0 && count($game['players'][0]['tableHidden']) > 0)
            <h3>Slēptās kārtis:</h3>
            @foreach($game['players'][0]['tableHidden'] as $index => $card)
                <form method="POST" action="/play" style="display:inline;">
                    @csrf
                    <input type="hidden" name="card" value="{{ $index }}">
                    <input type="hidden" name="play_count" value="1">
                    <input type="hidden" name="source" value="hidden">
                    <button style="border:none; background:none;">
                        <img src="{{ asset('cards/back_dark.png') }}" width="80">
                    </button>
                </form>
            @endforeach
        @endif
    @endif
@endif

<script>
    let pendingPlayForm = null;

    function openPlayPopup(button, sameCount) {
        if (sameCount < 2) {
            button.form.querySelector('input[name="play_count"]').value = '1';
            return true;
        }
        pendingPlayForm = button.form;
        // Dinamiski izveido pogas līdz sameCount
        const btns = document.getElementById('popup-btns');
        btns.innerHTML = '';
        for (let i = 1; i <= sameCount; i++) {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = i === sameCount ? `Visas (${i})` : i;
            b.style = 'padding:8px 14px; border:1px solid #1f7a1f; background:#2aa12a; color:#fff; border-radius:6px; cursor:pointer; margin-right:6px;';
            b.onclick = () => submitPlayCount(i);
            btns.appendChild(b);
        }
        document.getElementById('play-popup').style.display = 'flex';
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

<div id="play-popup" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:1000;">
    <div style="background:#fff; border-radius:10px; padding:18px; width:min(320px, 90vw); box-shadow:0 12px 30px rgba(0,0,0,0.2);">
        <p style="margin:0 0 14px 0; font-size:16px;">Cik vienādas kārtis likt?</p>
        <div id="popup-btns" style="display:flex; gap:10px; justify-content:flex-end;"></div>
        <div style="margin-top:10px; text-align:right;">
            <button type="button" onclick="closePlayPopup()" style="padding:8px 14px; border:1px solid #999; background:#fff; border-radius:6px; cursor:pointer;">Atcelt</button>
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
    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.45); display:flex; align-items:center; justify-content:center; z-index:3000;">
        <div style="background:#fff; border-radius:10px; padding:32px 40px; box-shadow:0 12px 30px rgba(0,0,0,0.2); text-align:center;">
            <h2>Uzvarētājs: {{ $winner === 0 ? 'Tu' : 'AI' }}</h2>
            <a href="/" style="display:inline-block; margin-top:18px; padding:10px 22px; border-radius:6px; background:#2aa12a; color:#fff; border:none; font-size:16px; text-decoration:none;">Jauna spēle</a>
        </div>
    </div>
@endif

<h2>Kava:</h2>

<div style="position: relative; width:120px; height:160px;">
    @foreach($game['pile'] as $index => $card)
        <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
             width="80"
             style="position:absolute; top:{{ $index*2 }}px; left:{{ $index*2 }}px;">
    @endforeach
</div>

@if(count($game['pile']) > 0)
    <p>Augšējā kārts: {{ end($game['pile'])['value'] }}</p>
@endif

<h2>Slēptās kārtis:</h2>

<div style="position: relative; width:120px; height:160px;">
    @foreach($game['players'][0]['tableHidden'] as $index => $card)
        <img src="{{ asset('cards/back_dark.png') }}"
             width="80"
             style="position:absolute; top:{{ $index*2 }}px; left:{{ $index*2 }}px;">
    @endforeach
</div>

<h2>Visible kārtis:</h2>

<div>
    @foreach($game['players'][0]['tableVisible'] as $card)
        <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
             width="70">
    @endforeach
</div>
