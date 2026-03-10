<?php

namespace App\Controller;

use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale}/panier')]
final class PanierController extends AbstractController
{
    #[Route('/', name: 'app_panier_index')]
    public function index(PanierService $panier): Response
    {
        $contenue = $panier->getContenu();
        $total = $panier->getTotal();

        return $this->render('panier/index.html.twig', [
            'contenue'  => $contenue,
            'total'     => $total,
        ]);
    }

    #[Route('/ajouter/{idProduit}/{quantite}', name: 'app_panier_ajouter', requirements: ['idProduit' => '\d+', 'quantite' => '\d+'])]
    public function ajouter(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        $panier->ajouterProduit($idProduit, $quantite);

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/enlever/{idProduit}/{quantite}', name: 'app_panier_enlever', requirements: ['idProduit' => '\d+', 'quantite' => '\d+'])]
    public function enlever(PanierService $panier, int $idProduit, int $quantite = 1): Response
    {
        $panier->enleverProduit($idProduit, $quantite);
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/supprimer/{idProduit}', name: 'app_panier_supprimer', requirements: ['idProduit' => '\d+'])]
    public function supprimer(PanierService $panier, int $idProduit): Response
    {
        $panier->supprimerProduit($idProduit);
        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'app_panier_vider')]
    public function vider(PanierService $panier): Response
    {
        $panier->vider();
        return $this->redirectToRoute('app_panier_index');
    }

    public function nombreProduits(PanierService $panier): Response
    {
        $nb = $panier->getNombreProduits();
        return new Response((string) $nb);
    }

    #[Route('/commander', name: 'app_panier_commander')]
    public function commander(PanierService $panier, UsagerRepository $usagerRepository): Response
    {
        $usager = $usagerRepository->find(1);
        if (!$usager) {
            return $this->redirectToRoute('app_usager_new');
        }

        $commande = $panier->panierToCommande($usager);

        if (!$commande) {
            return $this->redirectToRoute('app_panier_index');
        }

        return $this->render('panier/commande.html.twig', [
            'commande' => $commande,
            'usager' => $usager,
        ]);
    }
}
