<?php
namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Service\BoutiqueService;

// Service pour manipuler le panier et le stocker en session
class PanierService
{
    ////////////////////////////////////////////////////////////////////////////
    private $session;   // Le service session
    private $produitRepository;  // Le service boutique
    private $panier;    // Tableau associatif, la clé est un idProduit, la valeur associée est une quantité
                        //   donc $this->panier[$idProduit] = quantité du produit dont l'id = $idProduit
    const PANIER_SESSION = 'panier'; // Le nom de la variable de session pour faire persister $this->panier

    // Constructeur du service
    public function __construct(RequestStack $requestStack, ProduitRepository $produitRepository)
    {
        // Récupération des services session et BoutiqueService
        $this->produitRepository = $produitRepository;
        $this->session = $requestStack->getSession();
        // Récupération du panier en session s'il existe, init. à vide sinon
        $this->panier = $this->session->get(self::PANIER_SESSION, []);
    }

    // Renvoie le montant total du panier
    public function getTotal() : float
    {
        $total = 0;
        foreach ($this->panier as $idProduit => $quantite) {
            $produit = $this->produitRepository->find($idProduit);
            if ($produit != null) {
                $total += ($produit->getPrix() * $quantite);
            }
        }
        return $total;
    }

    // Renvoie le nombre de produits dans le panier
    public function getNombreProduits() : int
    {
      $nbProduits = 0;
        foreach ($this->panier as $idProduit => $quantite) {
            $nbProduits += $quantite;
        }
      return $nbProduits;
    }

    // Ajouter au panier le produit $idProduit en quantite $quantite
    public function ajouterProduit(int $idProduit, int $quantite = 1) : void
    {
        if (isset($this->panier[$idProduit])) {
            $this->panier[$idProduit] += $quantite;
        } else {
            $this->panier[$idProduit] = $quantite;
        }
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Enlever du panier le produit $idProduit en quantite $quantite
    public function enleverProduit(int $idProduit, int $quantite = 1) : void
    {
        if (isset($this->panier[$idProduit])) {
            if ($this->panier[$idProduit] > $quantite) {
                $this->panier[$idProduit] = $this->panier[$idProduit] - $quantite;
            }else{
                unset($this->panier[$idProduit]);
            }
            $this->session->set(self::PANIER_SESSION, $this->panier);
        }
    }

    // Supprimer le produit $idProduit du panier
    public function supprimerProduit(int $idProduit) : void
    {
      unset($this->panier[$idProduit]);
      $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Vider complètement le panier
    public function vider() : void
    {
        $this->panier = [];
        $this->session->set(self::PANIER_SESSION, $this->panier);
    }

    // Renvoie le contenu du panier
    public function getContenu() : array
    {
      $contenu = [];

      foreach ($this->panier as $idProduit => $quantite) {
          $produit = $this->produitRepository->find($idProduit);
          if ($produit != null) {
              $contenu[] = [
                  "produit" => $produit,
                  "quantite" => $quantite
              ];
          }
      }
      return $contenu;
    }

}
