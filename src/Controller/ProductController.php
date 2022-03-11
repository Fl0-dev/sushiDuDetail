<?php

namespace App\Controller;

use App\Entity\SearchData;
use App\Form\SearchForm;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository ,SessionInterface $session, Request $request): Response
    {
        $allProducts = $productRepository->findAll();
        $cart = $session->get("cart",[]);
        foreach ($allProducts as $product) {
            $idProduct = $product->getId();
            if(array_key_exists($idProduct,$cart)) {
                $product->setQuantity($cart[$idProduct]);
            }
        }

        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $data->setMin(1);
        $data->setMax(15000);
        $data->setCategories(new ArrayCollection());

        if ($form->isSubmitted() && $form->isValid()) {

            //récupération des produits selon les data
            $allProducts = $productRepository->findBySearch($data);
            //dd($allProducts);
        }

        return $this->render('product/home.html.twig', [
            'products' => $allProducts,
            'form' => $form->createView()
        ]);
    }

//    #[Route('/search', name: 'search')]
//    public function search(ProductRepository $productRepository, Request $request, SessionInterface $session): Response
//    {
//        //traitement sans formulaire
//        $allProducts = $productRepository->findAll();
//        $cart = $session->get("cart",[]);
//        foreach ($allProducts as $product) {
//            $idProduct = $product->getId();
//            if(array_key_exists($idProduct,$cart)) {
//                $product->setQuantity($cart[$idProduct]);
//            }
//        }
//
//        $data = new SearchData();
//        $form = $this->createForm(SearchForm::class, $data);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            //récupération des produits selon les data
//            $allProducts = $productRepository->findBySearch($data);
//        }
//
//        return $this->render('product/home.html.twig', [
//            'products' => $allProducts,
//            'form' => $form->createView()
//        ]);
//
//    }
}
