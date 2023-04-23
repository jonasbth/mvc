<?php

namespace App\Card;

class Spades extends CardBase
{
    public function __construct(int $rank)
    {
        parent::__construct($rank);
    }

    public function getHTMLEntity(): string
    {
        return "&#x1f0a" . $this->getLastHexCharUnicode() . ";";
    }
}
