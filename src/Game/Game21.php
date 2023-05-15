<?php

namespace App\Game;

use App\Card\CardDeck;
use App\Card\CardHand;

class Game21
{
    private CardDeck $deck;
    private CardHand $playerHand;
    private CardHand $bankHand;
    private int $round;
    private int $playerWins;
    private int $bankWins;
    private bool $playersTurn;

    /**
     *  An array of two numbers indicating the probability in per cent that
     *  the player´s next card won´t make the hand worth more than 21.
     *
     *  If there is only one valid probability, -1 is given for index position 1.
     *
     *  @var array<int, int>
     */
    private array $playerChance;

    /**
     *  An int array [0..13] of number of cards of each rank.
     *
     *  Keep total card count in array position 0.
     *
     *  @var array<int, int>
     */
    private array $cardCount;

    /**
     *  Show the chance to the user or not.
     */
    private bool $playerUseChance;

    /**
     *  Let the bank use the chance or not.
     */
    private bool $bankUseChance;

    public function __construct()
    {
    }

    /**
     *  Return number of cards left.
     *
     *  @return int  Number of cards.
     */
    public function cardsLeft(): int
    {
        return $this->deck->count();
    }

    /**
     *  Return player name.
     *
     *  @return string  The name.
     */
    public function playerName(): string
    {
        return $this->playerHand->getName();
    }

    /**
     *  Return bank name.
     *
     *  @return string  The name.
     */
    public function bankName(): string
    {
        return $this->bankHand->getName();
    }

    /**
     *  Return player hand.
     *
     *  @return CardHand  The hand.
     */
    public function playerHand(): CardHand
    {
        return $this->playerHand;
    }

    /**
     *  Return bank hand.
     *
     *  @return CardHand  The hand.
     */
    public function bankHand(): CardHand
    {
        return $this->bankHand;
    }

    /**
     *  Return the round.
     *
     *  @return int  The round.
     */
    public function round(): int
    {
        return $this->round;
    }

    /**
     *  Return number of player wins.
     *
     *  @return int  The number.
     */
    public function playerWins(): int
    {
        return $this->playerWins;
    }

    /**
     *  Return number of bank wins.
     *
     *  @return int  The number.
     */
    public function bankWins(): int
    {
        return $this->bankWins;
    }

    /**
     *  Return whether it is the player´s turn.
     *
     *  @return bool  true if it is the player's turn.
     */
    public function playersTurn(): bool
    {
        return $this->playersTurn;
    }

    /**
     *  Return whether the player can use computed chance.
     *
     *  @return bool  true if the player can use chance.
     */
    public function playerUseChance(): bool
    {
        return $this->playerUseChance;
    }

    /**
     *  Return whether the bank can use computed chance.
     *
     *  @return bool  true if the bank can use chance.
     */
    public function bankUseChance(): bool
    {
        return $this->bankUseChance;
    }

    /**
     *  Return an array indicating the chance of success for the player´s next draw.
     *
     *  Two percentages are returned if there is an ace counting as 14, which does not
     *  contribute to a hand above 21, otherwise -1 is returned for index position 1.
     *
     *  @return array<int, int>  The percentage array.
     */
    public function playerChance(): array
    {
        return $this->playerChance;
    }

    /**
     *  Calculate and set the $playerChance array based on the current hand of the player.
     *
     */
    private function setPlayerChance(): void
    {
        $points = $this->playerHand->getPoints21();

        $this->playerChance[0] = $this->calcSuccessFactor($points[0]);

        if (count($points) > 1) {
            $this->playerChance[1] = $this->calcSuccessFactor($points[1]);
        } else {
            $this->playerChance[1] = -1;
        }
    }

    /**
     *  Return an array of number of cards left of each rank.
     *
     *  The total card count is in array position 0.
     *
     *  @return array<int, int>  The count array.
     */
    public function cardCount(): array
    {
        return $this->cardCount;
    }

    /**
     *  Return a string describing the value of the player's hand.
     *
     *  @return string  The description.
     */
    public function playerWorth(): string
    {
        $points = $this->playerHand->getPoints21();

        if ($this->playersTurn) {
            return "$points[0]" . (count($points) > 1 ? " eller " . $points[1] : "");
        }

        return "{$this->playerHand->getMaxPoints21()}";
    }

    /**
     *  Draw cards off the deck.
     *
     *  @param int $numCards  Number of cards to draw.
     *
     *  @return array<int, \App\Card\CardBase>  An array of cards.
     */
    private function drawCards(int $numCards = 1): array
    {
        $cards = $this->deck->draw($numCards);

        foreach ($cards as $card) {
            $this->cardCount[$card->getRank()]--;
            $this->cardCount[0]--;
        }

        return $cards;
    }

    /**
     *  Calculate the probability of success for drawing a card off the current deck.
     *
     *  @param int $handWorth  The worth of the hand.
     *
     *  @return int  The probability of success in per cent.
     */
    private function calcSuccessFactor(int $handWorth): int
    {
        if ($this->cardCount[0] === 0) {
            return 0;
        }

        $count = 0;

        for ($i = 1; $i < 14; $i++) {
            if ($handWorth + $i > 21) {
                break;
            }

            $count += $this->cardCount[$i];
        }

        return (int)round(100 * $count / $this->cardCount[0]);
    }

    /**
     *  Initiate a new game of "21".
     *
     *  @param string $playerName  The player´s name.
     */
    public function newGame(string $playerName, bool $playerChance, bool $bankChance): void
    {
        $this->round = 1;
        $this->playerWins = 0;
        $this->bankWins = 0;
        $this->playersTurn = true;
        $this->playerUseChance = $playerChance;
        $this->bankUseChance = $bankChance;

        $this->deck = new CardDeck();
        $this->deck->shuffle(2);
        $this->cardCount = array_fill(0, 14, 4);
        $this->cardCount[0] = 52;

        $this->playerHand = new CardHand($playerName, $this->drawCards(1));
        $this->setPlayerChance();
        $this->bankHand = new CardHand("Banken");
    }

    /**
     *  Initiate a new round.
     *
     */
    public function nextRound(): void
    {
        $this->round++;
        $this->playerHand->reset();
        $this->playerHand->addCards($this->drawCards(1));
        $this->setPlayerChance();
        $this->playersTurn = true;
    }

    /**
     *  Let the player draw one card.
     *
     */
    public function playerNewCard(): void
    {
        $this->playerHand->addCards($this->drawCards(1));
        $points = $this->playerHand->getPoints21();

        if ($points[0] > 21) {
            $this->bankWins++;
        } else {
            $this->setPlayerChance();
        }
    }

    /**
     *  It's the banks turn to play a round.
     *
     */
    public function bankTurn(): void
    {
        $this->playersTurn = false;

        if ($this->bankUseChance) {
            $this->bankPlayChance();
        } else {
            $this->bankPlay();
        }

        $bankPoints = $this->bankHand->getMaxPoints21();

        if ($bankPoints <= 21 && $bankPoints >= $this->playerHand->getMaxPoints21()) {
            $this->bankWins++;
        } else {
            $this->playerWins++;
        }
    }

    /**
     *  Let the bank play one round of "21", using probabilities of the next drawn card.
     *
     */
    private function bankPlayChance(): void
    {
        $this->bankHand->reset();

        do {
            $this->bankHand->addCards($this->drawCards(1));
            $points = $this->bankHand->getPoints21();

            if (count($points) === 1) {
                $chanceHand = $this->calcSuccessFactor($points[0]);
                $chanceLow = 40;
            } else {
                $chanceHand = $this->calcSuccessFactor($points[1]);
                $chanceLow = 30;
            }

        } while ($chanceHand >= $chanceLow && $this->cardsLeft() > 0);
    }

    /**
     *  Let the bank play one round of "21".
     *
     */
    private function bankPlay(): void
    {
        $this->bankHand->reset();
        $this->bankHand->addCards($this->drawCards(2));
        $maxPoints = $this->bankHand->getMaxPoints21();

        while ($maxPoints < 17 && $this->cardsLeft() > 0) {
            $this->bankHand->addCards($this->drawCards(1));
            $maxPoints = $this->bankHand->getMaxPoints21();
        }
    }
}
