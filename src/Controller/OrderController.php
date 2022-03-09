<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/panier/ajout/{product}', name: 'panier_ajout')]
    public function AddProduct(Product $product, SessionInterface $session, Request $request): Response
    {

        //$qty = $request->get("quantity",1);
        $cart = $session->get('cart', []);

        $cart[] = $product->getId();

        $session->set("cart", $cart);

        return $this->redirectToRoute('home');
    }

    #[Route('/panier', name: 'panier')]
    public function panier(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart');
        $allProducts = [];
        $totalPrice = 0;
        foreach ($cart as $id){
            $product = $productRepository->find($id);
            $totalPrice += $product->getPrice();
            $allProducts[] = $product;
        }

        return $this->render('order/panier.html.twig', [
            'products' => $allProducts,
            'totalPrice' => $totalPrice,
        ]);
    }
}
