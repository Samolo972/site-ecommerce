<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();


        
        return $this->render('home_page/index.html.twig', [
            'products'=> $products,
        ]);
    }
}
