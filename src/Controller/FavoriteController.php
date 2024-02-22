<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Album;
use App\Utils;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{

    #[Route('/remove/{album_id}', name: 'app_remove_favorite')]
    public function removeFromFavorites(int $album_id,EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $album = $entityManager->getRepository(Album::class)->findBy(
            ['album_id' => $album_id]
            //['album_id' => $request->query->get('album_id')]
        );

        if (count($album) == 0) {
            return new Response("ca pas a marche");
        }

        $user->removeLiked($album[0]);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("ca a marche");
    }

    #[Route('/add/{album_id}', name: 'app_add_favorite')]
    public function addToFavorites(int $album_id, Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();
        $album = $entityManager->getRepository(Album::class)->findBy(
            ['album_id' => $album_id]
            //['album_id' => $request->query->get('album_id')]
        );

        if (count($album) == 0) {
            return new Response("ca pas a marche");
        }
        $user->addLiked($album[0]);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("ca a marche");
    }

    #[Route('/', name: 'app_favorite')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $all_liked = array();
        foreach ($user->getLiked() as $liked) {
            $release = Utils::makeRequest("GET", "/releases/" . strval($liked->getAlbumId()), []);
            $release = array_merge(
                $release,
                ["fruit" => $liked->getFruits()]
            );
            array_push(
                $all_liked,
                $release,
            );
        }

        return $this->render('favorite/index.html.twig', [
            'controller_name' => 'FavoriteController',
            'favorites' => $all_liked
        ]);
    }
}
