<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;
    private CartService $cartService;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator , CartService $cartService)
    {
        $this->em = $em;
        $this->generator = $generator;
        $this->cartService = $cartService;
    }


    #[Route('/order/create-session-stripe/{reference}', name: 'payment_stripe')]
    public function stripeCheckout($reference): RedirectResponse
    {
        $productStripe = [];
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            return $this->redirectToRoute('cart_index');
        }

        foreach ($order->getRecapDetails()->getValues() as $product) {
            $productData = $this->em->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);

            $productStripe[] = [
                'price_data' => [
                    'currency'  => 'eur',
                    'unit_amount_decimal'  => $productData->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct()
                    ],
                ],
                'quantity' => $product->getQuantity()
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency'  => 'eur',
                'unit_amount_decimal'  => $order->getTransporterPrice(),
                'product_data' => [
                    'name' => $order->getTransporterName()
                ],

            ],
            'quantity' => 1
        ];



        Stripe::setApiKey('sk_test_51OgCoABc572nPI6vE5iZSe4qQdfBA3xYaB4z04jI3x9ofqIKQfK415cjYinsf7J1hyOPT9T0pR0xC71gevbuqhfB00giCIjhWf');
        // // header('Content-Type: application/json');

        // // $YOUR_DOMAIN = 'http://localhost:4242';

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $productStripe,

            ]],


            'mode' => 'payment',
            'success_url' => $this->generator->generate('payment_success', [
                'reference' => $order->getReference(),

            ], UrlGeneratorInterface::ABSOLUTE_URL),


            'cancel_url' => $this->generator->generate('payment_error', [
                'reference' => $order->getReference(),

            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $order->setStripeSessionId($checkout_session->id);
        
        if($checkout_session['success_url']){
            $order->setIsPaid(1); 
        }
    

        $this->em->flush();

         $this->cartService->removeCartAll();

        return new RedirectResponse($checkout_session->url);
    }


    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSucess(Order $order): Response
    {   
        return $this->render('order/success.html.twig', []);

    }

    #[Route('/order/error/{reference}', name: 'payment_error')]
    public function stripeError(): Response
    {
        return $this->render('order/error.html.twig', []);
    }
}
