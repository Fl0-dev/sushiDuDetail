<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ProductRepository $productRepository, SessionInterface $session): Response
    {
        $allProducts = $productRepository->findAll();
        $cart = $session->get("cart",[]);

        foreach ($allProducts as $product) {
            $idProduct = $product->getId();
            if(array_key_exists($idProduct,$cart)) {
                $product->setQuantity($cart[$idProduct]);
            }
        }

        return $this->render('product/home.html.twig', [
            'products' => $allProducts,
        ]);
    }
}
