<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Player;

class GameService
{
    public function startGame($playerCount)
    {
        $deck = $this->createDeck();

        $players = [];

        for ($i = 0; $i < $playerCount; $i++) {
            $player = new Player();

            // 3 slēptās
            for ($j = 0; $j < 3; $j++) {
                $player->tableHidden[] = array_pop($deck);
            }

            // 3 redzamās
            for ($j = 0; $j < 3; $j++) {
                $player->tableVisible[] = array_pop($deck);
            }

            // 3 rokā
            for ($j = 0; $j < 3; $j++) {
                $player->hand[] = array_pop($deck);
            }

            $players[] = $player;
        }

        return [
            'players' => $players,
            'deck' => $deck,
            'pile' => [],
            'currentPlayer' => rand(0, $playerCount - 1),
        ];
    }

    private function createDeck()
    {
        $suits = ['hearts', 'diamonds', 'clubs', 'spades'];
        $values = ['2','3','4','5','6','7','8','9','10','J','Q','K','A'];

        $deck = [];

        foreach ($suits as $suit) {
            foreach ($values as $value) {
                $deck[] = new Card($value, $suit);
            }
        }

        shuffle($deck);

        return $deck;
    }
}
