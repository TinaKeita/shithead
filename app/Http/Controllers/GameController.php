<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameService;

class GameController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function start($players, GameService $gameService)
    {
        $game = $gameService->startGame($players);

        session(['game' => $game]);

        return redirect('/game');
    }

    public function game()
    {
        $game = session('game');

        return view('game', compact('game'));
    }

    public function play(Request $request)
{
    $game = session('game');

    // ❌ ja nav tavs gājiens
    if ($game['currentPlayer'] !== 0) {
        return redirect('/game');
    }

    $cardIndex = $request->card;

    $player = &$game['players'][0];
    $card = $player['hand'][$cardIndex];

    $pile = &$game['pile'];
    $lastCard = end($pile);

    // ja nevar uzlikt → paņem čupu
    if ($lastCard && $card['rank'] < $lastCard['rank']) {
        $player['hand'] = array_merge($player['hand'], $pile);
        $game['pile'] = [];
    } else {
        // liek kārti
        $game['pile'][] = $card;

        // izņem no rokas
        array_splice($player['hand'], $cardIndex, 1);

        // 10 → notīra
        if ($card['value'] === '10') {
            $game['pile'] = [];
        }

        // 6 → reset
        if ($card['value'] === '6') {
            $game['pile'] = [];
        }
    }

    $this->drawCards($game, 0);

    // 👉 pāriet uz nākamo spēlētāju
    $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);

    // 👉 tikai VIENS AI gājiens
    if ($game['currentPlayer'] !== 0) {

        $this->aiMove($game);

        $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);
    }

    session(['game' => $game]);

    return redirect('/game');
}

private function aiMove(&$game)
{
    $playerIndex = $game['currentPlayer'];
    $player = &$game['players'][$playerIndex];

    $pile = &$game['pile']; 
    $lastCard = end($pile);

    $played = false;

    foreach ($player['hand'] as $index => $card) {

        if (!$lastCard || $card['rank'] >= $lastCard['rank']) {

            // liek kārti
            $game['pile'][] = $card;

            // izņem no rokas
            array_splice($player['hand'], $index, 1);

            // 10 → notīra
            if ($card['value'] === '10') {
                $game['pile'] = [];
                session()->flash('message', 'AI noraka čupu ar 10!');
            }

            // 6 → reset
            if ($card['value'] === '6') {
                $game['pile'] = [];
                session()->flash('message', 'AI uzlika 6 (reset)!');
            }

            $played = true;
            break;
        }
    }

    // ja nevar uzlikt → paņem čupu
    if (!$played) {
        $player['hand'] = array_merge($player['hand'], $pile);
        $game['pile'] = [];
        session()->flash('message', 'AI paņēma čupu!');
    }
    $this->drawCards($game, $playerIndex);

}

public function pickup()
{
    $game = session('game');

    if ($game['currentPlayer'] !== 0) {
        return redirect('/game');
    }

    $player = &$game['players'][0];

    // paņem čupu
    $player['hand'] = array_merge($player['hand'], $game['pile']);
    $game['pile'] = [];

    // papildina kārtis
    $this->drawCards($game, 0);

    // nākamais spēlētājs
    $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);

    // 👉 AI gājiens pēc pickup
    if ($game['currentPlayer'] !== 0) {

        $this->aiMove($game);

        $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);
    }

    session(['game' => $game]);

    return redirect('/game');
}


private function drawCards(&$game, $playerIndex)
{
    $player = &$game['players'][$playerIndex];

    while (count($player['hand']) < 3 && count($game['deck']) > 0) {
        $player['hand'][] = array_pop($game['deck']);
    }
}



}
