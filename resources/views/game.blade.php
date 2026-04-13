<h1>Spēle</h1>

@if(session('message'))
    <p style="color:red;">
        {{ session('message') }}
    </p>
@endif


<h2>Tava roka:</h2>

@if($game['currentPlayer'] === 0)

    @foreach($game['players'][0]['hand'] as $index => $card)
        <form method="POST" action="/play" style="display:inline;">
            @csrf
            <input type="hidden" name="card" value="{{ $index }}">
            <button type="submit" style="border:none; background:none;">
                <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="80">
            </button>
        </form>
    @endforeach

@else
    <p>Nav tavs gājiens...</p>
@endif

<form method="POST" action="/pickup">
    @csrf
    <button type="submit">Pacelt čupu</button>
</form>


<h2>Tavas redzamās kārtis:</h2>

@foreach($game['players'][0]['tableVisible'] as $card)
    <img src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}" width="80">
@endforeach

<h2>Tavas slēptās kārtis:</h2>

@foreach($game['players'][0]['tableHidden'] as $card)
    <img src="{{ asset('cards/back_dark.png') }}" width="80">
@endforeach

<h2>Čupa:</h2>

<div style="position: relative; width: 120px; height: 160px;">

    @foreach($game['pile'] as $index => $card)
        <img 
            src="{{ asset('cards/' . $card['suit'] . '_' . $card['value'] . '.png') }}"
            width="80"
            style="
                position: absolute;
                top: {{ $index * 2 }}px;
                left: {{ $index * 2 }}px;
            "
        >
    @endforeach

</div>

@if(count($game['pile']) > 0)
    <p>
        Augšējā kārts: 
        {{ end($game['pile'])['value'] }}
    </p>
@endif

