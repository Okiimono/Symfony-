<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/boutique')]
final class BoutiqueController extends AbstractController
{
    #[Route('', name: 'app_boutique_index')]
    public function index(): Response
    {
        return $this->render('boutique/index.html.twig');
    }

    #[Route('/rayon/{idCategorie}', name: 'app_boutique_rayon')]
    public function rayon(int $idCategorie): Response
    {
        return $this->render('boutique/rayon.html.twig', [
            'categorie' => $idCategorie,
        ]);
    }
}
