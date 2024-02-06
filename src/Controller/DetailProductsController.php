<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailProductsController extends AbstractController
{
    // AFFICHER LES DETAILS DES HOTELS SELON ID 
    #[Route('/product/{id}', name: 'products.detail', methods: ['GET', 'POST'])]
    public function detail(
        $id,
        ProductRepository $repo,
    ): Response {
        // Utilisez la méthode find pour récupérer un élément par son ID
        $detail = $repo->find($id);

        if (!$detail) {
            // Gérez le cas où aucun hôtel n'est trouvé pour l'ID spécifié, par exemple, redirigez l'utilisateur ou affichez un message d'erreur.
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'ID ' . $id);
        }

        return $this->render('detail_products/index.html.twig', [
            'detail' => $detail
        ]);
    }
}
