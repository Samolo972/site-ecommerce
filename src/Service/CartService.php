<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $manager;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $manager)
    {
        $this->requestStack = $requestStack;
        $this->manager = $manager;
    }


    public function addToCart(int $id)
    {
        $cart = $this->getSession()->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->getSession()->set('cart', $cart);
    }


    public function getTotal() : array
    {
        $cart = $this->getSession()->get('cart');

        $cartData = [];

        if($cart){
        foreach ($cart as $id => $quantity) {
            $product = $this->manager->getRepository(Product::class)->findOneBy(['id'=>$id]);
            if(!$product){
                $this->removeToCart($id);
                continue;

            }
            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity,

            ];
        }
    }
        return $cartData;
    }



    public function removeCartAll()
    {
         return $this->getSession()->remove('cart');
    }


    public function removeToCart(int $id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        unset($cart[$id]);

        return $this->getSession()->set('cart', $cart);

    }


    public function decreaseToCart(int $id)
    {
        $cart = $this->getSession()->get('cart', []);
        if($cart[$id] > 1){
            $cart[$id]--;
        }
        else{
            unset($cart[$id]);
        }

        return $this->getSession()->set('cart', $cart);

    }


    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
