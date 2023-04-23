<?php

namespace App\Controller;

use App\Card\CardDeck;
use App\Card\CardHand;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    #[Route("/card", name: "card_index")]
    public function card(): Response
    {
        return $this->render('card/index.html.twig');
    }

    #[Route("/card/deck", name: "card_deck")]
    public function cardDeck(SessionInterface $session): Response
    {
        $deck = new CardDeck();

        $session->set("deck", $deck);

        $data = [
            'shuffleText' => "sorterad",
            'deck' => $deck
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "card_deck_shuffle")]
    public function cardDeckShuffle(SessionInterface $session): Response
    {
        $deck = new CardDeck();
        $deck->shuffle(2);

        $session->set("deck", $deck);

        $data = [
            'shuffleText' => "blandad",
            'deck' => $deck
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d{1,2}>?1}", name: "card_deck_draw")]
    public function cardDeckDraw(int $num, SessionInterface $session): Response
    {
        if ($session->has("deck")) {
            $deck = $session->get("deck");
        } else {
            $deck = new CardDeck();
            $deck->shuffle();
        }

        $cardHand = new CardHand("Player", $deck->draw($num));

        $session->set("deck", $deck);

        $data = [
            'count' => $deck->count(),
            'cardHand' => $cardHand
        ];

        return $this->render('card/draw.html.twig', $data);
    }

    #[Route("/card/deck/deal/{players<\d{1,2}>}/{num<\d{1,2}>}", name: "card_deck_deal")]
    public function cardDeckDeal(int $players, int $num, SessionInterface $session): Response
    {
        if ($session->has("deck")) {
            $deck = $session->get("deck");
        } else {
            $deck = new CardDeck();
            $deck->shuffle();
        }

        $playerArr = [];

        for ($i = 1; $i <= $players; $i++) {
            $playerArr[] = new CardHand("Spelare " . $i, $deck->draw($num));
        }

        $session->set("deck", $deck);

        $data = [
            'count' => $deck->count(),
            'playerArr' => $playerArr
        ];

        return $this->render('card/deal.html.twig', $data);
    }

}
