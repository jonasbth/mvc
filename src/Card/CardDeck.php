<?php

namespace App\Card;

use Countable;
use Iterator;

/**
 * A playing card deck class.
 *
 * @implements Iterator<int, CardBase>
 */
class CardDeck implements Countable, Iterator
{
    /**
     * A deck of cards.
     *
     * @var array<int, CardBase>
     */
    private array $deck = [];

    /**
     * The position in the deck.
     *
     * Used by the Iterator interface.
     */
    private int $position = 0;

    public const SUIT_SIZE = 13;
    public const SUITS = ['Spades', 'Hearts', 'Diamonds', 'Clubs'];

    /**
     * Number of used suits.
     *
     * Set in constructor, <= 4.
     */
    private int $nOfSuits = 4;

    /**
     * Constructor.
     *
     * @param int $nOfSuits  Number of used suits.
     */
    public function __construct(int $nOfSuits = 4)
    {
        $this->nOfSuits = ($nOfSuits <= 4) ? $nOfSuits : 4;
        $this->reset();
    }

    /**
     * Create a new ordered deck.
     */
    public function reset(): void
    {
        $this->deck = [];
        $this->position = 0;

        for ($suitInd = 0; $suitInd < $this->nOfSuits; $suitInd++) {
            for ($i = 1; $i <= self::SUIT_SIZE; $i++) {
                $this->deck[] = new (__NAMESPACE__ . '\\' . self::SUITS[$suitInd])($i);
            }
        }
    }

    /**
     * Return number of cards in the deck.
     *
     * Required method of the Countable interface.
     *
     * @return int  Number of cards.
     */
    public function count(): int
    {
        return count($this->deck);
    }

    /**
     * Shuffle the current deck of cards.
     *
     * @param int $nTimes  Indicates number of passes of the shuffle algorithm. Defaults to 1.
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
     * Pop card(s) off the deck.
     *
     * @param int $num  Number of cards to pop and return from the deck. Defaults to 1.
     * @param int ...$ranks  Optional list of desired card ranks to draw. Used for test.
     *                       If $ranks is present, $num is ignored.
     *
     * @return array<int, CardBase>  An array of at most $num card(s).
     */
    public function draw(int $num = 1, int ...$ranks): array
    {
        if (empty($ranks) || !$ranks[0])
            return array_splice($this->deck, -$num);

        $cards = [];

        foreach ($ranks as $rank) {
            foreach ($this->deck as $pos => $card) {
                if ($card->getRank() === $rank) {
                    $cards[] = array_splice($this->deck, $pos, 1)[0];
                    break;
                }
            }
        }

        return $cards;
    }

    /**
     * Reset the position.
     *
     * Required method of the Iterator interface.
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Return the current card.
     *
     * Required method of the Iterator interface.
     *
     * @return CardBase  A card.
     */
    public function current(): CardBase
    {
        return $this->deck[$this->position];
    }

    /**
     * Return the position in the deck.
     *
     * Required method of the Iterator interface.
     *
     * @return int  The position.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Advance the position in the deck.
     *
     * Required method of the Iterator interface.
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Return whether the position is valid.
     *
     * Required method of the Iterator interface.
     *
     * @return bool  true or false.
     */
    public function valid(): bool
    {
        return isset($this->deck[$this->position]);
    }
}
