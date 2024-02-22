<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class FavoriteController extends AbstractController
{

    #[Route('/favorites/remove/{album_id}', name: 'app_favorite')]
    public function removeFromFavorites(): Response
    {
        return $this->render('favorite/index.html.twig', [
            'controller_name' => 'FavoriteController',
        ]);
    }
    
    #[Route('/favorites/add/{album_id}', name: 'app_favorite')]
    public function addToFavorites(): Response
    {
        return $this->render('favorite/index.html.twig', [
            'controller_name' => 'FavoriteController',
        ]);
    }
    
    #[Route('/favorites', name: 'app_favorite')]
    public function index(Request $request): Response
    {
        return $this->render('favorite/index.html.twig', [
            'controller_name' => 'FavoriteController',
        ]);
    }
}
