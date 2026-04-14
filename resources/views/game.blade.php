<h1>Spēle</h1>

@if(session('message'))
    <p style="color:red;">{{ session('message') }}</p>
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

        <form method="POST" action="/play" style="display:inline;">
            @csrf
            <input type="hidden" name="card" value="{{ $index }}">

            <button style="border:none; background:none;">
                <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
                     width="80">
            </button>
        </form>

    @endforeach

@else
    <p>Nav tavs gājiens</p>
@endif

@if($game['currentPlayer'] === 0)
<form method="POST" action="/pickup">
    @csrf
    <button>Pacelt čupu</button>
</form>
@endif

<h2>Čupa:</h2>

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
