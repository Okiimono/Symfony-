<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use App\Service\BoutiqueService;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/boutique')]
final class BoutiqueController extends AbstractController
{
    #[Route('', name: 'app_boutique_index')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $lesCategories = $categorieRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'categories' => $lesCategories,
        ]);
    }

    #[Route('/rayon/{idCategorie}', name: 'app_boutique_rayon')]
    public function rayon(CategorieRepository $categorieRepository, int $idCategorie, ProduitRepository $produitRepository): Response
    {
        $categorie = $categorieRepository->find($idCategorie);

        $produits = $categorieRepository->findBy(['categorie' =>$categorie]);

        return $this->render('boutique/rayon.html.twig', [
            'produits' => $produits,
            'categorie' => $categorie
        ]);
    }

    #[Route('/chercher/{recherche}', name: 'app_boutique_chercher', requirements: ['recherche' => '.+'], defaults: ['recherche' => ''])]
    public function chercher(ProduitRepository $produitRepository, string $recherche, Request $request): Response
    {
        if ($recherche === '') {
            $produits = [];
        } else {
            $produits = $produitRepository->findByLibelleOrTexte($recherche);
        }

    return $this->render('boutique/chercher.html.twig', [
        'articles' => $produits,
        'recherche' => $recherche
    ]);
    }

}
