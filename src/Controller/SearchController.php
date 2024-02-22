<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class SearchController extends AbstractController
{

    private static function getEmojiName(string $emoji) : string {
        switch ($emoji){
            case "🍎":
                return "apple";
            case "🍐":
                return "pear";
            case "🍊":
                return "orange";
            case "🍋":
                return "lemon";
            case "🍌":
                return "banana";
            case "🍉":
                return "watermelon";
            case "🍇":
                return "grape";
            case "🍓":
                return "strawberry";
            case "🫐":
                return "blueberry";
            case "🍈":
                return "melon";
            case "🍒":
                return "cherry";
            case "🍑":
                return "peach";
            case "🍍":
                return "pineapple";
            case "🥝":
                return "kiwi";
            case "🥥":
                return "coconut";
        }
    }



    private static function makeRequest(string $method, string $endpoint, array $querryParams)
    {

        // Create a new Guzzle HTTP client
        $baseUri = 'https://api.discogs.com/';
        $client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => 10, // You can set request timeout (in seconds)
        ]);

        // Set the base URI for the request and any default request options
        $auth = array(
            "key" => "kLzxeHuJCXRyQuBURaEN",
            "secret" => "biDCkuoMbGLeDtQGqCPCtbVqmzPaVmQG"
        );

        // Create the query parameters array
        $querryParams = array_merge($querryParams, $auth);

        // Send a GET request to the specified URI with the query parameters
        $response = $client->request($method, "/database/" . $endpoint, [
            'query' => $querryParams
        ]);

        // Get the status code of the response
        $statusCode = $response->getStatusCode();

        // Get the body of the response
        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }

    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $page = $request->query->get('page');
        $fruit = $request->query->get('fruit');
        $fruit_querry = $request->query->get('q') . " " . SearchController::getEmojiName($fruit);
        $result = SearchController::makeRequest("GET", "search", [
            "q" => $fruit_querry,
            "type" => "release",
            "page" => strval($page),
            "per_page" => "15",
        ]);

        return $this->render('search/search.html.twig', [
            'controller_name' => 'SearchController',
            'query' => $request->query->get('q'),
            'fruit_emoji' => $request->query->get('fruit'),
            'fruit_name' => SearchController::getEmojiName($fruit),
            'page' => $request->query->get('page'),
            "next_page" => $page < $result["pagination"]["pages"] ? strval($page + 1) : strval($page),
            "previous_page" => $page > 1 ? strval($page - 1) : strval($page),
            'results' => $result["results"]
        ]);
    }
}
