<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Player;

class GameService
{
    /**
     * Pārbauda, vai izvēlēto kārti drīkst likt uz galda.
     * Ja pēdējā kārts ir 6, var likt jebkuru kārti.
     * Citādi jāievēro standarta noteikumi (piemēram, jābūt vienādai vai augstākai).
     *
     * @param Card $cardToPlay
     * @param array $pile
     * @return bool
     */
    public function canPlayCard(Card $cardToPlay, array $pile): bool
    {
        // Ja kaudzē nav nevienas kārts, var likt jebkuru
        if (empty($pile)) {
            return true;
        }

        /** @var Card $topCard */
        $topCard = end($pile);

        // Ja pēdējā kārts ir 6, var likt jebkuru
        if ($topCard->value === '6') {
            return true;
        }

        // Standarta noteikums: jābūt vienādai vērtībai vai augstākai
        return $cardToPlay->value === $topCard->value || $cardToPlay->rank >= $topCard->rank;
    }

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
