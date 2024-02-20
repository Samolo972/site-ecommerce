<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailProductsController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }



    #[Route('/product/{id}', name: 'products.detail', methods: ['GET'])]
    public function detail(
        $id,
        ProductRepository $repo,
        Request $request,
        CommentRepository $commentRepository,
        PaginatorInterface $paginator,

    ): Response {
        // Utilisez la méthode find pour récupérer un élément par son ID
        $detail = $repo->find($id);

        if (!$detail) {
            // Gérez le cas où aucun hôtel n'est trouvé pour l'ID spécifié, par exemple, redirigez l'utilisateur ou affichez un message d'erreur.
            throw $this->createNotFoundException('Aucun produit trouvé pour l\'ID ' . $id);
        }
        // Récupérez les commentaires associés au produit
        $commentsQuery = $commentRepository->findBy(['product' => $detail]);

        // Paginez les commentaires
        $pagination = $paginator->paginate(
            $commentsQuery,
            $request->query->getInt('page', 1), // Numéro de la page
            3 // Nombre d'éléments par page
        );

        return $this->render('detail_products/index.html.twig', [
            'detail' => $detail,
            'pagination' => $pagination
        ]);
    }



    #[Route('/product/commentaire/{id}', name: 'products_comment')]
    public function addComment(
        int $id,
        Product $product,
        Request $request,
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        $comment = new Comment();
        $comment->setUser($this->getUser());


        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {


            $comment->setProduct($product); // Lier le commentaire au produit
            $comment->setUser($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès.');

            return $this->redirectToRoute(
                'products.detail',
                ['id' => $product->getId()]
            );
        }



        return $this->render('comment/index.html.twig', [
            'form' => $form,
        ]);
    }
}
