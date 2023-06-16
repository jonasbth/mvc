<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Spades.
 */
class SpadesTest extends TestCase
{
    private int $rank;
    private CardBase $card;

    /**
     * Set up fixture.
     */
    protected function setUp(): void
    {
        $this->rank = random_int(1, 13);
        $this->card = new Spades($this->rank);
    }

    /**
     * Tear down fixture.
     */
    protected function tearDown(): void
    {
        unset($this->card);
    }

    /**
     * Test construct object and verify class name.
     */
    public function testCreateObject(): void
    {
        $this->assertInstanceOf("\App\Card\Spades", $this->card);
    }

    /**
     * Test getRank() method.
     */
    public function testGetRank(): void
    {
        $res = $this->card->getRank();

        $this->assertSame($this->rank, $res);
    }

    /**
     * Test getSuit() method.
     */
    public function testGetSuit(): void
    {
        $res = $this->card->getSuit();

        $this->assertSame("spades", $res);
    }

    /**
     * Test getHTMLEntity() method.
     */
    public function testGetHTMLEntity(): void
    {
        $res = $this->card->getHTMLEntity();
        $exp = "&#x1f0a" .
            ($this->rank < 12 ? dechex($this->rank) : dechex($this->rank + 1)) .
            ";";

        $this->assertSame($exp, $res);
    }
}
