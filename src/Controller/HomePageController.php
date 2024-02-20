<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $data = $productRepository->findAll();

        $products  = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            9
        );

        

        return $this->render('home_page/index.html.twig', [
            'products' => $products,
        ]);
    }
}
