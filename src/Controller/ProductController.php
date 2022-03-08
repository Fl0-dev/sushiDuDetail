<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ProductRepository $productRepository): Response
    {
        $allProducts = $productRepository->findAll();

        return $this->render('product/home.html.twig', [
            'products' => $allProducts,
        ]);
    }
}
