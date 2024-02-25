<?php

namespace App\Controller;

use App\Utils;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{

    private static function addFruitToAlbums(array $releases, string $fruit, EntityManagerInterface $entityManager): void
    {
        $albums = $entityManager->getRepository(Album::class)->findAll();
        foreach ($releases as $release) {
            $album = null;
            $id = $release["id"];
            foreach($albums as $remote_album){
                if ($remote_album->getAlbumId() == $id)
                    $album == $remote_album;
                break;
            }
            if ($album == null) {
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
        }
        $entityManager->flush();
    }

    private static function getEmojiName(string | null $emoji): string | null
    {
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
        if ($page == null) $page = 1;
        $fruit = $request->query->get('fruit');
        $user_query = $request->query->get('q');

        $fruit_name = SearchController::getEmojiName($fruit);
        if ($fruit_name == null) {
            // We do not support requests without a fruit or with an invalid one
            // Redirect to the homepage in this case
            return $this->redirect('/');
        }

        $fruit_query = $user_query . " " . $fruit_name;
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
        foreach ($user->getLiked() as $like) {
            array_push(
                $array_id,
                $like->getAlbumId()
            );
        }
        $true_results = array();
        foreach ($results as $result) {

            $isLiked = in_array($result["id"], $array_id);

            $result = array_merge(
                $result,
                ["isLiked" => $isLiked ? "true" : "false"]
            );
            array_push($true_results, $result);
        }

        $results = $true_results;

        return $this->render('search/search.html.twig', [
            'controller_name' => 'SearchController',
            'query' => $user_query,
            'fruit_emoji' => $fruit,
            'fruit_name' => SearchController::getEmojiName($fruit),
            'page' => $page,
            'all_page' => $response["pagination"]["pages"],
            "next_page" => $page < $response["pagination"]["pages"] ? strval($page + 1) : $page_str,
            "previous_page" => $page > 1 ? strval($page - 1) : $page_str,
            'results' => $results
        ]);
    }
}
