<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class Hearts.
 */
class HeartsTest extends TestCase
{
    private int $rank;
    private CardBase $card;

    /**
     * Set up fixture.
     */
    protected function setUp(): void
    {
        $this->rank = random_int(1, 13);
        $this->card = new Hearts($this->rank);
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
    public function testCreateObject()
    {
        $this->assertInstanceOf("\App\Card\Hearts", $this->card);
    }

    /**
     * Test getRank() method.
     */
    public function testGetRank()
    {
        $res = $this->card->getRank();

        $this->assertSame($this->rank, $res);
    }

    /**
     * Test getSuit() method.
     */
    public function testGetSuit()
    {
        $res = $this->card->getSuit();

        $this->assertSame("hearts", $res);
    }

    /**
     * Test getHTMLEntity() method.
     */
    public function testGetHTMLEntity()
    {
        $res = $this->card->getHTMLEntity();
        $exp = "&#x1f0b" .
            ($this->rank < 12 ? dechex($this->rank) : dechex($this->rank + 1)) .
            ";";

        $this->assertSame($exp, $res);
    }
}