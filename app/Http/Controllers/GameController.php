<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameService;

class GameController extends Controller
{
    public function pickupVisible()
    {
        $game = session('game');
        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }
        $player = &$game['players'][0];
        if (count($player['hand']) === 0 && count($game['deck']) === 0 && count($player['tableVisible']) > 0) {
            $card = array_shift($player['tableVisible']);
            $player['hand'][] = $card;
            session(['game' => $game]);
        }
        return redirect('/game');
    }

    public function pickupHidden()
    {
        $game = session('game');
        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }
        $player = &$game['players'][0];
        if (count($player['hand']) === 0 && count($game['deck']) === 0 && count($player['tableVisible']) === 0 && count($player['tableHidden']) > 0) {
            $card = array_shift($player['tableHidden']);
            $player['hand'][] = $card;
            session(['game' => $game]);
        }
        return redirect('/game');
    }

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

        if (!isset($game['reversed'])) {
            $game['reversed'] = false;
        }

        if ($game['currentPlayer'] !== 0 && request()->has('pretinieks')) {
            $this->pretinieksMove($game);
            $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);
            session(['game' => $game]);
            return redirect('/game');
        }

        return view('game', compact('game'));
    }

    public function play(Request $request)
    {
        $game = session('game');

        if (!isset($game['reversed'])) {
            $game['reversed'] = false;
        }

        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }

        $source = $request->input('source', 'hand'); // hand / visible / hidden
        $index = $request->card;

        $player = &$game['players'][0];
        $pile = &$game['pile'];
        $lastCard = end($pile);


        // 🔥 izvēlamies no kurienes ņemam
        if ($source === 'visible') {
            if (!isset($player['tableVisible'][$index])) {
                return redirect('/game');
            }
            $card = $player['tableVisible'][$index];
        } elseif ($source === 'hidden') {
            if (!isset($player['tableHidden'][$index])) {
                return redirect('/game');
            }
            $card = $player['tableHidden'][$index];
        } else {
            if (!isset($player['hand'][$index])) {
                return redirect('/game');
            }
            $card = $player['hand'][$index];
        }



        // Ja spēlē hidden kārti, pārbaudi derīgumu pēc atklāšanas
        if ($source === 'hidden') {
            $valid = true;
            if ($lastCard) {
                $isSpecial = in_array($card['value'], ['6', '10']);
                if ($game['reversed']) {
                    if (!$isSpecial && $card['rank'] > $lastCard['rank']) {
                        $valid = false;
                    }
                } else {
                    if ($lastCard['value'] !== '6' && !$isSpecial && $card['rank'] < $lastCard['rank']) {
                        $valid = false;
                    }
                }
            }
            if (!$valid) {
                if (isset($player['tableHidden'][$index])) {
                    unset($player['tableHidden'][$index]);
                    $player['tableHidden'] = array_values($player['tableHidden']);
                }
                // Pievieno hidden kārti čupai un paceļ visu čupu
                $game['pile'][] = $card;
                $player['hand'] = array_merge($player['hand'], $game['pile']);
                $game['pile'] = [];
                $game['reversed'] = false;
                session()->flash('message', 'Nevar izmantot šo kārti!');
                session(['game' => $game]);
                return redirect('/game');
            }
        } else {
            if ($lastCard) {
                $isSpecial = in_array($card['value'], ['6', '10']);
                if ($game['reversed']) {
                    if (!$isSpecial && $card['rank'] > $lastCard['rank']) {
                        session()->flash('message', 'Nevar izmantot šo kārti!');
                        return redirect('/game');
                    }
                } else {
                    if ($lastCard['value'] !== '6' && !$isSpecial && $card['rank'] < $lastCard['rank']) {
                        session()->flash('message', 'Nevar izmantot šo kārti!');
                        return redirect('/game');
                    }
                }
            }
        }

        $value = $card['value'];
        $requestedCount = (int) $request->input('play_count', 0);
        $sameInHand = collect($player['hand'])->where('value', $value)->count();
        $sameInVisible = collect($player['tableVisible'])->where('value', $value)->count();
        $maxSame = 1;
        if ($source === 'hand') {
            $maxSame = $sameInHand;
        } elseif ($source === 'visible') {
            $maxSame = $sameInVisible;
        }
        if ($requestedCount > 0) {
            $maxCardsToPlay = max(1, min($maxSame, $requestedCount));
        } else {
            $playAll = $request->boolean('play_all', true);
            $maxCardsToPlay = $playAll ? $maxSame : 1;
        }


        $toPlay = [];
        $newHand = $player['hand'];
        $newVisible = $player['tableVisible'];
        $newHidden = $player['tableHidden'];

        // 🔥 spēlē līdz 3 vienādas
        $played = 0;

        if ($source === 'visible') {
            foreach ($newVisible as $i => $c) {
                if ($c['value'] === $value && $played < $maxCardsToPlay) {
                    $toPlay[] = $c;
                    unset($newVisible[$i]);
                    $played++;
                }
            }
            $player['tableVisible'] = array_values($newVisible);
        } elseif ($source === 'hidden') {
            // hidden kārts vienmēr viena, pēc klikšķa uzreiz izņem
            if (isset($newHidden[$index])) {
                $toPlay[] = $newHidden[$index];
                unset($newHidden[$index]);
            }
            $player['tableHidden'] = array_values($newHidden);
        } else {
            foreach ($newHand as $i => $c) {
                if ($c['value'] === $value && $played < $maxCardsToPlay) {
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
            $game['reversed'] = false;
            session()->flash('message', '10 = čupa norakta!');
        }

        if ($value === '6') {
            $game['reversed'] = true;
        }

        if ($this->checkFourSame($game['pile'])) {
            $game['pile'] = [];
            $game['reversed'] = false;
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

        if (!isset($game['reversed'])) {
            $game['reversed'] = false;
        }

        if ($game['currentPlayer'] !== 0) {
            return redirect('/game');
        }

        $player = &$game['players'][0];

        $player['hand'] = array_merge($player['hand'], $game['pile']);
        $game['pile'] = [];
        $game['reversed'] = false;

        $this->drawCards($game, 0);

        $game['currentPlayer'] = ($game['currentPlayer'] + 1) % count($game['players']);

        session(['game' => $game]);

        return redirect('/game');
    }

    private function pretinieksMove(&$game)
    {
        if (!isset($game['reversed'])) {
            $game['reversed'] = false;
        }

        $playerIndex = $game['currentPlayer'];
        $player = &$game['players'][$playerIndex];

        $pile = &$game['pile'];
        $lastCard = end($pile);

        $played = false;

        foreach ($player['hand'] as $card) {

            if (
                !$lastCard ||
                ($game['reversed'] ? $card['rank'] <= $lastCard['rank'] : $card['rank'] >= $lastCard['rank']) ||
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
                    $game['reversed'] = false;
                    session()->flash('message', 'Pretinieks 10!');
                }

                if ($value === '6') {
                    $game['reversed'] = true;
                }

                if ($this->checkFourSame($game['pile'])) {
                    $game['pile'] = [];
                    $game['reversed'] = false;
                    session()->flash('message', 'Pretinieks 4 vienādas!');
                }

                $played = true;
                break;
            }
        }


        if (!$played) {
            $player['hand'] = array_merge($player['hand'], $pile);
            $game['pile'] = [];
            $game['reversed'] = false;
            session()->flash('message', 'Pretinieks paceļ kaudzi!');
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
