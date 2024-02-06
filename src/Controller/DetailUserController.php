<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailUserController extends AbstractController
{
    #[Route('/detail/user', name: 'app_detail_user')]
    public function index(): Response
    {
        return $this->render('detail_user/index.html.twig', [
        ]);
    }
}
