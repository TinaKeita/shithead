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

        if ($game['currentPlayer'] !== 0 && request()->has('ai')) {
            $this->aiMove($game);
            $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);
            session(['game' => $game]);
            return redirect('/game');
        }

        return view('game', compact('game'));
    }

    public function play(Request $request)
    {
        $game = session('game');

        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }

        $source = $request->source; // hand / visible
        $index = $request->card;

        $player = &$game['players'][0];
        $pile = &$game['pile'];
        $lastCard = end($pile);

        // 🔥 izvēlamies no kurienes ņemam
        if ($source === 'visible') {
            $card = $player['tableVisible'][$index];
        } else {
            $card = $player['hand'][$index];
        }

        // ❌ noteikumi
        if (
            $lastCard &&
            $card['rank'] < $lastCard['rank'] &&
            $card['value'] !== '6' &&
            $card['value'] !== '10'
        ) {
            return redirect('/game');
        }

        $value = $card['value'];

        $toPlay = [];
        $newHand = $player['hand'];
        $newVisible = $player['tableVisible'];

        // 🔥 spēlē līdz 3 vienādas
        $played = 0;

        if ($source === 'visible') {

            foreach ($newVisible as $i => $c) {
                if ($c['value'] === $value && $played < 3) {
                    $toPlay[] = $c;
                    unset($newVisible[$i]);
                    $played++;
                }
            }

            $player['tableVisible'] = array_values($newVisible);

        } else {

            foreach ($newHand as $i => $c) {
                if ($c['value'] === $value && $played < 3) {
                    $toPlay[] = $c;
                    unset($newHand[$i]);
                    $played++;
                }
            }

            $player['hand'] = array_values($newHand);
        }

        $game['pile'] = array_merge($game['pile'], $toPlay);

        if ($value === '10') {
            $game['pile'] = [];
            session()->flash('message', '10 = čupa norakta!');
        }

        if ($this->checkFourSame($game['pile'])) {
            $game['pile'] = [];
            session()->flash('message', '4 vienādas!');
        }

        $this->drawCards($game, 0);

        $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);

        session(['game' => $game]);

        return redirect('/game');
    }

    public function pickup()
    {
        $game = session('game');

        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }

        $player = &$game['players'][0];

        $player['hand'] = array_merge($player['hand'], $game['pile']);
        $game['pile'] = [];

        $this->drawCards($game, 0);

        $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);

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

        foreach ($player['hand'] as $card) {

            if (
                !$lastCard ||
                $card['rank'] >= $lastCard['rank'] ||
                $card['value'] === '6' ||
                $card['value'] === '10'
            ) {

                $value = $card['value'];

                $toPlay = [];
                $newHand = [];

                foreach ($player['hand'] as $c) {
                    if ($c['value'] === $value && count($toPlay) < 3) {
                        $toPlay[] = $c;
                    } else {
                        $newHand[] = $c;
                    }
                }

                $player['hand'] = $newHand;
                $game['pile'] = array_merge($game['pile'], $toPlay);

                if ($value === '10') {
                    $game['pile'] = [];
                    session()->flash('message', 'AI 10!');
                }

                if ($this->checkFourSame($game['pile'])) {
                    $game['pile'] = [];
                    session()->flash('message', 'AI 4 vienādas!');
                }

                $played = true;
                break;
            }
        }

        if (!$played) {
            $player['hand'] = array_merge($player['hand'], $pile);
            $game['pile'] = [];
        }

        $this->drawCards($game, $playerIndex);
    }

    private function drawCards(&$game, $playerIndex)
    {
        $player = &$game['players'][$playerIndex];

        while (count($player['hand']) < 3 && count($game['deck']) > 0) {
            $player['hand'][] = array_pop($game['deck']);
        }
    }

    private function checkFourSame($pile)
    {
        if (count($pile) < 4) return false;

        $last4 = array_slice($pile, -4);
        $value = $last4[0]['value'];

        foreach ($last4 as $c) {
            if ($c['value'] !== $value) return false;
        }

        return true;
    }
}
