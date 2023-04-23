<?php

namespace App\Card;

abstract class CardBase
{
    protected int $rank;   // 1 - 13

    protected function __construct(int $rank)
    {
        $this->rank = $rank;
    }

    /**
     *  Return the HTML entity representation of a card.
     *
     *  @return string  The entity.
     */
    abstract public function getHTMLEntity(): string;

    /**
     *  Return the rank of the card.
     *
     *  @return int  The rank.
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     *  Return the last hexadecimal character of the Unicode representation of a card.
     *
     *  @return string  The hex char.
     */
    protected function getLastHexCharUnicode(): string
    {
        return $this->rank < 12 ? dechex($this->rank) : dechex($this->rank + 1);
    }

    /**
     *  Return the suit of the card, which is derived from the class name.
     *
     *  @return string  The suit.
     */
    public function getSuit(): string
    {
        $namespacedClass = strtolower(get_class($this));
        $splitClass = explode("\\", $namespacedClass);
        return array_pop($splitClass);
    }
}
