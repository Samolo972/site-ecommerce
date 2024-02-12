<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\RecapDetails;
use App\Entity\User;
use App\Form\AdressesType;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\RecapDetailsRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/order/create', name: 'order_create')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $cartService->getTotal()
        ]);
    }

    #[Route('/order/verify', name: 'order_verify', methods: ['POST'])]
    public function prepareOrder(CartService $cartService, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new \DateTimeImmutable('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('address')->getData();
            $reference = $datetime->format('dmY') . '-' . uniqid();
            $deliveryForOrder = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $deliveryForOrder .= '</br>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryForOrder .= ' - ' . $delivery->getCompany();
            }
            $deliveryForOrder .= '</br>' . $delivery->getAddress();
            $deliveryForOrder .= '</br>' . $delivery->getPostalCode() . ' - ' . $delivery->getCity();
            $deliveryForOrder .= '</br>' . $delivery->getCountry();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($datetime);
            $order->setReference($reference);
            $order->setTransporterName($transporter);
            $order->setDelivery($delivery->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setIsPaid(0);
            $order->setDelivery($deliveryForOrder);
            $order->setMethod('stripe');


            $this->em->persist($order);

            foreach ($cartService->getTotal() as $product) {
                $recapDetails = new RecapDetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getPrice());
                $recapDetails->setProduct($product['product']->getName());
                $recapDetails->setTotalRecap(
                    $product['product']->getPrice() * $product['quantity']
                );

                $this->em->persist($recapDetails);
            }

            $this->em->flush();
            return $this->render('order/recap.html.twig', [
                'method' => $order->getMethod(),
                'recapCart' => $cartService->getTotal(),
                'transporter' => $transporter,
                'delivery' => $deliveryForOrder,
                'reference' => $order->getReference()
            ]);
        }


        return $this->redirectToRoute('cart_index');
    }


    #[Route('/order/my-order', name: 'order_view', methods: ['GET'])]
    public function OrderView(
        OrderRepository $order,
        UserRepository $user,
        RecapDetailsRepository $recapDetailsRepository
    ): Response {
        if (!$this->getUser()) {
            $this->redirectToRoute('app_login');
        }
        if ($this->getUser() != $user) {
            $this->redirectToRoute('app_login');
        }

        $orderUser = $order->findByUser($this->getUser());





        return $this->render('order/view.html.twig', [
            'order' => $orderUser,
        ]);
    }


    #[Route('/add/address', name: 'add_address')]
    public function AddressAdd(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $address = new Address();
        $address->setUser($user); // Associez directement l'utilisateur à l'adresse ici

        $form = $this->createForm(AdressesType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($address);
            $this->em->flush();

            $this->addFlash('success', "Votre adresse de livraison a été bien ajoutée.");

            return $this->redirectToRoute('order_create');
        }

        return $this->render('order/addressAdd.html.twig', [
            'address' => $form->createView(),
        ]);
    }
}
