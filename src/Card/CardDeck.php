<?php

namespace App\Card;

use Countable;
use Iterator;

class CardDeck implements Countable, Iterator
{
    private array $deck = [];
    private int $position = 0;

    public const SUIT_SIZE = 13;
    public const SUITS = ['Spades', 'Hearts', 'Diamonds', 'Clubs'];

    public function __construct()
    {
        $this->reset();
    }

    /**
     *  Create a new ordered deck.
     */
    public function reset(): void
    {
        $this->deck = [];
        $this->position = 0;

        foreach (self::SUITS as $suit) {
            for ($i = 1; $i <= self::SUIT_SIZE; $i++) {
                $this->deck[] = new (__NAMESPACE__ . '\\' . $suit)($i);
            }
        }
    }

    /**
     *  Return number of cards in the deck.
     *
     *  Required method of the Countable interface.
     *
     *  @return int  Number of cards.
     */
    public function count(): int
    {
        return count($this->deck);
    }

    /**
     *  Shuffle the current deck of cards.
     *
     *  @param int $nTimes  Indicates number of passes of the shuffle algorithm. Defaults to 1.
     */
    public function shuffle(int $nTimes = 1): void
    {
        $count = $this->count();

        for ($i = 1; $i <= $nTimes; $i++) {
            foreach ($this->deck as $pos => &$card) {
                $newPos = random_int(0, $count - 1);

                if ($newPos !== $pos) {
                    $temp = $this->deck[$newPos];
                    $this->deck[$newPos] = $card;
                    $this->deck[$pos] = $temp;
                }
            }
        }
    }

    /**
     *  Pop card(s) off the deck.
     *
     *  @param int $num  Number of cards to pop and return from the deck. Defaults to 1.
     *
     *  @return array  An array of at most $num card(s).
     */
    public function draw(int $num = 1): array
    {
        return array_splice($this->deck, -$num);
    }

    /**
     *  Required methods below of the Iterator interface.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->deck[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->deck[$this->position]);
    }
}
