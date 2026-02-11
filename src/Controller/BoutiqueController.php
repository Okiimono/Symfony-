<?php

namespace App\Controller;

use App\Service\BoutiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/boutique')]
final class BoutiqueController extends AbstractController
{
    #[Route('', name: 'app_boutique_index')]
    public function index(BoutiqueService $boutique): Response
    {
        $lesCategories = $boutique->findAllCategories();

        return $this->render('boutique/index.html.twig', [
            'categories' => $lesCategories,
        ]);
    }

    #[Route('/rayon/{idCategorie}', name: 'app_boutique_rayon')]
    public function rayon(BoutiqueService $boutique, int $idCategorie): Response
    {
        $produits = $boutique->findProduitsByCategorie($idCategorie);

        $categorie = $boutique->findCategorieById($idCategorie);

        return $this->render('boutique/rayon.html.twig', [
            'produits' => $produits,
            'categorie' => $categorie
        ]);
    }
}
