<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\ProductLine;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/panier/ajout/{product}', name: 'panier_ajout')]
    public function updateProduct(Product $product, SessionInterface $session, Request $request): Response
    {
        //récupération de la quantité
        $quantity = $request->query->get("quantity",1);
        //récupération du panier
        $cart = $session->get('cart', []);
        //récupération de l'ID du produit à ajouter
        $id = $product->getId();

        $cart[$id] = (int)$quantity;
        $session->set("cart", $cart);
        //récupérer la route d'où l'on vient
        $previousUrl = $request->headers->get('referer');
        return $this->redirectToRoute($previousUrl);
    }

    #[Route('/panier', name: 'panier')]
    public function panier(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart',[]);

        $allProducts = [];
        $totalPrice = 0;
        $totalQuantity= 0;
        foreach ($cart as $id =>$qty){
            $product = $productRepository->find($id);
            $product->setQuantity($qty);
            $totalPrice += $product->getPrice()*$qty;
            $totalQuantity += $qty;
            $allProducts[] = $product;
        }

        return $this->render('order/panier.html.twig', [
            'products' => $allProducts,
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity
        ]);
    }

    #[Route('/qtyPanier', name: 'qtyPanier')]
    public function qtyPanier(SessionInterface $session): Response
    {
        $cart = $session->get('cart',[]);
        $totalQuantity=0;

        foreach ($cart as $id => $qty) {
            $totalQuantity += $qty;
        }
        return new Response($totalQuantity);
    }

    #[Route('/panier/remove/{product}', name: 'panier_remove')]
    public function removeProductOfCart(Product $product, SessionInterface $session): Response
    {
        //récupération du panier
        $cart = $session->get('cart', []);
        //récupération de l'ID du produit à ajouter
        $idProduct = $product->getId();

        if(array_key_exists($idProduct,$cart)) {
            unset($cart[$idProduct]);
        }

        $session->set("cart", $cart);

        return $this->redirectToRoute('panier');

    }

    #[Route('/panier/info', name: 'panier_info')]
    public function infos(Request $request,
                          EntityManagerInterface $entityManager,
                          SessionInterface $session,
                          ProductRepository $productRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //order
            $date = new \DateTime();
            $order->setCreatedAt($date);
            $dateDelivery = clone $date;
            $interval = (new DateInterval('PT1H'));
            $dateDelivery->add($interval);
            $order->setDeliveryTime($dateDelivery);
            $order->setInProgress(false);
            //mise en BDD
            $entityManager->persist($order);


            //récupération du panier
            $cart = $session->get('cart', []);
            $totalPrice = 0;

            foreach ($cart as $id =>$qty) {
                $product = $productRepository->find($id);
                $productLine = new ProductLine();
                $productLine->setIdProduct($product);
                $productLine->setQtyProduct($qty);
                $price = $qty*$product->getPrice();
                $productLine->setTotal($price);
                $totalPrice += $price;
                $productLine->setCommandNumber($order);

                $entityManager->persist($productLine);
                $order->addProductLine($productLine);
            }
            $order->setTotal($totalPrice);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('recap', ['id' => $order->getId()]);

        }

        return $this->renderForm('order/info.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/panier/recap/{id}', name: 'recap')]
    public function recap(Order $order): Response
    {
        return $this->render('order/recap.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/panier/paiement/{order}', name: 'paiement')]
    public function redirectStripe(Order $order,SessionInterface $sessionActive, ProductRepository $productRepository): Response
    {
        //création d'un token pour la validation
        $token=bin2hex(random_bytes(10));
        $sessionActive->set("token_payment",$token);

        $cart = $sessionActive->get('cart', []);
        $stripeOrder= [];
        foreach ($cart as $id =>$qty) {
            $product = $productRepository->find($id);
            $stripeOrder[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getName(),
                        ],
                        'unit_amount' => $product->getPrice(),
                    ],
                    'quantity' => $qty,
            ];
        }

        \Stripe\Stripe::setApiKey('sk_test_51Kc4NfIH1eZO47FF7kFbnj6AGBg25Re20NIfWtWcR0Li2ND8ZEScLMU2SkkclW5fLtC3pxqoLeqbQvpbIl9dWBa600Xbd3QWCB');
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [$stripeOrder],
            'mode' => 'payment',
            'success_url' => 'http://127.0.0.1:8000/panier/success/'.$order->getId().'/'.$token,
            'cancel_url' => 'http://127.0.0.1:8000/panier/error'
        ]);

        return $this->redirect($session->url,303);
    }

    #[Route('/panier/success/{order}/{token}', name: 'success')]
    public function success(Order $order, string $token, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        //vérification du token
        $tokenSession = $session->get('token_payment');

        if($token == $tokenSession) {

            $order->setInProgress(1);
            $entityManager->flush();

            $session->set("cart",[]);
            return $this->render('order/success.html.twig');
        }

        return $this->redirectToRoute('error');

    }

    #[Route('/panier/error', name: 'error')]
    public function error(): Response
    {
        return $this->redirectToRoute('home');
    }
}
