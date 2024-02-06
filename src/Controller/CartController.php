<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {

        $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal()
        ]);
    }


    #[Route('/mon-panier/ajouter/{id<\d+>}', name: 'cart_add')]
    public function addToCart(CartService $cartService, int $id): Response
    {   
        if($this->getUser()){
        $cartService->addToCart($id);
        return $this->redirectToRoute('cart_index');
    }else{
        return $this->redirectToRoute('app_login');
    }
       
    }

    #[Route('/mon-panier/supprimerTout', name: 'cart_removeAll')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();
        
        return $this->redirectToRoute('cart_index');
    }


    #[Route('/mon-panier/supprimer/{id<\d+>}', name: 'cart_remove')]
    public function removeToCart(CartService $cartService ,int $id): Response
    {
        $cartService->removeToCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/mon-panier/decrease/{id<\d+>}', name: 'decrease_cart')]
    public function decreaseToCart(CartService $cartService ,int $id): RedirectResponse
    {
        $cartService->decreaseToCart($id);
        return $this->redirectToRoute('cart_index');
    }

}
