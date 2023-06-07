<?php

namespace App\Card;

/**
 *  Concrete class for a suit of Hearts playing card.
 */
class Hearts extends CardBase
{
    /**
     *  Constructor.
     *
     *  @param int $rank  The card rank.
     */
    public function __construct(int $rank)
    {
        parent::__construct($rank);
    }

    /**
     *  Return the HTML entity representation of a card.
     *
     *  @return string  The entity.
     */
    public function getHTMLEntity(): string
    {
        return "&#x1f0b" . $this->getLastHexCharUnicode() . ";";
    }
}
