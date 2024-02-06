<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\RecapDetails;
use App\Form\OrderType;
use App\Service\CartService;
use DateTime;
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
                'reference'=> $order->getReference()
            ]);
        }


        return $this->redirectToRoute('cart_index');
    }
    
}
