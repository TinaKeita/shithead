<?php

namespace App\Models;

class Card
{
    public $value;
    public $rank;
    public $suit;

    public function __construct($value, $suit)
    {
        $this->value = $value;
        $this->suit = $suit;
        $this->rank = $this->getRank($value);
    }

    private function getRank($value)
    {
        return match($value) {
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            'J' => 11,
            'Q' => 12,
            'K' => 13,
            'A' => 14,
        };
    }

    public function getImage()
    {
        return asset("cards/{$this->suit}_{$this->value}.png");
    }
}
