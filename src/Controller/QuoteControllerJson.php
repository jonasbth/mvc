<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuoteControllerJson
{
    /**
     *  A collection of quotes or puns.
     *
     *  @var array<int, string>
     */
    public static $quotes = [
        "There is no place like 127.0.0.1",
        "There are only 10 kinds of people in this world: those who know binary and those who don’t.",
        "A programmers’s favourite hangout place: Foo Bar.",
        "Why did the programmer quit his job? He didn’t get arrays.",
        "0 is false and 1 is true, right? 1.",
        "Why do Java programmers have to wear glasses? Because they don’t C#.",
        "Real programmers count from 0.",
        "['hip', 'hip']",
        "#lego { display: block; }",
        ".monarch { position: inherit; }",
        ".hobbit { height: 50%;}",
        "#titanic { float: none; bottom: 0; }",
        "Why do programmers always mix up Halloween and Christmas? Because Oct 31 equals Dec 25."
    ];

    #[Route("/api/quote", name: "api_quote")]
    public function jsonQuote(): JsonResponse
    {
        date_default_timezone_set('Europe/Stockholm');

        $nOfQuotes = count(self::$quotes);

        // Construct needed for PHPStan level 7
        if ($nOfQuotes > 1) {
            $quote = self::$quotes[random_int(0, $nOfQuotes - 1)];
        } elseif ($nOfQuotes === 1) {
            $quote = self::$quotes[0];
        } else {
            $quote = "There is no quote";
        }

        $data = [
            'timestamp' => date("H:i:s"),
            'date' => date("Y-m-d"),
            'quote' => $quote
        ];

        $response = new JsonResponse($data);

        $encOptions = $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        $encOptions = $encOptions ^ JSON_HEX_APOS; // Don't escape "'"

        $response->setEncodingOptions($encOptions);
        return $response;
    }
}
