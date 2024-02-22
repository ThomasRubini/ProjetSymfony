<?php

namespace App\Controller;

use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
Use App\Utils;

class SearchController extends AbstractController
{

    private static function addFruitToAlbums(array $releases, string $fruit, EntityManagerInterface $entityManager): void
    {        
        foreach ($releases as $release) {
            $id = $release["id"];
            $album = $entityManager->getRepository(Album::class)->findBy(
                ['album_id' => $id]
            );
            if (count($album) == 0) {
                $album = new Album();
                $album->setAlbumId($id);
                //https://symfony.com/doc/current/doctrine.html#persisting-objects-to-the-database
            } else {
                $album = $album[0];
            }
            $album_fruit = $album->getFruits();
            if (!str_contains($album_fruit, $fruit)) {
                $album->setFruits($album_fruit . $fruit);
                //https://symfony.com/doc/current/doctrine.html#persisting-objects-to-the-database
            }
            $entityManager->persist($album);
            $entityManager->flush();
        }
    }

    private static function getEmojiName(string | null $emoji) : string | null {
        return match ($emoji) {
            "🍎" => "apple",
            "🍐" => "pear",
            "🍊" => "orange",
            "🍋" => "lemon",
            "🍌" => "banana",
            "🍉" => "watermelon",
            "🍇" => "grape",
            "🍓" => "strawberry",
            "🫐" => "blueberry",
            "🍈" => "melon",
            "🍒" => "cherry",
            "🍑" => "peach",
            "🍍" => "pineapple",
            "🥝" => "kiwi",
            "🥥" => "coconut",
            default => null,
        };
    }

    #[Route('/search', name: 'app_search')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $page = $request->query->get('page');
        $fruit = $request->query->get('fruit');
        $user_query = $request->query->get('q');

        $fruit_emoji = SearchController::getEmojiName($fruit);
        if ($fruit_emoji == null) {
            // We do not support requests without a fruit or with an invalid one
            // Redirect to the homepage in this case
            return $this->redirect('/');
        }

        $fruit_query = $user_query . " " . $fruit_emoji;
        $page_str = strval($page);
        $response = Utils::makeRequest("GET", "/database/search", [
            "q" => $fruit_query,
            "type" => "release",
            "page" => $page_str,
            "per_page" => "15",
        ]);

        $results = $response["results"];
        SearchController::addFruitToAlbums($results, $fruit, $entityManager);
        $user = $this->getUser();
        $array_id = array();
        foreach($user->getLiked() as $like){
            array_push(
                $array_id,
                $like->getAlbumId()
            );
        }
        $true_results = array();
        foreach($results as $result){

            $isLiked = in_array($result["id"],$array_id);
            
            $result = array_merge(
                            $result,
                            ["isLiked" => $isLiked? "true": "false"]
            );
            array_push($true_results,$result);
        }
        
        $results = $true_results;
        
        return $this->render('search/search.html.twig', [
            'controller_name' => 'SearchController',
            'query' => $user_query,
            'fruit_emoji' => $fruit_emoji,
            'fruit_name' => SearchController::getEmojiName($fruit),
            'page' => $page,
            "next_page" => $page < $response["pagination"]["pages"] ? strval($page + 1) : $page_str,
            "previous_page" => $page > 1 ? strval($page - 1) : $page_str,
            'results' => $results
        ]);
    }
}
